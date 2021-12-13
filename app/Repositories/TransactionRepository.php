<?php

namespace App\Repositories;

use App\Exceptions\NotEnoughMoneyException;
use App\Exceptions\UnavailableServiceException;
use App\Exceptions\UserNotFoundException;
use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

Class TransactionRepository 
{
    /**
     * Class constructor.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function realizeTransaction(UserTypeRepositoryInterface $payeeRepository, Array $data)
    {
        $payerAccount = auth('users')->user()->account;
        
        if (!$this->checkPayerMoney($payerAccount, $data['value'])) {
            throw new NotEnoughMoneyException('Payer does not have enough money to make the transaction');
        }

        $payee = $payeeRepository->findUser($data['payee']);
        
        if (!$payee) {
            throw new UserNotFoundException('Payee not found');
        }

        $payeeAccount = $payee->account;

        $payload = [
            'id' => Str::uuid(),
            'payer_account_id' => $payerAccount->id,
            'payee_account_id' => $payeeAccount->id,
            'value' => $data['value']
        ];

        if (!$this->isAuthorizedtoMakeTransaction()) {
            throw new UnavailableServiceException('Authorization service is unavailable');
        }
        
        $transaction =  $this->makeTransaction($payload);

        $this->notifyPayee($payee);

        return $transaction;
    }

    public function checkPayerMoney(Account $account, $value)
    {
        return $value <= $account->balance;
    }

    public function isAuthorizedtoMakeTransaction()
    {
        $client = new Client();
        $uri = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        try {
            $response = $client->get($uri);
            $result = json_decode($response->getBody(), true);
            
            return $result['message'] == 'Autorizado';
        } catch (GuzzleException $exception) {
            return false;
        }
    }

    private function notifyPayee()
    {
        $client = new Client();
        $uri = 'http://o4d9z.mocklab.io/notify';
        try {
            $client->request('get', $uri);
        } catch (GuzzleException $exception) {
            //
        }
    }

    private function makeTransaction(Array $payload)
    {
        $transaction = DB::transaction(function () use ($payload) {
            $transaction = Transaction::create($payload);

            $transaction->payerAccount->withdraw($payload['value']);
            $transaction->payeeAccount->deposit($payload['value']);

            return $transaction;
        });

        return $transaction;
    } 

}