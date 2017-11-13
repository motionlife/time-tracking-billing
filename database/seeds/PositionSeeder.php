<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Templates\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new Position(['name'=>'Accountant']))->save();
        (new Position(['name'=>'Accounting Bookkeeper']))->save();
        (new Position(['name'=>'Accounting Manager']))->save();
        (new Position(['name'=>'Business Intelligence Analyst']))->save();
        (new Position(['name'=>'Business Intelligence Developer']))->save();
        (new Position(['name'=>'Business Intelligence Director']))->save();
        (new Position(['name'=>'CFO']))->save();
        (new Position(['name'=>'CFO_Lead']))->save();
        (new Position(['name'=>'Controller']))->save();
        (new Position(['name'=>'Database Analyst']))->save();
        (new Position(['name'=>'Database Director']))->save();
        (new Position(['name'=>'Director FP&A']))->save();
        (new Position(['name'=>'Financial Analyst']))->save();
        (new Position(['name'=>'Financial Sr Analyst']))->save();
        (new Position(['name'=>'Staff']))->save();
    }
}
