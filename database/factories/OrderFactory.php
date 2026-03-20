<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'stripe_session_id' => 'cs_test_'.$this->faker->unique()->uuid(),
            'stripe_payment_intent' => null,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'status' => 'pending',
            'total' => $this->faker->numberBetween(1000, 100000),
            'shipping_address' => null,
            'notes' => null,
        ];
    }
}
