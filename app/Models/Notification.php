<?php

namespace App\Models;
// namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model 
{

    use Notifiable;

    protected $fillable = [
        'reciever_id', 'sender_id', 'message', 'description','seen'
    ];

    protected $hidden = [];

    protected $casts = [];

    protected static function boot()
    {
        parent::boot();
    }
}
