<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::firstOrCreate(
            ['document' => '00.000.000/0001-00'],
            [
                'name'               => 'DRS Matriz',
                'corporate_name'     => 'DRS Sistemas Ltda',
                'slug_name'          => 'drs-matriz',
                'document'           => '00.000.000/0001-00',
                'state_registration' => '000.000.000.000',
                'email'              => 'matriz@drs.com.br',
                'phone'              => '(11) 3000-0000',
                'zip_code'           => '01310-100',
                'street'             => 'Avenida Paulista',
                'number'             => '1000',
                'complement'         => 'Sala 101',
                'district'           => 'Bela Vista',
                'city'               => 'São Paulo',
                'state'              => 'SP',
                'is_active'          => true,
            ]
        );

        Branch::factory()->count(9)->create();
        Branch::factory()->inactive()->create();
    }
}
