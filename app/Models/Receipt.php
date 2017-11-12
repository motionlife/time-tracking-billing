<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $guarded = [];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
