<?php

namespace App\Http\Controllers;

use App\Models\User;
use Tests\CreatesApplication;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use CreatesApplication;
    
    public function testUserShouldNotAuthenticateWithoutSendEmail()
    {
        $data = [
            'email' => '',
            'password' => 'password'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $request->assertStatus(401);
        $request->assertJson(['errors' => ['main' => 'The given data was invalid.']]);
    }
    
    public function testUserShouldNotAuthenticateWithoutSendPassword()
    {
        $data = [
            'email' => 'savio@email.teste',
            'password' => ''
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $request->assertStatus(401);
        $request->assertJson(['errors' => ['main' => 'The given data was invalid.']]);
    }

    public function testUserShouldNotAuthenticateWithInvalidToken()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];
        $payer->account->deposit(500.00);

        $baerer = "Bearer invalidToken";

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

        $request->assertJson(['status' => 'Token is Invalid']);
    }

    public function testUserShouldNotAuthenticateWithoutToken()
    {
        $payer = User::factory()->create();
        $data = [
            'email' => $payer->email,
            'password' => 'password_user'
        ];
        $payer->account->deposit(500.00);

        $token = "";
        $baerer = "Bearer $token";

        $request = $this->get(route('me', ['provider' => 'user']), ['Authorization' => $baerer]);

        $request->assertStatus(401);
        $request->assertJson(['status' => 'Authorization Token not found']);
    }

    public function testUserShouldgetMe()
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

        $request = $this->get(route('me', ['provider' => 'user']), ['Authorization' => $baerer]);

        $request->assertStatus(200);
    }
}
