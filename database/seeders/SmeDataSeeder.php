<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceField;
use App\Models\SmeData;
use Illuminate\Database\Seeder;

class SmeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the 'smedata' service exists
        $service = Service::firstOrCreate(
            ['name' => 'smedata'],
            [
                'description' => 'SME Data Subscription Services',
                'is_active' => true,
            ]
        );

        // 2. Create Service Fields for each network under 'smedata' service
        $networks = [
            'mtn' => 'MTN',
            'glo' => 'GLO',
            'airtel' => 'AIRTEL',
            '9mobile' => '9MOBILE',
        ];

        foreach ($networks as $code => $name) {
            // Check if field_code is already taken by another service to prevent unique key violation
            $fieldCode = $code;
            if (ServiceField::where('field_code', $fieldCode)->where('service_id', '!=', $service->id)->exists()) {
                $fieldCode = 'sme_' . $code;
            }

            ServiceField::firstOrCreate(
                [
                    'service_id' => $service->id,
                    'field_name' => $name,
                ],
                [
                    'field_code' => $fieldCode,
                    'base_price' => 0.00,
                    'is_active' => true,
                ]
            );
        }

        // 3. Seed Sample SME Data Plans
        $plans = [
            [
                'data_id' => 'AIRTEL_DG_40GB_ULTRA_ROUTER_30DAYS',
                'network' => 'AIRTEL',
                'plan_type' => 'GIFTING',
                'business_price' => 10000.00,
                'personal_price' => 10000.00,
                'agent_price' => 10000.00,
                'partner_price' => 10000.00,
                'size' => 'ULTRA PLANS ROUTER ONLY 40GB + 250MB DAILY',
                'validity' => '30',
                'status' => 'enabled',
            ],
            [
                'data_id' => '234',
                'network' => 'MTN',
                'plan_type' => 'GIFTING',
                'business_price' => 7150.00,
                'personal_price' => 7500.00,
                'agent_price' => 7200.00,
                'partner_price' => 7300.00,
                'size' => '35GB(GIFTING)',
                'validity' => '30',
                'status' => 'enabled',
            ],
            [
                'data_id' => 'AIRTEL_DG_40GB_ROUTER_30DAYS',
                'network' => 'AIRTEL',
                'plan_type' => 'GIFTING',
                'business_price' => 10000.00,
                'personal_price' => 10000.00,
                'agent_price' => 10000.00,
                'partner_price' => 10000.00,
                'size' => 'AIRTEL ROUTER PLANS 40GB',
                'validity' => '30',
                'status' => 'enabled',
            ],
            [
                'data_id' => '445',
                'network' => 'MTN',
                'plan_type' => 'GIFTING',
                'business_price' => 1950.00,
                'personal_price' => 1999.00,
                'agent_price' => 1998.00,
                'partner_price' => 1997.00,
                'size' => '7GB(GIFTING)',
                'validity' => '2',
                'status' => 'enabled',
            ],
            [
                'data_id' => '436',
                'network' => 'GLO',
                'plan_type' => 'SME',
                'business_price' => 1400.00,
                'personal_price' => 1450.00,
                'agent_price' => 1450.00,
                'partner_price' => 1450.00,
                'size' => '5GB SME',
                'validity' => '30',
                'status' => 'enabled',
            ],
        ];

        foreach ($plans as $plan) {
            SmeData::updateOrCreate(
                ['data_id' => $plan['data_id']],
                $plan
            );
        }
    }
}
