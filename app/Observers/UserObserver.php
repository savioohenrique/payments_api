<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\app\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $user->account()->create(['id' => Str::uuid(),  'balance' => 0]);
    }

}
