<?php
namespace App\Events;
use App\Models\Task;  // changed here
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable; // not available in lumen
use Illuminate\Broadcasting\InteractsWithSockets;
class TaskAssignedEvent extends Event implements ShouldBroadcast
{
    use  SerializesModels;
    public $user;
    public function __construct($user)
    {
        $this->user=$user;
    }
    public function broadcastOn()
    {
        return [env('PUSHER_APP_KEY')];
    }

}