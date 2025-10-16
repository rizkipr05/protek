<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DivisionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('division'); // route model binding key

        return [
            'name'        => [
                'sometimes','string','max:100',
                Rule::unique('divisions','name')->ignore($id, 'id')
            ],
            'description' => 'sometimes|nullable|string|max:1000',
            'is_active'   => 'sometimes|boolean',
        ];
    }
}
