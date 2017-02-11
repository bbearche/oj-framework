<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Headers.
     *
     * @var array
     */
    protected $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods'=> 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'=> 'Accept, Authorization, Content-Type, Origin, X-Requested-With',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
           return response()->json([], 200, $this->headers);
       }

        $response = $next($request);

        foreach($this->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
