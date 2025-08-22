<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBoxRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

   public function rules()
   {
       return [
           'height' => 'required|integer|min:1',
           'width'  => 'required|integer|min:1',
           'color'  => 'required|string|max:50',
       ];
   }

}
