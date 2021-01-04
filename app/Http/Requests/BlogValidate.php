<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogValidate extends FormRequest
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

    /**
     *
     *
     * @return array
     */
    public function rules()
    {
        return [
            //Get the validation rules that apply to the request.
            'title' => 'bail|required',
            'content' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title' => '没有title啊'
        ];
    }
}
