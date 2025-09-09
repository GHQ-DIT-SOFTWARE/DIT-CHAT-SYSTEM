<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
     protected $fillable = [
        'send_id',
        'receiver_id',
        'message',
        'is_read',
    ];
}
