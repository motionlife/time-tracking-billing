<?php

namespace newlifecfo;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Outreferrer;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'priority'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    //Get the corresponding client attached to the user (if exists)
    public function client()
    {
        return $this->hasOne(Client::class)->withDefault([
            'name' => 'Not_A_Client'
        ]);
    }

    //Get the corresponding consultant attached to the user (if exists)
    public function consultant()
    {
        return $this->hasOne(Consultant::class)->withDefault([
            'first_name' => 'Not_A_Consultant',
            'last_name' => 'Not_A_Consultant'
        ]);
    }

    //Get the corresponding client attached to the user (if exists)
    public function outreferrer()
    {
        return $this->hasOne(Outreferrer::class)->withDefault([
            'first_name' => 'Not_An_Outside_Referrer',
            'last_name'=>'Not_An_Outside_Referrer'
        ]);
    }
}
