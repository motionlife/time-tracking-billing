<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $guarded = [];

    //get the client publish this revenue
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
