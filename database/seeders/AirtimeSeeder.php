<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceField;
use App\Models\ServicePrice;
use Illuminate\Database\Seeder;

class AirtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or retrieve the 'Airtime' service
        $service = Service::firstOrCreate(
            ['name' => 'Airtime'],
            [
                'description' => 'Airtime VTU Top-up Services',
                'is_active' => true,
            ]
        );

        // 2. Define fields (networks) and pricing/discount configuration
        // In the database model, 'price' represents the discount percentage (e.g., 2.00 for 2% discount)
        $networks = [
            [
                'name' => 'MTN Airtime',
                'code' => 'mtn',
                'base_discount' => 1.50,
                'role_discounts' => [
                    'personal' => 1.50,
                    'agent'    => 2.00,
                    'business' => 2.50,
                    'partner'  => 3.00,
                ],
                'role_commissions' => [
                    'personal' => 0.00,
                    'agent'    => 0.00,
                    'business' => 0.00,
                    'partner'  => 0.00,
                ]
            ],
            [
                'name' => 'Airtel Airtime',
                'code' => 'airtel',
                'base_discount' => 1.50,
                'role_discounts' => [
                    'personal' => 1.50,
                    'agent'    => 2.00,
                    'business' => 2.50,
                    'partner'  => 3.00,
                ],
                'role_commissions' => [
                    'personal' => 0.00,
                    'agent'    => 0.00,
                    'business' => 0.00,
                    'partner'  => 0.00,
                ]
            ],
            [
                'name' => 'Glo Airtime',
                'code' => 'glo',
                'base_discount' => 2.00,
                'role_discounts' => [
                    'personal' => 2.00,
                    'agent'    => 2.50,
                    'business' => 3.00,
                    'partner'  => 3.50,
                ],
                'role_commissions' => [
                    'personal' => 0.00,
                    'agent'    => 0.00,
                    'business' => 0.00,
                    'partner'  => 0.00,
                ]
            ],
            [
                'name' => '9Mobile Etisalat Airtime',
                'code' => 'etisalat',
                'base_discount' => 2.50,
                'role_discounts' => [
                    'personal' => 2.50,
                    'agent'    => 3.00,
                    'business' => 3.50,
                    'partner'  => 4.00,
                ],
                'role_commissions' => [
                    'personal' => 0.00,
                    'agent'    => 0.00,
                    'business' => 0.00,
                    'partner'  => 0.00,
                ]
            ],
        ];

        foreach ($networks as $net) {
            // Check if field_code is already taken by another service to prevent unique key violation
            $fieldCode = $net['code'];
            if (ServiceField::where('field_code', $fieldCode)->where('service_id', '!=', $service->id)->exists()) {
                $fieldCode = 'airtime_' . $net['code'];
            }

            // Create/update the service field by unique field_code
            $field = ServiceField::updateOrCreate(
                [
                    'field_code' => $fieldCode,
                ],
                [
                    'service_id' => $service->id,
                    'field_name' => $net['name'],
                    'base_price' => $net['base_discount'],
                    'is_active' => true,
                ]
            );

            // Seed/update role-based custom discounts (which map to 'price' column)
            foreach ($net['role_discounts'] as $role => $discountValue) {
                $commissionValue = $net['role_commissions'][$role] ?? 0.00;
                ServicePrice::updateOrCreate(
                    [
                        'service_fields_id' => $field->id,
                        'user_type'         => $role,
                        'user_id'           => null,
                    ],
                    [
                        'service_id' => $service->id,
                        'price'      => $discountValue,
                        'commission' => $commissionValue,
                    ]
                );
            }
        }
    }
}
