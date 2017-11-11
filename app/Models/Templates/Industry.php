<?php

namespace newlifecfo\Models\Templates;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Client;

class Industry extends Model
{
    protected $guarded = [];
    protected $table = 'industries';

    //all the clients in this industry
    public function clients()
    {
        return $this->hasMany(Client::class, 'industry_id');
    }
}
