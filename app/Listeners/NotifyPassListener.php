<?php

namespace newlifecfo\Listeners;

use newlifecfo\Events\ConsultantRecognizedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use newlifecfo\Notifications\ApplicationPassed;

class NotifyPassListener// implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ConsultantRecognizedEvent  $event
     * @return void
     */
    public function handle(ConsultantRecognizedEvent $event)
    {
        //
        $event->user->notify(new ApplicationPassed($event->user));
    }
}
