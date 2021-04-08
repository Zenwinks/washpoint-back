<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = User::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $firstName = $this->faker->firstName;
    $lastName = $this->faker->lastName;
    $username = strtolower(substr($firstName, 0, 1) . $lastName);
    $username = str_replace(' ', '', $username);
    $username = preg_replace('/[^A-Za-z0-9\-]/', '', $username);
    $email = $username . "@example.com";
    return [
      'firstname' => $firstName,
      'lastname' => $lastName,
      'roles' => json_encode([]),
      'email' => $email,
      'email_verified_at' => now(),
      'password' => Hash::make("test"), // test
      'remember_token' => Str::random(10),
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  public function unverified()
  {
    return $this->state(function (array $attributes) {
      return [
        'email_verified_at' => null,
      ];
    });
  }
}
