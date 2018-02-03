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
        (new Task(['taskgroup_id'=>1,'description'=>'Build / Manage Data Requests/Room']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Due Diligence']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Projections and Deal Book']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Quality of Earning Analysis']))->save();
        (new Task(['taskgroup_id'=>1,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>2,'description'=>'Covenant Review and Reporting']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Expand Current Credit Line']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Negotiate & Review Agreements']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Source New Bank or Lender']))->save();
        (new Task(['taskgroup_id'=>2,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>3,'description'=>'Email & Correspondence']))->save();
        (new Task(['taskgroup_id'=>3,'description'=>'Engagement Review and Billing']))->save();
        (new Task(['taskgroup_id'=>3,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>4,'description'=>'Accounting Staff']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'CEO']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Executive Team']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Investors/Bankers']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Lawyers/CPA']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Summit - Preparation']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Summit / Board Meetings']))->save();
        (new Task(['taskgroup_id'=>4,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>5,'description'=>'Compensation Work']))->save();
        (new Task(['taskgroup_id'=>5,'description'=>'Entity Review/Transitions']))->save();
        (new Task(['taskgroup_id'=>5,'description'=>'Tax Planning/Review']))->save();
        (new Task(['taskgroup_id'=>5,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>6,'description'=>'Consult / Transition with CI Consultant']))->save();
        (new Task(['taskgroup_id'=>6,'description'=>'Meeting with Management: Strengths & Weaknesses']))->save();
        (new Task(['taskgroup_id'=>6,'description'=>'Survey CEO / Leadership']))->save();
        (new Task(['taskgroup_id'=>6,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>7,'description'=>'Custom Desgin and Implement']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Implimentation']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Monthly Down/Up Load Refresh & Proofing data']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Presentation and Training']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Review Results & Alerts']))->save();
        (new Task(['taskgroup_id'=>7,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>8,'description'=>'Defining Prioritizing Rocks']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'Kick off']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'L10 Meetings']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'Wokring on Quarterly Rocks']))->save();
        (new Task(['taskgroup_id'=>8,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>9,'description'=>'Assess Future "What if" Value']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Plan of Action Define and Prioritze']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Report & Scoring Review']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Review Results']))->save();
        (new Task(['taskgroup_id'=>9,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>10,'description'=>'3 Year Financial Diagnostic Analysis']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Diagnostic']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Execute Agreements and Fee Structure']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Gain Access to System/Data and Historical Financials']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Interview Management Team']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Present Initial Findings']))->save();
        (new Task(['taskgroup_id'=>10,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>11,'description'=>'Controller Review']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Monthly Close Summit']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Process Improvement']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Reconcile GL Accounts']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Report Generation']))->save();
        (new Task(['taskgroup_id'=>11,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>12,'description'=>'13 Week Short Term Cash Flow']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'AOP']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'Quarterly Re-forecast of AOP']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'Strategic Planning 3-5+ Years']))->save();
        (new Task(['taskgroup_id'=>12,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>13,'description'=>'Process Flow Review & Design']))->save();
        (new Task(['taskgroup_id'=>13,'description'=>'Training & Monitoring']))->save();
        (new Task(['taskgroup_id'=>13,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>14,'description'=>'Assess Current Needs / Capabilities']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'Build Tools: Reporting & Process Automation']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'Evaluate ERP Vendors and Negotiate Pricing']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'System Implementation & Training']))->save();
        (new Task(['taskgroup_id'=>14,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>15,'description'=>'Assess Future "What if" Value']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Plan of Action Define and Prioritze']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Report & Scoring Review']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Review Results']))->save();
        (new Task(['taskgroup_id'=>15,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>16,'description'=>'Vendor Negotiation/Meetings']))->save();
        (new Task(['taskgroup_id'=>16,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>17,'description'=>'Monthly Close & Reporting']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Company Meeting']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Payroll Processing']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Tax Filings and Compliance']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Travel']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Billings & Collections']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Staff Search/Recruitment']))->save();
        (new Task(['taskgroup_id'=>17,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>18,'description'=>'Website Design']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Analytical Database Design']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'PlanGuru & Standard Models']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Dashboard Design']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Employee/Contractor Training']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Processes & Procedures']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Network, Servers, Computers']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Survey Design']))->save();
        (new Task(['taskgroup_id'=>18,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>19,'description'=>'Agency/Strategy Meeting']))->save();
        (new Task(['taskgroup_id'=>19,'description'=>'Meet Networking Partner/Group']))->save();
        (new Task(['taskgroup_id'=>19,'description'=>'Attend Event/Presentation']))->save();
        (new Task(['taskgroup_id'=>19,'description'=>'Collateral Design']))->save();
        (new Task(['taskgroup_id'=>19,'description'=>'Attend CEO Group']))->save();
        (new Task(['taskgroup_id'=>19,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>20,'description'=>'Meeting with Prospects']))->save();
        (new Task(['taskgroup_id'=>20,'description'=>'Prepare/Present Proposal']))->save();
        (new Task(['taskgroup_id'=>20,'description'=>'Scoring Review of Financials']))->save();
        (new Task(['taskgroup_id'=>20,'description'=>'Present Value Builder']))->save();
        (new Task(['taskgroup_id'=>20,'description'=>'Other']))->save();

        (new Task(['taskgroup_id'=>21,'description'=>'Recurring CFO Hourly Services']))->save();
        (new Task(['taskgroup_id'=>21,'description'=>'Recurring CFO Monthly Retainer']))->save();
