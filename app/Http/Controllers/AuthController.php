<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;



class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh','captcha']]);
    }

    public function emailRequestVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email address is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json('Email request verification sent to ' . Auth::user()->email);
    }

    public function emailVerify(Request $request)
    {
        // $this->validate($request, [
        //     'token' => 'required|string',
        // ]);
        \Tymon\JWTAuth\Facades\JWTAuth::getToken();
        \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        if (!$request->user()) {
            return response()->json('Invalid token', 401);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email address ' . $request->user()->getEmailForVerification() . ' is already verified.');
        }
        $request->user()->markEmailAsVerified();
        return response()->json('Email address ' . $request->user()->email . ' successfully verified.');
    }


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {;
            return response("invalid arg", 401);
        }
        return $this->respondWithToken($token);
    }

    public function me()
    {
        //return response("invalid",401);
        return response()->json(auth()->user());
    }


    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function createAdmin($id, Request $request)
    {

        $table = DB::table('users')->find($id);

        if ($table) {
            $user['email'] = $table->email;
            $user['mac_name'] = $table->name;
            //return response()->json($user);
            $temp = DB::table('admins')->get();
            foreach ($temp as $t) {
                if ($t->email == $user['email']) {
                    return response("already admin");
                }
            }
            $admin = new Admin;
            $admin->mac_name = $user['mac_name'];
            $admin->email = $user['email'];
            $admin->save();
            return response()->json($user, 200);
        }
        return response("user doesnot exist");
    }

    public function admin(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 1) {
            return response(1);
        }
        return response(0);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }

    public function test(Request $request)
    {
        Mail::send();
        return response();
    }

    public function captcha(Request $request){
        $token = $request->ref;
        $key = env('CAPTCHA_SECRET_KEY');
        $url = "https://www.google.com/recaptcha/api/siteverify?secret={$key}&response={$token}";
        $response = Http::post($url);

        return response($response);

    }
}
