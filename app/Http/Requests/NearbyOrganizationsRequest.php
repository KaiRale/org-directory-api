<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NearbyOrganizationsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
            'radius' => 'nullable|numeric|min:1|max:10000',
        ];
    }
}
