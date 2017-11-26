<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use newlifecfo\User;

class Approval extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    //get the user the approval been requested to
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
