<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    //the arrangement it belong to
    public function arrangement()
    {
        return $this->belongsTo(Arrangement::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class)->withDefault([
            'filename'=>'null',
            'description'=>'null'
        ]);
    }
}
