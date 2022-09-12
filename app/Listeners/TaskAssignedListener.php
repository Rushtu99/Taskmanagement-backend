<?php
namespace App\Listeners;
use App\Events\TaskAssignedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
// use App\Models\User;
class TaskAssignedListener
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
        
        //  dd($data->user);
        // dd($user->email);
        // $data=['to' => $user,'by' => $by];
        $user=$data->user;
        // dd($user);

        Mail::send('emails.taskassigned',$data->user, function ($message) use($user) {
        $message->to($user['to']->email, $user['to']->name)->subject('new task assigned');
        $message->from('lumen_react@dnr.com','react-app');
      });
    }
}