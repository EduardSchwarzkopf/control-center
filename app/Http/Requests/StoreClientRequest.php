<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = Client::VALIDATION_RULES;

        if ($this->getMethod() == 'PUT') {
            $nullableRules = ['name', 'options', 'options.url'];

            foreach($nullableRules as $rule) {
                $rules[$rule][0] = 'nullable';
            }
        }

        return $rules;
    }
}
