<?php

namespace App\Listeners;

use App\Events\UserSaved;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserSavedListener
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
     * @param  \App\Events\UserSaved  $event
     * @return void
     */
    public function handle(UserSaved $event)
    {
        $user = $event->user;

        // check phone number changed
        if ($user->phone_number != $user->getOriginal('phone_number')) {
            $user->phone_number_verified_at = null;
            $user->saveQuietly();
        }

    }
}
