<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Revenue extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    //get the client publish this revenue
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
