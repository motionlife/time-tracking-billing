<?php

namespace newlifecfo;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use newlifecfo\Models\Approval;
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
        'first_name', 'last_name', 'email', 'password', 'priority','role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $roles = ['Unassigned','Consultant','Client','Outside Referrer','General Admin','Super Admin','root'];

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    //Get the corresponding role entity
    public function entity()
    {
        switch ($this->role){
            case 1:
                return $this->hasOne(Consultant::class);
            case 2:
                return $this->hasOne(Client::class);
            case 3:
                return $this->hasOne(Outreferrer::class);
            default:
                return $this;
        }
    }

    //all the approval this user has been processed
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
