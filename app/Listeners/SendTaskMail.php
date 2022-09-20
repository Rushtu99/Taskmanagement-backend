<?php

namespace App\Listeners;

use App\Events\TaskAssignedEvent;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;

// use App\Models\User;
class SendTaskMail
{

    public function __construct()
    {
        //
    }

    public function handle(TaskAssignedEvent $data)
    {
        $user=$data->user;

        Mail::send('emails.taskassigned',$data->user, function ($message) use($user) {
        $message->to($user['to']->email, $user['to']->name)->subject('new task assigned');
        $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
      });
    }
}
