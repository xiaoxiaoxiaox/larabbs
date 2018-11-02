<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest as Base;

class FormRequest extends Base
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
