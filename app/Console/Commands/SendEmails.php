<?php

namespace newlifecfo\Console\Commands;

use Illuminate\Console\Command;
use newlifecfo\Models\Consultant;
use newlifecfo\Notifications\ConfirmReports;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send confirmation email to all consultants';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        Consultant::recognized()->each(function ($consultant) {
//            if (($consultant->first_name == 'Hao' && $consultant->last_name == 'Xiong')
//                || ($consultant->first_name == 'John' && $consultant->last_name == 'Doe'))
                $user = $consultant->user;
                $user->notify(new ConfirmReports($user));

        });

    }
}
