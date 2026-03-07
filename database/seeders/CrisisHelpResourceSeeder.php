<?php

namespace Database\Seeders;

use App\Models\CrisisHelpResource;
use Illuminate\Database\Seeder;

class CrisisHelpResourceSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            [
                'type' => 'phone',
                'icon' => 'phone',
                'title_en' => 'Mental Health Hotline',
                'title_ar' => 'خط نجدة الصحة النفسية',
                'value' => '08008880700',
                'detail_en' => null,
                'detail_ar' => null,
                'url' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'website',
                'icon' => 'globe',
                'title_en' => 'Nefsy.com',
                'title_ar' => 'Nefsy.com',
                'value' => 'nefsy.com',
                'detail_en' => null,
                'detail_ar' => null,
                'url' => 'https://nefsy.com',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'facility',
                'icon' => 'hospital',
                'title_en' => 'University Health Unit',
                'title_ar' => 'الوحدة الصحية بالجامعة',
                'value' => '',
                'detail_en' => 'Contact the health unit at your university campus',
                'detail_ar' => 'تواصل مع الوحدة الصحية في حرمك الجامعي',
                'url' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($resources as $resource) {
            CrisisHelpResource::updateOrCreate(
                ['type' => $resource['type'], 'title_en' => $resource['title_en']],
                $resource,
            );
        }
    }
}
