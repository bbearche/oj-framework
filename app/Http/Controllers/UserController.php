<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
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
    public function update(Request $request, $id)
    {
        if ($user = $this->user->update($request, $id)) {
            $response = [
                'message' => 'User update successful',
                'data' => $user,
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
    public function search(Request $request)
    {
        $users = $this->user->search($request);

        return response()->json($users);
    }
}
