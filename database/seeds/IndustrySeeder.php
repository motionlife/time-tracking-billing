<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Templates\Industry;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new Industry(['name'=>'Amusement/Entertainment','naics'=>0]))->save();
        (new Industry(['name'=>'Auto Repair','naics'=>0]))->save();
        (new Industry(['name'=>'Building Repair & Maintenance','naics'=>0]))->save();
        (new Industry(['name'=>'Company Admin','naics'=>0]))->save();
        (new Industry(['name'=>'Construction-Commercial','naics'=>0]))->save();
        (new Industry(['name'=>'Distribution','naics'=>0]))->save();
        (new Industry(['name'=>'Equipment Finance','naics'=>0]))->save();
        (new Industry(['name'=>'Home & Garden Center','naics'=>0]))->save();
        (new Industry(['name'=>'Home Builder','naics'=>0]))->save();
        (new Industry(['name'=>'Home Repair & Maintenance','naics'=>0]))->save();
        (new Industry(['name'=>'IT Services & Programming','naics'=>0]))->save();
        (new Industry(['name'=>'Manufacturing - Job Shop','naics'=>0]))->save();
        (new Industry(['name'=>'Manufacturing - Process','naics'=>0]))->save();
        (new Industry(['name'=>'Medical Devices','naics'=>0]))->save();
        (new Industry(['name'=>'Oil & Gas','naics'=>0]))->save();
        (new Industry(['name'=>'Professional Services/Consultant','naics'=>0]))->save();
        (new Industry(['name'=>'Restaurant','naics'=>0]))->save();
        (new Industry(['name'=>'Retail Store','naics'=>0]))->save();
        (new Industry(['name'=>'Surgical/Physician Practice','naics'=>0]))->save();
        (new Industry(['name'=>'Telecom - Services','naics'=>0]))->save();
        (new Industry(['name'=>'Trucking & Logistics','naics'=>0]))->save();
        (new Industry(['name'=>'Wholesale','naics'=>0]))->save();
    }
}
