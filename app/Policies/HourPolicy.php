<?php

namespace newlifecfo\Policies;

use newlifecfo\User;
use newlifecfo\Models\Hour;
use Illuminate\Auth\Access\HandlesAuthorization;

class HourPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the hour.
     *
     * @param  \newlifecfo\User  $user
     * @param  \newlifecfo\Hour  $hour
     * @return mixed
     */
    public function view(User $user, Hour $hour)
    {
        //
    }

    /**
     * Determine whether the user can create hours.
     *
     * @param  \newlifecfo\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the hour.
     *
     * @param  \newlifecfo\User  $user
     * @param  \newlifecfo\Hour  $hour
     * @return mixed
     */
    public function update(User $user, Hour $hour)
    {
        //
    }

    /**
     * Determine whether the user can delete the hour.
     *
     * @param  \newlifecfo\User  $user
     * @param  \newlifecfo\Hour  $hour
     * @return mixed
     */
    public function delete(User $user, Hour $hour)
    {
        //
    }
}
