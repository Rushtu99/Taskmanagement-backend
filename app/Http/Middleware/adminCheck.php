<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class adminCheck
{
    public function handle($request, Closure $next)
    {
        $email = auth()->user()['email'];

        //$id = $data['id'];
        $temp = DB::table('admins')->get();
        foreach ($temp as $t){
            if($t->email == $email){
                return $next($request);
            }
        }
        return response("not admin :(");
   
    }
}
