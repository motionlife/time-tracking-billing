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
        return $this->role != 0;
    }

    public function isSuperAdmin()
    {
        return $this->priority > 50;
    }

    public function isManager()
    {
        return $this->priority > 10 && $this->priority < 50;
    }

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function consultant()
    {
        if (self::ROLES[$this->role] == 'Consultant') return $this->hasOne(Consultant::class);
    }

    public function client()
    {
        if (self::ROLES[$this->role] == 'client') return $this->hasOne(Client::class);
    }

    public function outreferrer()
    {
        if (self::ROLES[$this->role] == 'Outside Referrer') return $this->hasOne(Outreferrer::class);
    }

    //all the approval this user has been processed
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
