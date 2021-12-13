<?php

namespace App\Http\Controllers;

use App\Models\Storekeeper;
use App\Models\User;
use Tests\CreatesApplication;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use CreatesApplication;
    
    public function testUserShouldNotAuthenticateWithWrongprovider()
    {
        $data = [
            'email' => 'savio@email.teste',
            'password' => 'password'
        ];
        
        $request = $this->post(route('authenticate', ['provider' => 'seller']), $data);
        
        $request->assertStatus(422);
        $request->assertJson(['errors' => ['main' => 'Invalid user type']]);
    }
    
    public function testUserShouldNotAuthenticateWithWrongPassword()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'wrong_password'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $request->assertJson(['errors' => ['main' => 'Invalid credentials']]);
        $request->assertStatus(401);
    }
    
    public function testUserShouldNotBefound()
    {
        $data = [
            'email' => 'email@email.com',
            'password' => 'password_user'
        ];
        
        $request = $this->post(route('authenticate', ['provider' => 'user']), $data);
        
        $request->assertJson(['errors' => ['main' => 'Invalid credentials']]);
        $request->assertStatus(401);
    }

    public function testUserShouldAuthenticate()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'password_user'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $data);

        $request->assertStatus(200);
    }
    
    public function testStorekeeperShouldAuthenticate()
    {
        $user = Storekeeper::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'password_storekeeper'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'storekeeper']), $data);

        $request->assertStatus(200);
    }
}
