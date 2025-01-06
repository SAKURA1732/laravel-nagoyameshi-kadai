<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'representative' => $this->faker->name,
            'establishment_date' => $this->faker->date,
            'capital' => $this->faker->numberBetween(1000000, 10000000),
            'business' => $this->faker->company(),
            'number_of_employees' => $this->faker->numberBetween(10, 1000),
        ];
    }
}
