<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DivisionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Atur sesuai kebutuhan (policy/role). Untuk cepat: true lalu batasi di route middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:100|unique:divisions,name',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|boolean',
        ];
    }
}
