<?php

namespace newlifecfo;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use newlifecfo\Models\Approval;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Outreferrer;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
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

    public function unRecognized()
    {
        return $this->priority == 0;
    }

    public function isVerified()
    {
        return !$this->unRecognized() && !$this->isInactive();
    }

    public function isInactive()
    {
        return $this->priority > 0 && $this->priority < 3;
    }

    public function isNormalUser()
    {
        return $this->priority > 2 && $this->priority < 5;
    }

    public function isLeaderCandidate()
    {
        return $this->priority > 4 && $this->priority < 10;
    }

    public function isSupervisor()
    {
        return $this->isManager() || $this->isSuperAdmin();
    }

    public function getType()
    {
        return self::ROLES[$this->role];
    }

    public function isVerifiedConsultant()
    {
        return $this->isVerified() && self::ROLES[$this->role] == 'Consultant';
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
