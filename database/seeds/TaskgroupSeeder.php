<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Templates\Taskgroup;

class TaskgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new Taskgroup(['name'=>'Acquisition / Sell']))->save();
        (new Taskgroup(['name'=>'Banking & Leasing']))->save();
        (new Taskgroup(['name'=>'Client Admin']))->save();
        (new Taskgroup(['name'=>'Client Meetings']))->save();
        (new Taskgroup(['name'=>'Corporate Structure']))->save();
        (new Taskgroup(['name'=>'Culture Index']))->save();
        (new Taskgroup(['name'=>'Dashboards']))->save();
        (new Taskgroup(['name'=>'EOS - Traction']))->save();
        (new Taskgroup(['name'=>'Goals and Planning Assessment']))->save();
        (new Taskgroup(['name'=>'Initial Client Onboarding']))->save();
        (new Taskgroup(['name'=>'Monthly End Close']))->save();
        (new Taskgroup(['name'=>'Planning & Projections']))->save();
        (new Taskgroup(['name'=>'Process Improvement']))->save();
        (new Taskgroup(['name'=>'System Improvement']))->save();
        (new Taskgroup(['name'=>'Value Builder']))->save();
        (new Taskgroup(['name'=>'Vendor Management']))->save();

    }
}
