<?php

namespace App\Observers;

use App\Models\Storekeeper;
use Illuminate\Support\Str;

class StorekeeperObserver
{
    /**
     * Handle the Storekeeper "created" event.
     *
     * @param  \App\Models\Storekeeper  $storekeeper
     * @return void
     */
    public function created(Storekeeper $storekeeper)
    {
        $storekeeper->account()->create(['id' => Str::uuid() , 'balance' => 0]);
    }

}
