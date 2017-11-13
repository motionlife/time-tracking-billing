<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Templates\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new Task(['taskgroup_id'=>1,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Build / Manage Data Requests/Room']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Due Diligence']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Projections and Deal Book']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Quality of Earning Analysis']))->save();

        (new Task(['taskgroup_id'=>2,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Covenant Review and Reporting']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Expand Current Credit Line']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Negotiate & Review Agreements']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Source New Bank or Lender']))->save();

        (new Task(['taskgroup_id'=>3,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>3,'description'=>'Email & Correspondence']))->save();
        (new Task(['taskgroup_id'=>3,'description'=>'Engagement Review and Billing']))->save();

        (new Task(['taskgroup_id'=>4,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Accounting Staff']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'CEO']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Executive Team']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Investors/Bankers']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Lawyers/CPA']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Summit - Preparation']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Summit / Board Meetings']))->save();

        (new Task(['taskgroup_id'=>5,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>5,'description'=>'Compensation Work']))->save();
        (new Task(['taskgroup_id'=>5,'description'=>'Entity Review/Transitions']))->save();
        (new Task(['taskgroup_id'=>5,'description'=>'Tax Planning/Review']))->save();

        (new Task(['taskgroup_id'=>6,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>6,'description'=>'Consult / Transition with CI Consultant']))->save();
        (new Task(['taskgroup_id'=>6,'description'=>'Meeting with Management: Strengths & Weaknesses']))->save();
        (new Task(['taskgroup_id'=>6,'description'=>'Survey CEO / Leadership']))->save();

        (new Task(['taskgroup_id'=>7,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Custom Desgin and Implement']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Implimentation']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Monthly Down/Up Load Refresh & Proofing data']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Presentation and Training']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Review Results & Alerts']))->save();

        (new Task(['taskgroup_id'=>8,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'Defining Prioritizing Rocks']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'Kick off']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'L10 Meetings']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'Wokring on Quarterly Rocks']))->save();

        (new Task(['taskgroup_id'=>9,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Assess Future "What if" Value']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Plan of Action Define and Prioritze']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Report & Scoring Review']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Review Results']))->save();

        (new Task(['taskgroup_id'=>10,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'3 Year Financial Diagnostic Analysis']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Diagnostic']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Execute Agreements and Fee Structure']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Gain Access to System/Data and Historical Financials']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Interview Management Team']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Present Initial Findings']))->save();

        (new Task(['taskgroup_id'=>11,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Controller Review']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Monthly Close Summit']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Process Improvement']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Reconcile GL Accounts']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Report Generation']))->save();

        (new Task(['taskgroup_id'=>12,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'13 Week Short Term Cash Flow']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'AOP']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'Quarterly Re-forecast of AOP']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'Strategic Planning 3-5+ Years']))->save();

        (new Task(['taskgroup_id'=>13,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>13,'description'=>'Process Flow Review & Design']))->save();
        (new Task(['taskgroup_id'=>13,'description'=>'Training & Monitoring']))->save();

        (new Task(['taskgroup_id'=>14,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'Assess Current Needs / Capabilities']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'Build Tools: Reporting & Process Automation']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'Evaluate ERP Vendors and Negotiate Pricing']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'System Implementation & Training']))->save();

        (new Task(['taskgroup_id'=>15,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Assess Future "What if" Value']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Plan of Action Define and Prioritze']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Report & Scoring Review']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Review Results']))->save();

        (new Task(['taskgroup_id'=>16,'description'=>'Other']))->save();
        (new Task(['taskgroup_id'=>16,'description'=>'Vendor Negotiation/Meetings']))->save();

    }
}
