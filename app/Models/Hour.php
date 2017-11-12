<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Task;

class Hour extends Model
{
    protected $guarded = [];

    //to which arrangement the hour reported
    public function arrangement()
    {
        return $this->belongsTo(Arrangement::class);
    }

    //what task does it report
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
