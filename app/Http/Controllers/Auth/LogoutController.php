<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * Authenticate a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed  $user
     * @return mixed
     */
    public function __invoke(Request $request)
    {

        if ($request->user()) {

            $request->user()->token()->revoke();

            return response()->json(['data' => [
                'message' => 'Logout successful'
            ]]);
        }
    }
}
