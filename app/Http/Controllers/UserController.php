<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\Api\UserRequest;
use App\Http\Controllers\Controller;
use Storage;

class UserController extends Controller
{
    /**
     * Create a new instance of the controller.
     *
     * @param UserService $user
     */
    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserRequest $request, $id = null)
    {

        if (!$id) {
            $response = ['data' => $request->user()];

            return response()->json($response);
        }

        if ($user = $this->user->get($id)) {
            $response = ['data' => $user];

            return response()->json($response);
        }

        return response()->json('Not found', 404);
    }

    /**
     * Update user.
     *
     * @param UserRequest $request
     *
     * @return response
     */
    public function update(UserRequest $request)
    {
        if ($user = $request->user()->update($request->all())) {
            $response = [
                'message' => 'User update successful',
                'data' => $request->user(),
            ];

            return response()->json($response);
        }

        return response()->json('Forbidden', 403);
    }

    /**
     * Search for a user.
     *
     * @param ApiRequest $request
     *
     * @return response
     */
    public function search(ApiRequest $request)
    {
        $users = $this->user->search($request);

        return response()->json($users);
    }

    /**
     * Update the avatar for the given user.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function postProfileImage(UserRequest $request)
    {
        Storage::put(
            'profile-image/'.$request->user()->id.'.jpg',
            file_get_contents($request->profile_image), 'public'
        );

        $url = env('APP_URL').Storage::url('profile-image/'.$request->user()->id.'.jpg');

        if ($user = $request->user()->update(['profile_image' => $url])) {
            $response = [
                'message' => 'User profile image upload successful',
                'profile-image' => $url,
            ];

            return response()->json($response);
        }

        return response()->json('Forbidden', 403);
    }
}
