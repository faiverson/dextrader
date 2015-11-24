<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use Faker\Provider\en_US\PhoneNumber;
use App\Models\User;

class ProfileUpdateTest extends TestCase
{
//	use DatabaseTransactions;
	use WithoutMiddleware;
    /**
     * Check the profile update system
	 * @test
     * @return void
     */
    public function testProfileUpdate()
    {
		$user = factory(User::class)->create();
		$this->seeInDatabase('users', ['email' => $user->email]);
		$faker = Faker::create();
		$faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

		$updated = [
			'id' => $user->id,
			'first_name' => $faker->firstName,
			'last_name' => $faker->lastName,
			'email' => $faker->email,
			'phone' => $faker->phoneNumberCleared
		];

		$this->withoutMiddleware();
		$response = $this->call('put', '/api/users', $updated);
		$this->assertEquals(200, $response->status());
//		dd($response->status());
//		$this->assertResponseOk();
//		$this->seeInDatabase('users', $updated);
    }
}
