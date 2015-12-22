<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class UserTest extends TestCase
{
	use DatabaseTransactions;
	/**
	 * Check the profile update system
	 * @test
	 * @return void
	 */
	public function testNewUser()
	{
		$user = factory(User::class)->create();
		$this->seeInDatabase('users', ['email' => $user->email]);
	}
}
