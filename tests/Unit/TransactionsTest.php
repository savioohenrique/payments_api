<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Storekeeper;
use App\Models\User;
use Tests\CreatesApplication;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use CreatesApplication;

    public function testTransactionValueMustBeWithdrawn()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];
        $payerDeposit = 500.00;
        $payer->account->deposit($payerDeposit);

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        $baerer = "Bearer $token";

        $storekeeper = Storekeeper::factory()->create();
        $valueTransaction = 315.50;
        
        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => $valueTransaction, 
                'provider' => 'storekeeper', 
                'payee' => $storekeeper->email
            ], 
            ['Authorization' => $baerer]
        );

        $transaction = json_decode($request->content());
        $payerAccount = $transaction->payer_account;

        $this->assertEquals($payerAccount->balance, $payerDeposit - $valueTransaction);
    }

    public function testTransactionValueMustBeWiDeposited()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];
        $payerDeposit = 500.00;
        $payer->account->deposit($payerDeposit);

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        $baerer = "Bearer $token";

        $storekeeper = Storekeeper::factory()->create();
        $valueTransaction = 170.50;
        
        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => $valueTransaction, 
                'provider' => 'storekeeper', 
                'payee' => $storekeeper->email
            ], 
            ['Authorization' => $baerer]
        );

        $transaction = json_decode($request->content());
        $payeeAccount = $transaction->payee_account;

        $this->assertEquals($payeeAccount->balance, $valueTransaction);
    }

    public function testTransactionValueShouldBeBiggerThanValue()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];
        $payer->account->deposit(500.00);

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        $baerer = "Bearer $token";

        $storekeeper = Storekeeper::factory()->create();
        $value = 200.50;

        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => $value, 
                'provider' => 'storekeeper', 
                'payee' => $storekeeper->email
            ], 
            ['Authorization' => $baerer]
        );

        $this->assertNotEquals($request->content(), $value - 0.01);
    }

    public function testTransactionValueShouldBeSmallerThanValue()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];
        $payer->account->deposit(500.00);

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        $baerer = "Bearer $token";

        $storekeeper = Storekeeper::factory()->create();
        $value = 80.15;

        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => $value, 
                'provider' => 'storekeeper', 
                'payee' => $storekeeper->email
            ], 
            ['Authorization' => $baerer]
        );

        $this->assertNotEquals($request->content(), $value + 0.01);
    }
    
}