<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\User;

class Approval extends Model
{
    protected $guarded = [];

    //get the user the approval been requested to
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
