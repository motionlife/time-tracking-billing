<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Hour;

class Task extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    //all the hour-reports related to this task
    public function hourReports()
    {
        return $this->hasMany(Hour::class);
    }

    public function taskGroup()
    {
        return $this->belongsTo(Taskgroup::class);
    }
}
