<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $guarded = [];

    //get the expense report the receipt belongs to
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
