<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\ApiRequest;

class AuthRequest extends ApiRequest
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
        switch ($this->path()) {
            case 'auth/login':
                return [
                    'auth_id' => 'required',
                    'password' => 'required',
                ];
                break;
            case 'auth/login-facebook':
                return [
                    'facebook_user_id' => 'required',
                    'facebook_access_token' => 'required',
                    'facebook_token_expires' => 'required',
                ];
                break;
            case 'auth/forgot-password':
                return ['email' => 'bail|required|email|exists:users,email'];
            default:
                return [];
                break;
        }
    }
}
