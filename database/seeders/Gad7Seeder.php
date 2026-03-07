<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class Gad7Seeder extends Seeder
{
    /**
     * Seed the GAD-7 (Generalized Anxiety Disorder-7) with validated Arabic translations.
     */
    public function run(): void
    {
        $questionnaire = Questionnaire::updateOrCreate(
            ['type' => 'GAD-7', 'version' => 1],
            [
                'name_ar' => 'مقياس اضطراب القلق العام-7',
                'name_en' => 'Generalized Anxiety Disorder-7 (GAD-7)',
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
                'text_en' => 'Feeling nervous, anxious, or on edge',
                'text_ar' => 'الشعور بالعصبية أو القلق أو التوتر',
            ],
            [
                'text_en' => 'Not being able to stop or control worrying',
                'text_ar' => 'عدم القدرة على التوقف عن القلق أو السيطرة عليه',
            ],
            [
                'text_en' => 'Worrying too much about different things',
                'text_ar' => 'القلق المفرط بشأن أشياء مختلفة',
            ],
            [
                'text_en' => 'Trouble relaxing',
                'text_ar' => 'صعوبة في الاسترخاء',
            ],
            [
                'text_en' => 'Being so restless that it\'s hard to sit still',
                'text_ar' => 'التوتر الشديد لدرجة صعوبة الجلوس بهدوء',
            ],
            [
                'text_en' => 'Becoming easily annoyed or irritable',
                'text_ar' => 'سهولة الانزعاج أو التهيج',
            ],
            [
                'text_en' => 'Feeling afraid as if something awful might happen',
                'text_ar' => 'الشعور بالخوف كأن شيئاً فظيعاً قد يحدث',
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
