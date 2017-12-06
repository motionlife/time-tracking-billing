<?php

namespace newlifecfo\Policies;

use newlifecfo\Models\Consultant;
use newlifecfo\User;
use newlifecfo\Models\Engagement;
use Illuminate\Auth\Access\HandlesAuthorization;

class EngagementPolicy
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
     * Determine whether the user can view the engagement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Engagement $engagement
     * @return mixed
     */
    public function view(User $user, Engagement $engagement)
    {
        //
        $consultant = $user->consultant;
        if ($consultant instanceof Consultant) {
            return $engagement->arrangements()->pluck('consultant_id')->contains($consultant->id);
        }
        return false;
    }

    /**
     * Determine whether the user can create engagements.
     *
     * @param  \newlifecfo\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->consultant instanceof Consultant;//any consultant can create his engagement;
    }

    /**
     * Determine whether the user can update the engagement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Engagement $engagement
     * @return mixed
     */
    public function update(User $user, Engagement $engagement)
    {
        //Policy: Only the leader can update a pending engagement, after the engagement turned into
        //active state, the team leader can only add member to it, which shall pass the policy of arrangement
        $consultant = $user->consultant;
        if ($consultant instanceof Consultant) {
            return $user->isManager() || ($consultant->id == $engagement->leader_id && $engagement->state() == 'Pending');
        }
        return false;
    }

    public function activate(User $user, Engagement $engagement)
    {
        return $user->isManager() && $engagement->state() != 'Active';
        //only manager can activate the engagement.(change the state)
    }

    /**
     * Determine whether the user can delete the engagement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Engagement $engagement
     * @return mixed
     */
    public function delete(User $user, Engagement $engagement)
    {
        //Only the leader can DELETE a pending engagement, to which no one had reported hours
        $consultant = $user->consultant;
        if ($consultant instanceof Consultant) {
            return ($user->isManager() || $consultant->id == $engagement->leader_id) && $engagement->isPending();
        }
        return false;
    }

    /**
     * Determine whether the user can close the engagement.
     *
     * @param  \newlifecfo\User $user
     * @param  \newlifecfo\Engagement $engagement
     * @return mixed
     */
    public function close(User $user, Engagement $engagement)
    {
        //Policy: Only the leader can change a engagement's state to close--SOFT DELETE
        return $user->isManager() && $engagement->state() == 'Active';
    }
}
