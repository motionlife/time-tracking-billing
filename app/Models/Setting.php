<?php

namespace newlifecfo\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $guarded = [];
    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }
    public function toggle($value)
    {

        $values = explode(',',$this->value);
        if(!$values[0]) array_shift($values);
        if(in_array($value,$values)){
            $values = array_diff($values,[$value]);
        }else{
            array_push($values,$value);
        }
        $this->value = implode(',',$values);
        return $this->save();
    }
}
