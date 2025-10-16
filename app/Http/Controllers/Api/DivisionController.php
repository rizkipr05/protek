<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DivisionStoreRequest;
use App\Http\Requests\DivisionUpdateRequest;
use App\Http\Resources\DivisionResource;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        // Query params: q (search), is_active (true/false), sort, page, perPage
        $q        = $request->string('q')->toString();
        $isActive = $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
        $sort     = $request->query('sort', '-created_at'); // e.g. name or -name
        $perPage  = (int) $request->query('perPage', 15);

        $query = Division::query();

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('name','like',"%{$q}%")
                    ->orWhere('description','like',"%{$q}%");
            });
        }

        if (!is_null($isActive)) {
            $query->where('is_active', $isActive);
        }

        // Sorting: prefix "-" berarti desc
        if (str_starts_with($sort, '-')) {
            $query->orderBy(ltrim($sort,'-'), 'desc');
        } else {
            $query->orderBy($sort, 'asc');
        }

        $divisions = $query->paginate($perPage)->appends($request->query());

        return DivisionResource::collection($divisions)
            ->additional(['status' => 'success']);
    }

    public function store(DivisionStoreRequest $request)
    {
        $division = Division::create($request->validated());

        return (new DivisionResource($division))
            ->additional(['status' => 'success'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Division $division)
    {
        return (new DivisionResource($division))
            ->additional(['status' => 'success']);
    }

    public function update(DivisionUpdateRequest $request, Division $division)
    {
        $division->update($request->validated());

        return (new DivisionResource($division))
            ->additional(['status' => 'success']);
    }

    public function destroy(Division $division)
    {
        $division->delete();

        return response()->json(['status' => 'success'], 204);
    }
}
