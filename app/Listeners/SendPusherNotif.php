<?php
namespace App\Listeners;
use App\Events\TaskAssignedEvent;
use Pusher\Pusher;
use Illuminate\Support\Facades\Mail;
// use App\Models\User;
class SendPusherNotif
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     *
     * @param  TaskAssigned  $event
     * @return void
     */
    public function handle(TaskAssignedEvent $data)
    {
        $user=$data->user;
        // dd($user["by"]->id);
        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), ['cluster' => env('PUSHER_APP_CLUSTER')]);
        $pusher->trigger("my-channel-{$user["to"]->id}", 'Task Assigned', 'new task assigned');
     
        
    }
}