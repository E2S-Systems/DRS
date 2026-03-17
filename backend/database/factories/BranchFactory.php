<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name'               => $name,
            'corporate_name'     => $name . ' ' . $this->faker->randomElement(['Ltda', 'S/A', 'ME', 'EIRELI', 'S.A.']),
            'slug_name'          => Str::slug($name),
            'document'           => $this->fakeCnpj(),
            'state_registration' => $this->faker->numerify('###.###.###.###'),
            'email'              => $this->faker->unique()->safeEmail(),
            'phone'              => $this->fakeBrazilianPhone(),
            'zip_code'           => $this->faker->numerify('#####-###'),
            'street'             => $this->faker->streetName(),
            'number'             => $this->faker->buildingNumber(),
            'complement'         => $this->faker->optional(0.4)->secondaryAddress(),
            'district'           => $this->faker->citySuffix(),
            'city'               => $this->faker->city(),
            'state'              => $this->faker->randomElement([
                'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
                'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
                'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
            ]),
            'is_active'          => true,
        ];
    }

    /**
     * State for an inactive branch.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Generate a formatted fake CNPJ (Brazilian company document).
     */
    private function fakeCnpj(): string
    {
        $n = $this->faker->numerify('##############');

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($n, 0, 2),
            substr($n, 2, 3),
            substr($n, 5, 3),
            substr($n, 8, 4),
            substr($n, 12, 2)
        );
    }

    /**
     * Generate a formatted fake Brazilian phone number.
     */
    private function fakeBrazilianPhone(): string
    {
        $ddd    = $this->faker->randomElement(['11', '21', '31', '41', '51', '61', '71', '81', '91']);
        $mobile = $this->faker->boolean(70);

        if ($mobile) {
            return sprintf('(%s) 9%s-%s', $ddd, $this->faker->numerify('####'), $this->faker->numerify('####'));
        }

        return sprintf('(%s) %s-%s', $ddd, $this->faker->numerify('####'), $this->faker->numerify('####'));
    }
}
