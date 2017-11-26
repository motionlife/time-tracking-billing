<?php

namespace newlifecfo\Policies;

use newlifecfo\User;
use newlifecfo\Models\Hour;
use Illuminate\Auth\Access\HandlesAuthorization;

class HourPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        } else if (!$user->isVerified()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view the hour.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Hour $hour
     * @return mixed
     */
    public function view(User $user, Hour $hour)
    {
        //
    }

    /**
     * Determine whether the user can create hours.
     *
     * @param  \newlifecfo\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the hour.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Hour $hour
     * @return mixed
     */
    public function update(User $user, Hour $hour)
    {
        //
        $status = $hour->getStatus();
        return ($hour->arrangement->consultant_id = $user->consultant->id && ($status[0] == 'Pending' || $status[0] == 'Modified'))
            || $user->isManager();
    }

    /**
     * Determine whether the user can delete the hour.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Hour $hour
     * @return mixed
     */
    public function delete(User $user, Hour $hour)
    {
        //
        return ($hour->arrangement->consultant_id = $user->consultant->id && $hour->getStatus()[0] == 'Pending')
            || $user->isManager();
    }
}
