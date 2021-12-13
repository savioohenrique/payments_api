<?php

namespace App\Http\Controllers;

use App\Models\storekeeper;

class StorekeeperController extends Controller
{
    /**
     * Generate new storekeeper.
     * 
     * @OA\Post(
     *     path="/storekeeper",
     *     tags={"Storekeeper"},
     *     description="Return a new storekeeper.",
     *     @OA\Response(
     *         response=201, 
     *         description="New storekeeper."
     *     ),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function factory()
    {
        return storekeeper::factory()->create();
    }
}
