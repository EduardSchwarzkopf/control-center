<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $isAdmin = $user->is_admin;
        return $isAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this->request;
        $type = $request->get('type');

        if ($type == null) {
            $type = 'string';
        }

        $rules = Setting::VALIDATION_RULES;

        $rules['value'] = ['required', $type];

        return $rules;
    }
}
