<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;
use newlifecfo\Models\Templates\Position;

class Arrangement extends Model
{
    protected $guarded = [];

    //get it's parent engagement
    public function engagement()
    {
        return $this->belongsTo(Engagement::class);
    }

    //get the arranged consultant
    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }

    //get the arranged position
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    //all the hour reports for this arrangement
    public function hours()
    {
        return $this->hasMany(Hour::class);
    }

    //all the expense reports for this arrangement
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

}
