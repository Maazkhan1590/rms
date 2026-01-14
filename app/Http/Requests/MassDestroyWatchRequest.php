<?php

namespace App\Http\Requests;

use App\Models\Watch;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyWatchRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('watch_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:watches,id',
        ];
    }
}
