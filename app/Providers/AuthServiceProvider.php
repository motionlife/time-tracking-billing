<?php

namespace newlifecfo\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;
use newlifecfo\Policies\ArrangementPolicy;
use newlifecfo\Policies\EngagementPolicy;
use newlifecfo\Policies\HourPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'newlifecfo\Model' => 'newlifecfo\Policies\ModelPolicy',
        Engagement::class => EngagementPolicy::class,
        Arrangement::class=>ArrangementPolicy::class,
//        Hour::class=>HourPolicy::class,
//        Expense::class=>EngagementPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
