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

        $this->validate($request, [
            'title' => 'required',
            'desc' => 'required',
            'assigned_to' => 'required',
            //'due_date' => 'required|date',
        ]);
        $task = new Task;
        $to = User::findorfail($request->assigned_to);

        //$data = $request->all();

        $task->status = 'assigned';
        $task->due_date = carbon::now();
        $task->desc = $request->desc;
        $task->title = $request->title;
        
        $task->assigned_to = $request->assigned_to;
        $task->assigned_by = (auth()->user()->id);
        $task->assigned_by_name = (auth()->user()->name);
        $task->assigned_to_name = ($to->name);

        $user = auth()->user();
        $admin = Admin::find($user->id);
        //return response()->json($request->desc);

        if ($admin) {
            $task->save();
            return response()->json($task, 200);
        } else {
            if ($request->assigned_to == $user->id) {
                $task->save();
                return response()->json($task, 200);
            }
        }

        // Task::create($data);

        return response()->json("not created", 200);
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
        //return response()->json($request);
        $status = $request->status_change_to;
        $user = auth()->user();
        $admin = Admin::find($user->id);
        $task = Task::findOrFail($request->id);
        //return response($task);
        if ($admin) {
            $task->status = $status;
            $task->save();

            return response("changed ADMIN");
        } else {
            if ($request->assigned_to == $user->id) {
                $task->status = $status;
                $task->save();

                return response("changed");
            }
        }
        // $task->save();
        return response("nooo :(");
    }
}
