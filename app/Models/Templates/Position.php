<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Arrangement;

class Position extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    //all the arrangements this position has ever been assigned
    public function arrangements()
    {
        return $this->hasMany(Arrangement::class);
    }
}
