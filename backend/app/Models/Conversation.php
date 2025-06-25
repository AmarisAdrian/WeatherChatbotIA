<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Conversation  extends Model
{
    protected $connection = 'mysql';
    protected $table = 'conversations';
    protected $fillable = ['user_name', 'user_message', 'ai_response', 'api_response'];


}