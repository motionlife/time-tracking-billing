<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Hour;

class Task extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    //all the hour-reports related to this task
    public function hours()
    {
        return $this->hasMany(Hour::class);
    }

    public function taskGroup()
    {
        return $this->belongsTo(Taskgroup::class,'taskgroup_id')->withDefault([
            'name'=>'Other'
        ]);
    }

    public function getDesc()
    {
        $desc = $this->description;
        if (!$desc||$desc=='Other') $desc = $this->taskGroup->name.'-Other';
        return $desc;
    }
}
