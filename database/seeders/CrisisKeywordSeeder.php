<?php

namespace Database\Seeders;

use App\Models\CrisisKeyword;
use Illuminate\Database\Seeder;

class CrisisKeywordSeeder extends Seeder
{
    public function run(): void
    {
        $keywords = [
            // English crisis keywords
            ['phrase' => 'kill myself', 'language' => 'en'],
            ['phrase' => 'want to die', 'language' => 'en'],
            ['phrase' => 'end my life', 'language' => 'en'],
            ['phrase' => 'suicide', 'language' => 'en'],
            ['phrase' => 'suicidal', 'language' => 'en'],
            ['phrase' => 'self-harm', 'language' => 'en'],
            ['phrase' => 'hurt myself', 'language' => 'en'],
            ['phrase' => 'cutting myself', 'language' => 'en'],
            ['phrase' => 'no reason to live', 'language' => 'en'],
            ['phrase' => 'better off dead', 'language' => 'en'],
            ['phrase' => 'wish i was dead', 'language' => 'en'],
            ['phrase' => 'not worth living', 'language' => 'en'],

            // Arabic crisis keywords
            ['phrase' => 'أقتل نفسي', 'language' => 'ar'],
            ['phrase' => 'أريد أن أموت', 'language' => 'ar'],
            ['phrase' => 'إنهاء حياتي', 'language' => 'ar'],
            ['phrase' => 'انتحار', 'language' => 'ar'],
            ['phrase' => 'انتحر', 'language' => 'ar'],
            ['phrase' => 'إيذاء نفسي', 'language' => 'ar'],
            ['phrase' => 'أؤذي نفسي', 'language' => 'ar'],
            ['phrase' => 'لا سبب للعيش', 'language' => 'ar'],
            ['phrase' => 'أفضل لو كنت ميت', 'language' => 'ar'],
            ['phrase' => 'الحياة لا تستحق', 'language' => 'ar'],
            ['phrase' => 'أتمنى الموت', 'language' => 'ar'],
            ['phrase' => 'جرح نفسي', 'language' => 'ar'],
        ];

        foreach ($keywords as $keyword) {
            CrisisKeyword::updateOrCreate(
                ['phrase' => $keyword['phrase'], 'language' => $keyword['language']],
                ['is_active' => true],
            );
        }
    }
}
