<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class P2PSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::firstOrCreate(
            ['name' => 'P2P Transfer'],
            [
                'description' => 'Peer-to-Peer Wallet Transfer Service',
                'is_active' => true,
            ]
        );
    }
}
