<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /* GET /api/users (admin) */
    public function index(Request $r)
    {
        $this->ensureAdmin($r);

        $q = User::query()
            ->with('division:id,name,code')
            ->when($r->filled('q'), function($qq) use($r){
                $search = trim($r->q);
                $qq->where(function($w) use($search){
                    $w->where('name','like',"%$search%")
                      ->orWhere('email','like',"%$search%")
                      ->orWhere('username','like',"%$search%");
                });
            })
            ->when($r->filled('role'), fn($qq)=>$qq->where('role',$r->role))
            ->when($r->filled('division'), fn($qq)=>$qq->where('division_id',$r->division))
            ->when($r->filled('is_active'), fn($qq)=>$qq->where('is_active', (int)$r->is_active === 1));

        $perPage = (int)($r->per_page ?? 15);
        $rows = $q->orderByDesc('id')->paginate($perPage)->appends($r->query());

        return response()->json([
            'data' => $rows->items(),
            'meta' => [
                'current_page'=>$rows->currentPage(),
                'per_page'=>$rows->perPage(),
                'total'=>$rows->total(),
                'last_page'=>$rows->lastPage(),
            ],
        ]);
    }

    /* POST /api/users (admin) */
    public function store(Request $r)
    {
        $this->ensureAdmin($r);

        $data = $r->validate([
            'name'       => ['required','string','max:100'],
            'username'   => ['required','string','max:50','unique:users,username'],
            'email'      => ['required','email','unique:users,email'],
            'password'   => ['required','string','min:6'],
            'role'       => ['required', Rule::in(['admin','user','staff_divisi'])],
            'division_id'=> ['nullable','exists:divisions,id'],
            'is_active'  => ['nullable','boolean'],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'is_active'=> $data['is_active'] ?? true,
        ]);

        return response()->json($this->present($user), 201);
    }

    /* GET /api/users/{id} (admin) */
    public function show(Request $r, User $user)
    {
        $this->ensureAdmin($r);
        return $this->present($user->load('division:id,name,code'));
    }

    /* PUT /api/users/{id} (admin) */
    public function update(Request $r, User $user)
    {
        $this->ensureAdmin($r);

        $data = $r->validate([
            'name'       => ['sometimes','string','max:100'],
            'username'   => ['sometimes','string','max:50', Rule::unique('users','username')->ignore($user->id)],
            'email'      => ['sometimes','email', Rule::unique('users','email')->ignore($user->id)],
            'role'       => ['sometimes', Rule::in(['admin','user','staff_divisi'])],
            'division_id'=> ['sometimes','nullable','exists:divisions,id'],
            'is_active'  => ['sometimes','boolean'],
        ]);

        // Jangan biarkan admin terakhir di-downgrade/nonaktif/hapus
        if (isset($data['role']) || array_key_exists('is_active',$data)) {
            if ($this->isLastAdmin($user) && (($data['role'] ?? $user->role) !== 'admin' || ($data['is_active'] ?? $user->is_active) === false)) {
                return response()->json(['message'=>'Cannot modify: this is the last active admin'], 422);
            }
        }

        $user->update($data);
        return $this->present($user->fresh('division'));
    }

    /* DELETE /api/users/{id} (admin) */
    public function destroy(Request $r, User $user)
    {
        $this->ensureAdmin($r);

        if ($r->user()->id === $user->id) {
            return response()->json(['message'=>'Cannot delete your own account'], 422);
        }
        if ($this->isLastAdmin($user)) {
            return response()->json(['message'=>'Cannot delete the last active admin'], 422);
        }

        $user->delete();
        return response()->json(['message'=>'Deleted']);
    }

    /* PATCH /api/users/{id}/change-password (admin|owner) */
    public function changePassword(Request $r, User $user)
    {
        $auth = $r->user();
        if (! ($auth->role === 'admin' || $auth->id === $user->id)) {
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $data = $r->validate([
            'current_password' => [$auth->role === 'admin' ? 'nullable' : 'required','string'],
            'new_password'     => ['required','string','min:6'],
        ]);

        // Owner harus verifikasi password lama. Admin bebas.
        if ($auth->id === $user->id && !Hash::check($data['current_password'] ?? '', $user->password)) {
            return response()->json(['message'=>'Current password is incorrect'], 422);
        }

        $user->update(['password'=> Hash::make($data['new_password'])]);
        // Opsional: revoke token lama
        // $user->tokens()->delete();

        return response()->json(['message'=>'Password updated']);
    }

    /* PATCH /api/users/{id}/assign-division (admin) */
    public function assignDivision(Request $r, User $user)
    {
        $this->ensureAdmin($r);

        $data = $r->validate([
            'division_id' => ['nullable','exists:divisions,id'],
            'role'        => ['nullable', Rule::in(['user','staff_divisi','admin'])],
        ]);

        // default: kalau set division_id dan role user, naikin ke staff_divisi
        if ($user->role === 'user' && isset($data['division_id']) && !isset($data['role'])) {
            $data['role'] = 'staff_divisi';
        }

        if ($this->isLastAdmin($user) && (($data['role'] ?? $user->role) !== 'admin')) {
            return response()->json(['message'=>'Cannot demote the last active admin'], 422);
        }

        $user->update($data);
        return $this->present($user->fresh('division'));
    }

    /* ---------- helpers ---------- */
    private function ensureAdmin(Request $r): void
    {
        if (!$r->user() || $r->user()->role !== 'admin') {
            abort(response()->json(['message'=>'Forbidden'], 403));
        }
    }

    private function isLastAdmin(User $u): bool
    {
        return $u->role === 'admin'
            && $u->is_active
            && User::where('role','admin')->where('is_active',true)->count() === 1;
    }

    private function present(User $u)
    {
        return [
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
            'role' => $u->role,
            'division' => $u->division ? [
                'id'=>$u->division->id, 'name'=>$u->division->name, 'code'=>$u->division->code
            ] : null,
            'is_active' => (bool)$u->is_active,
            'created_at' => $u->created_at,
        ];
    }
}
