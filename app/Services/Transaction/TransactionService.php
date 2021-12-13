<?php

namespace App\Services\Transaction;

use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\RefusedTransactionException;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use App\Repositories\StorekeeperRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

Class TransactionService
{
    /**
     * Class constructor.
     */
    public function __construct(TransactionRepository $transactionRepository, UserRepository $userRepository, StorekeeperRepository $storekeeperRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->storekeeperRepository = $storekeeperRepository;
    }

    public function realizeTransaction(Request $request)
    {
        $data = $request->only(['payee', 'value']);
        
        if (!$this->isUserAbleToDoTransaction()) {
            throw new RefusedTransactionException('Storekeeper is not allowed to realize a transaction');
        }

        $payeeRepository = $this->getPayeeTypeRepository($request['provider']);
        
        return $this->transactionRepository->realizeTransaction($payeeRepository, $data);    
    }

    public function isUserAbleToDoTransaction()
    {
        return auth('users')->check();
    }

    public function getPayeeTypeRepository(string $provider): UserTypeRepositoryInterface
    {
        if ($provider == "user") {
            return $this->userRepository;
        } else if ($provider == "storekeeper") {
            return $this->storekeeperRepository;
        }
        
        throw new InvalidUserTypeException('Invalid user type for payee');
    }
}