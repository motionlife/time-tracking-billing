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
        'first_name', 'last_name', 'email', 'password', 'priority', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    const ROLES = ['Unassigned', 'Client', 'Outside Referrer', 'Consultant', 'General Admin', 'Super Admin', 'root'];

    public function isVerified()
    {
        return $this->role >= 1;
    }

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    //Get the corresponding role entity
    public function entity()
    {
        switch (self::ROLES[$this->role]) {
            case 'Consultant':
                return $this->hasOne(Consultant::class);
            case 'Client':
                return $this->hasOne(Client::class);
            case 'Outside Referrer':
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