//        (new Task(['taskgroup_id'=>21,'description'=>'Out of Scope Hourly Work']))->save();
//        (new Task(['taskgroup_id'=>21,'description'=>'One time Project - Hourly']))->save();
//        (new Task(['taskgroup_id'=>21,'description'=>'One Time Project - Flat Fee']))->save();

        (new Task(['taskgroup_id'=>22,'description'=>'Monthly Controller Recurring Services']))->save();
        (new Task(['taskgroup_id'=>22,'description'=>'Accounting Wellness Assessment']))->save();
        (new Task(['taskgroup_id'=>22,'description'=>'GL Catch-up/Clean-up']))->save();
        (new Task(['taskgroup_id'=>22,'description'=>'Process Re-Engineering']))->save();
//        (new Task(['taskgroup_id'=>22,'description'=>'Out of Scope Hourly Controller Services']))->save();
//        (new Task(['taskgroup_id'=>22,'description'=>'One time Project - Hourly']))->save();
//        (new Task(['taskgroup_id'=>22,'description'=>'One Time Project - Flat Fee']))->save();

        (new Task(['taskgroup_id'=>23,'description'=>'Dashboard Services - Onboarding']))->save();
        (new Task(['taskgroup_id'=>23,'description'=>'Dashboard Services - Retainer']))->save();
        (new Task(['taskgroup_id'=>23,'description'=>'Dashboard Services - Other']))->save();
        (new Task(['taskgroup_id'=>23,'description'=>'Reporting Portal']))->save();
        (new Task(['taskgroup_id'=>23,'description'=>'Investor Corral']))->save();
//        (new Task(['taskgroup_id'=>23,'description'=>'One time Project - Hourly']))->save();
//        (new Task(['taskgroup_id'=>23,'description'=>'One Time Project - Flat Fee']))->save();

        (new Task(['taskgroup_id'=>24,'description'=>'G.A.P.']))->save();
        (new Task(['taskgroup_id'=>24,'description'=>'i.V.O.S.']))->save();
//        (new Task(['taskgroup_id'=>24,'description'=>'Out of Scope Hourly Controller Services']))->save();
//        (new Task(['taskgroup_id'=>24,'description'=>'One Time Project - Flat Fee']))->save();
//        (new Task(['taskgroup_id'=>24,'description'=>'One time Project - Hourly']))->save();

        (new Task(['taskgroup_id'=>25,'description'=>'Accounting Assessment']))->save();
        (new Task(['taskgroup_id'=>25,'description'=>'Value Builder']))->save();
        (new Task(['taskgroup_id'=>25,'description'=>'Vision Goals & Accountability']))->save();
        (new Task(['taskgroup_id'=>25,'description'=>'Benchmarking Services']))->save();

        (new Task(['taskgroup_id'=>26,'description'=>'Business Development & Marketing']))->save();
        (new Task(['taskgroup_id'=>26,'description'=>'Systems & Infrastructure']))->save();
        (new Task(['taskgroup_id'=>26,'description'=>'Accounting, Payroll & Billings etc']))->save();
        (new Task(['taskgroup_id'=>26,'description'=>'Company Meetings & Events']))->save();
        (new Task(['taskgroup_id'=>26,'description'=>'Legal']))->save();
        (new Task(['taskgroup_id'=>26,'description'=>'Training']))->save();


        (new Task(['taskgroup_id'=>27,'description'=>'Out of Scope Hourly Service']))->save();
        (new Task(['taskgroup_id'=>27,'description'=>'One time Project - Hourly']))->save();
        (new Task(['taskgroup_id'=>27,'description'=>'One Time Project - Flat Fee']))->save();
        (new Task(['taskgroup_id'=>27,'description'=>'Travel']))->save();
        (new Task(['taskgroup_id'=>27,'description'=>'Other']))->save();
    }
}
