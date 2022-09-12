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
use App\Events\TaskAssignedEvent;
use Illuminate\Contracts\Event\Dispatcher;
use Pusher\Pusher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;


class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'desc' => 'required',
            'assigned_to' => 'required',
            'due_date' => 'required|date',
        ]);
        $task = new Task;
        $to = User::findorfail($request->assigned_to);


        $task->status = 'assigned';

        $task->due_date = $request->due_date;
        $task->desc = $request->desc;
        $task->title = $request->title;
        $task->assigned_to = $request->assigned_to;
        $task->assigned_by = (auth()->user()->id);
        $task->assigned_by_name = (auth()->user()->name);
        $task->assigned_to_name = ($to->name);
        $user = auth()->user();


        $data = ['by' => $user, 'to' => $to];

        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), ['cluster' => env('PUSHER_APP_CLUSTER')]);

        $pusher->trigger('my-channel', 'my-event', array('new task assigned'));
        event(new TaskAssignedEvent($data));
        //return response('b');
        if ($user->role == 1) {
            $task->save();
            return response()->json($task, 200);
        } else {
            if ($request->assigned_to == $user->id) {
                $task->save();
                return response()->json($task, 200);
            }
        }
        return response()->json("not created", 200);
    }

    public function showTasks(Request $request)
    {
        $me = auth()->user()->id;
        $temp = DB::table('tasks')->get();
        $results = DB::table('tasks')
            ->where('status', '!=', 'Deleted')
            ->where(function ($query) {
                $query->where('assigned_to', '=', auth()->user()->id)
                    ->orWhere('assigned_by', '=', auth()->user()->id);
            })
            ->get();
        return response()->json($results);
    }

    public function showAllTasks(Request $request)
    {
        $me = auth()->user()->id;
        $temp = DB::table('tasks')->get();
        return response()->json($temp);
    }

    public function changeStatus(Request $request)
    {
        $status = $request->status_change_to;
        $user = auth()->user();
        $task = Task::findOrFail($request->id);
        if ($user->role == 1 || $task->assigned_by == $user->id || $task->assigned_to == $user->id) {
            $task->status = $status;
            $task->save();
            return response("changed ADMIN");
        }
        return response("not Authorized", 401);
    }


    public function deleteTask($id,Request $request)
    {
        $user = auth()->user();
        $task = Task::findOrFail($id);
        if ($user->role == 1 || $task->assigned_by == $user->id || $task->assigned_to == $user->id) {
            $task = Task::findOrFail($id);
            $task->status = "Deleted";
            $task->save();
        }
        return response('Deleted Successfully', 200);
    }
}
