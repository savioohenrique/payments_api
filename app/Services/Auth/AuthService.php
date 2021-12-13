<?php

namespace App\Services\Auth;

use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\AuthRepository;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use App\Repositories\StorekeeperRepository;
use App\Repositories\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

Class AuthService {

    private AuthRepository $authRepository;

    /**
     * Class constructor.
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function authenticate(Request $request, string $provider)
    {
        $userTypeRepository = $this->getUserTypeRepository($provider);

        $credentials = $request->only(['email', 'password']);

        $user = $userTypeRepository->findUser($credentials['email']);
        
        if (!$user){
            throw new UserNotFoundException('Invalid credentials', 401);
        }
        
        if (!$this->authRepository->validatePassword($userTypeRepository, $credentials)) {
            throw new AuthorizationException('Invalid credentials', 403);
        }

        $auth = $userTypeRepository->getAuth();
        
        $token = $auth->attempt($credentials);
        
        return $this->respondWithToken($auth, $token);
    }

    public function getUserTypeRepository(string $userType): UserTypeRepositoryInterface
    {
        if ($userType == "user") {
            return new UserRepository();
        } else if ($userType == "storekeeper") {
            return new StorekeeperRepository();
        }
        
        throw new InvalidUserTypeException('Invalid user type');
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($auth, $token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60
        ];
    }
}
