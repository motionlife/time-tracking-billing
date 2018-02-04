<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;

class Taskgroup extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    //all the tasks belong to this group
    public function tasks()
    {
        return $this->hasMany(Task::class,'taskgroup_id');
    }
    public static function newGroups()
    {
        return Taskgroup::where('id','>',20)->get();
    }
}
