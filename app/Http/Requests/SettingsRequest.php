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
        $method = $this->getMethod();
        $rules = Setting::VALIDATION_RULES;

        if ($method == 'POST') {
            $rules = $this->handleCreateSetting($request, $rules);
        }

        if ($method == 'PUT') {
            $rules = $this->handleUpdateSetting($request, $rules);
        }

        return $rules;
    }

    private function handleCreateSetting($request, array $rules): array
    {

        $type = $request->get('type');

        if ($type == null) {
            $type = 'string';
        }

        $rules['value'] = ['required', $type];

        return $rules;
    }

    private function handleUpdateSetting($request, array $rules): array
    {
        $settingId = $this->route('setting.id');
        $setting = Setting::find($settingId);

        $type = $request->get('type');
        $value = $request->get('value');

        if ($type) {
            $request->set('value', $setting->value);
            $rules['value'] = ['required', $type];
        }

        if ($value) {
            $settingType = $setting->type;
            $request->set('type', $settingType);
            $rules['value'] = ['required', $settingType];
        }

        return $rules;
    }
}
