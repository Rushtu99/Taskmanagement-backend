<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Task;
use App\Http\Middleware\isAdmin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        //return response("hello");
        $this->validate($request, [
            'desc' => 'required',
            'assigned_to' => 'required',
            //'due_date' => 'required|date',
         ]);
        $data = $request->all();
        $data['status'] = 'assigned';
        $data['due_date'] = carbon::now();
        $to=User::findorfail($request->assigned_to);
        $data['assigned_by'] = (auth()->user()->id);
        $data['assigned_by_name'] = (auth()->user()->name);
        $data['assigned_to_name'] = ($to->name);


        Task::create($data);
        return response()->json($data, 200);
    }

    public function showTasks(Request $request)
    {
        $me = auth()->user()->id;
        $temp = DB::table('tasks')->get();
        $results = DB::select('select * from tasks where assigned_to = :x', ['x' => $me]);
        return response()->json($results);
    }

    public function changeStatus(Request $request)
    {
        $status = $request->status_change_to;
        $user = auth()->user();
        
        $admin = Admin::find($user->id);
        $task = Task::findOrFail($request->id);
        //return response($task);
        if($admin){
            $task->status = $status;
            $task->save();

            return response("changed ADMIN");
        }
        else{
            if($request->assigned_to == $user->id){
                $task->status = $status;
                $task->save();

                return response("changed");
            }
        }
        $task->save();
        return response("nooo :(");
    }
}

