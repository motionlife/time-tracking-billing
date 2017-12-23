<?php

namespace newlifecfo\Policies;

use Illuminate\Http\Request;
use newlifecfo\User;
use newlifecfo\Models\Arrangement;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArrangementPolicy
{
    use HandlesAuthorization;
    private $inAdminMode;

    public function before($user, $ability)
    {
//        if ($user->isSuperAdmin()) {
//            return true;
//        } else if (!$user->isVerified()) {
//            return false;
//        }
    }

    public function __construct($inAdminMode)
    {
        $this->inAdminMode = $inAdminMode;
    }

    /**
     * Determine whether the user can view the arrangement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Models\Arrangement $arrangement
     * @return mixed
     */
    public function view(User $user, Arrangement $arrangement)
    {
        //
        $consultant = $user->consultant;
        $engagement = $arrangement->engagement;
        return ($user->isSupervisor() && $this->inAdminMode) || $consultant->id == $arrangement->consultant_id ||
            ($engagement->isPending() && $engagement->leader_id == $consultant->id);
    }

    /**
     * Determine whether the user can create arrangements.
     *
     * @param  \newlifecfo\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the arrangement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Models\Arrangement $arrangement
     * @return mixed
     */
    public function update(User $user, Arrangement $arrangement)
    {
        //
    }

    /**
     * Determine whether the user can delete the arrangement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Models\Arrangement $arrangement
     * @return mixed
     */
    public function delete(User $user, Arrangement $arrangement)
    {
        //
    }
}
