<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafServiceEvent extends Model
{
    protected $fillable = ['user_id', 'status', 'internal_note'];
}
