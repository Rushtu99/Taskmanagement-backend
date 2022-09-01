<?php namespace App\Http\Middleware;

    use Closure;

    class Cors
    {
     
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => ' *'
               ];

        if($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        foreach($headers as $key => $value)
        {
            $response->header($key, $value);
        }

        return $response;

    }
}