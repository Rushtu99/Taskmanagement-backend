<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Http\Middleware\isAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('isAdmin');
    // }

    public function showAllUsers(Request $request)
    {
        $filter = $request->search;

        $response = DB::table('users')
            ->where(function ($query) use ($filter) {
                $query->where('name', 'LIKE', "%{$filter}%")
                    ->orWhere('email', 'LIKE', "%{$filter}%")
                    ->orWhere('id', 'LIKE', "%{$filter}%");
            })->paginate(5);

        return response($response);
    }

    public function showOneUser($id)
    {
        return response()->json(User::find($id));
    }

    public function notif(Request $request){
        $user = auth()->user();
        $response = DB::table('notifications')
            ->where(function ($query) use ($user) {
                $query->where('reciever_id', '=',$user->id)
                ->where('seen','=',false);
            })->paginate(30);

            // $response;
        return response($response);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        $table = DB::table('users');
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->input('password'));
        $user->role = 2;
        $user->save();
        //$data['password'] =  Hash::make($request->input('password'));

        //$table->insert($data);
        //$table->save();
        return response()->json($user, 200);
        // User::create($data);
    }

    public function changeRole(Request $request)
    {
        $role = $request->role_change_to;
        $user = auth()->user();
        $user_ = User::findOrFail($request->to);
        //return response($user_);
        if ($user->role == 1) {
            $user_->role = $role;
            $user_->save();
            return response("changed ADMIN");
        }
        return response("nooo :(");
    }

    public function role(Request $request)
    {
        $user = auth()->user();
        return response($user->role);
    }

    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return response()->json($user, 200);
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }
}
