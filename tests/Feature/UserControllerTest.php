<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game; 


class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    // register method

    public function testEmailValidation(){

        $user = User::factory()->create();

        $user_data = [
            'name' => 'pavel',
            'email' => $user->email,
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "email" => ["The email is already registered."]
            ]
        ]);
    }
    

   
    //logout
    public function testLogout(){

        $user = new User();
        $user->name = 'pamiral';
        $user->email = 'admin@hotmail.com';
        $user->password = bcrypt('987654321');
        $user->save();

        $this->actingAs($user);

        $response = $this->post('/api/logout');

        $response->assertStatus(302);

    }

    
   
   

    // game
    public function testListPlayers(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        User::factory(5)->create();

        $response = $this->get('/api/players');

        $response->assertStatus(200);

        $response->assertJsonStructure(['users']);

        $response->assertJsonCount(8, 'users');
    }

    public function testWinner(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'name',
            'Wins rate',
            'Wins',
            'Total Games',
        ]);
    }
    public function testLoser(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        $response = $this->get('/api/players/ranking/loser');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'name',
            'Wins rate',
            'Wins',
            'Total Games',
        ]);
    }
    
}