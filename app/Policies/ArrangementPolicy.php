<?php

namespace newlifecfo\Policies;

use newlifecfo\User;
use newlifecfo\Models\Arrangement;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArrangementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the arrangement.
     *
     * @param  \newlifecfo\User  $user
     * @param  \newlifecfo\Models\Arrangement  $arrangement
     * @return mixed
     */
    public function view(User $user, Arrangement $arrangement)
    {
        //
    }

    /**
     * Determine whether the user can create arrangements.
     *
     * @param  \newlifecfo\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the arrangement.
     *
     * @param  \newlifecfo\User  $user
     * @param  \newlifecfo\Models\Arrangement  $arrangement
     * @return mixed
     */
    public function update(User $user, Arrangement $arrangement)
    {
        //
    }

    /**
     * Determine whether the user can delete the arrangement.
     *
     * @param  \newlifecfo\User  $user
     * @param  \newlifecfo\Models\Arrangement  $arrangement
     * @return mixed
     */
    public function delete(User $user, Arrangement $arrangement)
    {
        //
    }
}
