<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class Phq9Seeder extends Seeder
{
    /**
     * Seed the PHQ-9 (Patient Health Questionnaire-9) with validated Arabic translations.
     */
    public function run(): void
    {
        $questionnaire = Questionnaire::updateOrCreate(
            ['type' => 'PHQ-9', 'version' => 1],
            [
                'name_ar' => 'استبيان صحة المريض-9',
                'name_en' => 'Patient Health Questionnaire-9 (PHQ-9)',
                'published_at' => now(),
            ]
        );

        $options = [
            ['label_en' => 'Not at all', 'label_ar' => 'لا على الإطلاق', 'value' => 0],
            ['label_en' => 'Several days', 'label_ar' => 'عدة أيام', 'value' => 1],
            ['label_en' => 'More than half the days', 'label_ar' => 'أكثر من نصف الأيام', 'value' => 2],
            ['label_en' => 'Nearly every day', 'label_ar' => 'تقريباً كل يوم', 'value' => 3],
        ];

        $questions = [
            [
                'text_en' => 'Little interest or pleasure in doing things',
                'text_ar' => 'قلة الاهتمام أو المتعة في القيام بالأشياء',
            ],
            [
                'text_en' => 'Feeling down, depressed, or hopeless',
                'text_ar' => 'الشعور بالإحباط أو الاكتئاب أو اليأس',
            ],
            [
                'text_en' => 'Trouble falling or staying asleep, or sleeping too much',
                'text_ar' => 'صعوبة في النوم أو البقاء نائماً، أو النوم أكثر من اللازم',
            ],
            [
                'text_en' => 'Feeling tired or having little energy',
                'text_ar' => 'الشعور بالتعب أو قلة الطاقة',
            ],
            [
                'text_en' => 'Poor appetite or overeating',
                'text_ar' => 'ضعف الشهية أو الإفراط في الأكل',
            ],
            [
                'text_en' => 'Feeling bad about yourself — or that you are a failure or have let yourself or your family down',
                'text_ar' => 'الشعور بالسوء تجاه نفسك — أو أنك فاشل أو خذلت نفسك أو عائلتك',
            ],
            [
                'text_en' => 'Trouble concentrating on things, such as reading the newspaper or watching television',
                'text_ar' => 'صعوبة في التركيز على الأشياء، مثل قراءة الصحيفة أو مشاهدة التلفزيون',
            ],
            [
                'text_en' => 'Moving or speaking so slowly that other people could have noticed? Or the opposite — being so fidgety or restless that you have been moving around a lot more than usual',
                'text_ar' => 'التحرك أو التحدث ببطء شديد لدرجة أن الآخرين لاحظوا ذلك؟ أو العكس — أن تكون متوتراً أو مضطرباً لدرجة أنك تتحرك أكثر من المعتاد',
            ],
            [
                'text_en' => 'Thoughts that you would be better off dead or of hurting yourself in some way',
                'text_ar' => 'أفكار بأنك ستكون أفضل حالاً لو كنت ميتاً أو بإيذاء نفسك بطريقة ما',
            ],
        ];

        foreach ($questions as $index => $questionData) {
            $question = $questionnaire->questions()->updateOrCreate(
                ['order' => $index + 1, 'questionnaire_id' => $questionnaire->id],
                [
                    'text_en' => $questionData['text_en'],
                    'text_ar' => $questionData['text_ar'],
                    'min_score' => 0,
                    'max_score' => 3,
                ]
            );

            foreach ($options as $optionIndex => $optionData) {
                $question->options()->updateOrCreate(
                    ['order' => $optionIndex, 'question_id' => $question->id],
                    [
                        'label_en' => $optionData['label_en'],
                        'label_ar' => $optionData['label_ar'],
                        'value' => $optionData['value'],
                    ]
                );
            }
        }
    }
}
