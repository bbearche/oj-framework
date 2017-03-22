<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class UserRequest extends ApiRequest
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
        return $this->validationRules();
    }

    /**
     * Validation rules for this request.
     *
     * @return array
     */
        private function validationRules($rules = [])
    {
        $rules = $this->validateUserRegistration() ?:
                 $this->validateUpdateUser() ?:
                 $this->validateUserSettings();

        return $rules;
    }

    /**
     * Validate registration request.
     *
     * @return array
     */
    private function validateUserRegistration()
    {
        if ($this->method() == 'POST' && $this->is('register')) {

            return [
                'email' => [
                    'bail', 'required', 'email',
                    Rule::unique('users'),
                ],
                'username' => [
                    'bail', 'sometimes', 'required', 'alpha_dash', 'min:3',' max:255',
                    Rule::unique('users'),
                ],
                'password' => 'required_without:facebook_user_id|min:6',
                'profile_image' => 'image|max=2000|filled',
            ];
        }

        return [];
    }

    /**
     * Validate user update request.
     *
     * @return array
     */
    private function validateUpdateUser()
    {

        if ($this->isMethod('PUT')) {
            return [
                'email' => [
                    'bail', 'sometimes', 'required', 'email', 'max:255',
                    Rule::unique('users')->ignore($this->user()->id),
                ],
                'username' => [
                    'bail', 'sometimes', 'required', 'alpha_dash', 'min:3',' max:255',
                    Rule::unique('users')->ignore($this->user()->id)
                ],
                'password' => 'sometimes|required|min:6',
            ];
        } else if($this->isMethod('POST') && $this->is('user/*/profile-image')) {
            return ['profile_image' => 'required|image|max:2000'];
        }

        return [];
    }

    /**
     * Validate user settings request.
     *
     * @return array
     */
    private function validateUserSettings()
    {
        if ($this->method() == 'PUT' && $this->is('user/settings')) {
            return [
                'badge_app_icon' => 'boolean',
                'private_account' => 'boolean',
                'push_notification_connection_requests' => 'boolean',
                'push_notification_matched_name' => 'boolean',
                'push_notification_shared_name' => 'boolean',
                'push_notification_shared_name_response' => 'boolean',
                'push_notification_shared_list' => 'boolean',
                'push_notification_reminders' => 'boolean',
                'push_notification_rewards' => 'boolean',
                'push_notification_product_announcements' => 'boolean'
            ];
        }

        return [];
    }
}
