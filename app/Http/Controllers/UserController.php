<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
 
    /**
     * Generate new user.
     * 
     * @OA\Post(
     *     path="/user",
     *     tags={"User"},
     *     description="Return a new user.",
     *     @OA\Response(
     *         response=201, 
     *         description="New user."
     *     ),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function factory()
    {
        $user = User::factory()->create();
        $user->account->deposit(500.00);
        
        return $user;
    }

}
