<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
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
        $t = (auth()->user()->name);
        $notif = new Notification;
        $to = User::findorfail($request->assigned_to);
        // 'reciever_id', 'sender_id', 'message', 'description','seen'
        $notif->reciever_id = $request->assigned_to;
        $notif->sender_id = (auth()->user()->id);
        $notif->message = "New Task assigned";
        $notif->description = "New Task $request->title has been assigned by {$t}. Deadline is {$request->due_date}";
        $notif->seen = false;

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
        event(new TaskAssignedEvent($data));

     
        if ($user->role == 1) {
            $task->save();
            $notif->save();
            return response()->json($task, 200);
        } else {
            if ($request->assigned_to == $user->id) {
                $task->save();
                $notif->save();
                return response()->json($task, 200);
            }
        }
        return response()->json("not created", 200);
    }

    public function showTasks(Request $request)
    {
        $filter = $request->search;
            $sort = 'due_date';
    
        if($request->sort == 'Assigned By'){
            $sort = 'assigned_by';
        }
        if($request->sort == 'Title'){
            $sort = 'title';
        }
        if($request->sort == 'Assigned To'){
            $sort = 'assigned_to';
        }
        if($request->sort == 'Status'){
            $sort = 'status';
        }

        $me = auth()->user()->id;
        $results = DB::table('tasks')
            ->where('status', '!=', 'Deleted')
            ->where(function ($query) {
                $query->where('assigned_to', '=', auth()->user()->id)
                    ->orWhere('assigned_by', '=', auth()->user()->id);
            })
            ->where(function ($query) use ($filter) {
                $query->where('assigned_to_name', 'LIKE', "%{$filter}%")
                    ->orWhere('assigned_by_name', 'LIKE', "%{$filter}%")
                    ->orWhere('status', 'LIKE', "%{$filter}%")
                    ->orWhere('desc', 'LIKE', "%{$filter}%")
                    ->orWhere('title', 'LIKE', "%{$filter}%");
            })->orderBy($sort,"asc")
            ->paginate(6);
        return response()->json($results);
    }

    public function showAllTasks(Request $request)
    {
        $filter = $request->search;
        // return response($request);
        
            $sort = 'due_date';
        
        if($request->sort == 'Assigned By'){
            $sort = 'assigned_by';
        }
        if($request->sort == 'Title'){
            $sort = 'title';
        }
        if($request->sort == 'Assigned To'){
            $sort = 'assigned_to';
        }
        if($request->sort == 'Status'){
            $sort = 'status';
        }
        $me = auth()->user()->id;
        $temp = DB::table('tasks')->where(function ($query) use ($filter) {
            $query->where('assigned_to_name', 'LIKE', "%{$filter}%")
                ->orWhere('assigned_by_name', 'LIKE', "%{$filter}%")
                ->orWhere('status', 'LIKE', "%{$filter}%")
                ->orWhere('desc', 'LIKE', "%{$filter}%")
                ->orWhere('title', 'LIKE', "%{$filter}%");
        })->orderBy($sort,"asc")
        ->paginate(6);
        return response()->json($temp);
    }

    public function changeStatus(Request $request)
    {
        $status = $request->status_change_to;
        $user = auth()->user();
        $task = Task::findOrFail($request->id);

        $notif = new Notification;
        $to = User::findorfail($task->assigned_by);
        // 'reciever_id', 'sender_id', 'message', 'description','seen'
        $t = (auth()->user()->name);

        $notif->reciever_id = $task->assigned_by;
        $notif->sender_id = (auth()->user()->id);
        $notif->message = "Task status updated";
        $notif->description = "Task {$title} status has been changed to {$status} by {$t}";
        $notif->seen = false;

        if ($user->role == 1 || $task->assigned_by == $user->id || $task->assigned_to == $user->id) {
            $task->status = $status;
            $task->save();
            $notif->save();
            return response("changed ADMIN");
        }
        return response("not Authorized", 401);
    }


    public function deleteTask($id, Request $request)
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

    public function stats(Request $request)
    {
        $me = auth()->user()->id;
        $temp = DB::table('tasks')->get();
        $Assigned = 0;
        $InProgress = 0;
        $Completed = 0;

        if ($request->type == 'to') {
            $results = DB::table('tasks')
                ->where('status', '!=', 'Deleted')
                ->where(function ($query) {
                    $query->where('assigned_to', '=', auth()->user()->id);
                    // ->orWhere('assigned_by', '=', auth()->user()->id);
                })->get();
        }
        if ($request->type == 'by') {
            $results = DB::table('tasks')
                ->where('status', '!=', 'Deleted')
                ->where(function ($query) {
                    $query->where('assigned_by', '=', auth()->user()->id);
                    // ->orWhere('assigned_by', '=', auth()->user()->id);
                })->get();
        }
        foreach ($results as $task) {
            if ($task->status == 'Completed') {
                $Completed++;
            }
            if ($task->status == 'assigned') {
                $Assigned++;
            }
            if ($task->status == 'In Progress') {
                $InProgress++;
            }
        }

        return response([$Assigned, $Completed, $InProgress], 200);
    }

    public function changeTaskStatusBulk(Request $request)
    {
        $arr = $request->idArray;
        $action = $request->bulkAction;
        $user = auth()->user();
        if ($user->role == 1) {
            foreach ($arr as $id) {
                $task = Task::findOrFail($id);
                $task->status = $action;
                $task->save();
            }
        } else {
            return response("not Authorized", 401);
        }
        return response($request);
    }

    public function setSeen(Request $request){
        $id = $request->id;
        $notif = Notification::findOrFail($id);
        $notif->seen = true;
        $notif->save();
        return response($id);
    }
}
