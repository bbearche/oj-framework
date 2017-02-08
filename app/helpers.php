<?php

if (!function_exists('api_token')) {
    /**
     * Get an API token.
     *
     * @param  Request $request
     * @return mixed
     */
    function api_token($request)
    {
        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post(env('APP_URL').'/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('API_CLIENT_ID'),
                    'client_secret' => env('API_CLIENT_SECRET'),
                    'username' => $request->input('email'),
                    'password' => $request->input('password'),
                    'scope' => '',
                ],
            ]);
        } catch (Exception $e) {
            return false;
        }

        return json_decode((string) $response->getBody(), true);
    }
}

if (!function_exists('clear_api_tokens')) {
    /**
     * Clear a user's access tokens.
     *
     * @param  Request $request
     * @return mixed
     */
    function clear_tokens($user)
    {
        $user->tokens()->delete();
    }
}

if (!function_exists('notify')) {
    /**
     * Application notification helper function.
     *
     * @param string $key
     * @param int    $object_id
     * @param int    $to
     * @param mixed  $from
     */
    function notify($key, $object_id, $to, $from = null)
    {
        $notificationInterface = app()->make('App\Interfaces\NotificationInterface');

        $notification = $notificationInterface->create($key, $object_id, $to, $from);

        event(new \App\Events\NotificationEvent($notification));
    }
}

if (!function_exists('reward')) {
    /**
     * Application reward helper function.
     *
     * @param string $key
     * @param object $user
     * @param array  $data
     */
    function reward($key, $user, $data = [])
    {
        $rewardInterface = app()->make('App\Interfaces\RewardInterface');

        event(new \App\Events\RewardEvent($key, $user, $data));
    }
}
