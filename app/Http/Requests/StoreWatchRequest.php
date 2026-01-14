<?php

namespace App\Http\Requests;

use App\Models\Watch;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreWatchRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('watch_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                // 'min:100',
                'max:180',
                'required',
            ],
            'sku_code' => [
                'string',
                // 'min:200',
                'max:255',
                'nullable',
            ],
            'size' => [
                'string',
                // 'min:100',
                'max:150',
                'nullable',
            ],
            'details' => [
                'string',
                // 'min:255',
                'max:255',
                'nullable',
            ],
            'color' => [
                'string',
                // 'min:100',
                'max:150',
                'nullable',
            ],
        ];
    }
}
