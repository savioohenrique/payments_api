<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\UserNotFoundException;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private AuthService $authService;
    
    /**
     * Class constructor.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * Get a JWT via given credentials.
     * 
     * @OA\Post(
     *     path="/auth/{provider}",
     *     tags={"Auth"},
     *     description="Authenticate user and return a JWT token.",
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         description="Provider (user | storekeeper)",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         style="form"    
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="mypassword"),
     *         ),
     *    ),
     *     @OA\Response(
     *         response=200, 
     *         description="User authenticated"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid Credentials"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid provider"
     *     ),
     * )
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request, string $provider)
    {
        try {
            $this->validate($request,['email' => 'required|email', 'password' => 'required']);

            $result = $this->authService->authenticate($request, $provider);
            
            return response()->json($result, 200);
        } catch (ValidationException | AuthorizationException | UserNotFoundException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 401);
        } catch (InvalidUserTypeException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 422);
        }
    }

    /**
     * Get the authenticated User.
     * 
     * @OA\Get(
     *     path="/me/{provider}",
     *     tags={"Auth"},
     *     description="Return the authenticated user.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         description="Provider (users | storekeepers)",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         style="form"    
     *     ),
     *     @OA\Response(
     *         response=200, 
     *         description="Authenticated user"
     *     ),
     *     @OA\Response(
     *         response=401, 
     *         description="Unauthorized"
     *     ),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(string $provider)
    {
        return response()->json(auth($provider)->user());
    }

}
