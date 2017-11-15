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

    //get the attached receipt
    public function receipt()
    {
        return $this->hasOne(Receipt::class)->withDefault([
            'filename' => 'null',
            'description' => 'null'
        ]);
    }

    public function total()
    {
        return $this->company_paid ? 0 : $this->hotel + $this->flight + $this->meal + $this->office_supply
            + $this->car_rental + $this->mileage_cost + $this->other;
    }
}
