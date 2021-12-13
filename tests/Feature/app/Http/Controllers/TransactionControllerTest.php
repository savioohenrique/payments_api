<?php

namespace App\Http\Controllers;

use App\Models\Storekeeper;
use App\Models\User;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{

    public function testStorekeeperShouldNotRealizeTransaction()
    {
        $payer = Storekeeper::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_storekeeper'
        ];

        $authenticate = $this->post(route('authenticate', ['provider' => 'storekeeper']), $data);
        
        $token = json_decode($authenticate->getContent())->access_token;
        
        $baerer = "Bearer $token";

        $request = $this->post(
            route(
                'transaction', 
                ['provider' => 'storekeeper']
            ), 
            [
                'value' => 150.60, 
                'provider' => 'user', 
                'payee' => 'savio@email.teste'
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(403);
        $request->assertJson(['errors' => ['main' => 'Storekeeper is not allowed to realize a transaction']]);
    }

    public function testShouldNotMakeTransactionWithInvalidData()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        
        $baerer = "Bearer $token";

        $request = $this->post(
            route(
                'transaction', 
                ['provider' => 'storekeeper']
            ), 
            [
                'value' => '', 
                'provider' => 'storekeeper', 
                'payee' => 'savio-storekeeper@email.teste'
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(422);
        $request->assertJson(['errors' => ['main' => 'The given data was invalid.']]);
    }

    public function testShouldNotMakeTransactionForInvalidprovider()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        
        $baerer = "Bearer $token";

        $request = $this->post(
            route('transaction', 
                ['provider' => 'storekeeper']
            ), 
            [
                'value' => 150.80, 
                'provider' => 'owner', 
                'payee' => 'savio-storekeeper@email.teste'
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(422);
        $request->assertJson(['errors' => ['main' => 'Invalid user type for payee']]);
    }

    public function testPayerShouldNotDoTransactionForInvalidPayee()
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

        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => 45.00, 
                'provider' => 'storekeeper', 
                'payee' => 'invalidEmail@email.teste'
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(404);
        $request->assertJson(['errors' => ['main' => 'Payee not found']]);
    }
    
    public function testPayerShouldNotHaveEnoughMoney()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];

        $authenticate = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $token = json_decode($authenticate->getContent())->access_token;
        
        $baerer = "Bearer $token";

        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => 150.80, 
                'provider' => 'storekeeper', 
                'payee' => 'savio-storekeeper@email.teste'
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(403);
        $request->assertJson(['errors' => ['main' => 'Payer does not have enough money to make the transaction']]);
    }

    public function testShouldMakeTransactionToUser()
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

        $payee = User::factory()->create();

        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => 200.50, 
                'provider' => 'user', 
                'payee' => $payee->email
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(200);
    }

    public function testShouldMakeTransactionToStorekeeper()
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

        $payee = Storekeeper::factory()->create();

        $request = $this->post(
            route('transaction', 
                ['provider' => 'user']
            ), 
            [
                'value' => 200.50, 
                'provider' => 'storekeeper', 
                'payee' => $payee->email
            ], 
            ['Authorization' => $baerer]
        );

        $request->assertStatus(200);
    }

}