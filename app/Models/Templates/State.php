<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    //get all the contacts that filled this state
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
