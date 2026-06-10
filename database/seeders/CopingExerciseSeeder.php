<?php

namespace Database\Seeders;

use App\Enums\CopingTheme;
use App\Models\CopingExercise;
use Illuminate\Database\Seeder;

class CopingExerciseSeeder extends Seeder
{
    /**
     * Seed a starter set of evidence-based coping exercises.
     */
    public function run(): void
    {
        foreach ($this->exercises() as $exercise) {
            CopingExercise::query()->updateOrCreate(
                ['title_en' => $exercise['title_en']],
                $exercise,
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function exercises(): array
    {
        return [
            [
                'type' => 'breathing',
                'themes' => [CopingTheme::Anxiety->value, CopingTheme::Stress->value, CopingTheme::Anger->value],
                'title_en' => '4-7-8 Breathing',
                'title_ar' => 'تنفّس 4-7-8',
                'summary_en' => 'A simple breathing pattern that calms the nervous system.',
                'summary_ar' => 'نمط تنفّس بسيط يهدّئ الجهاز العصبي.',
                'steps_en' => "1. Breathe in quietly through your nose for 4 seconds.\n2. Hold your breath for 7 seconds.\n3. Exhale slowly through your mouth for 8 seconds.\n4. Repeat 3-4 times.",
                'steps_ar' => "1. استنشق بهدوء من أنفك لمدة 4 ثوانٍ.\n2. احبس أنفاسك لمدة 7 ثوانٍ.\n3. ازفر ببطء من فمك لمدة 8 ثوانٍ.\n4. كرّر 3-4 مرات.",
                'duration_minutes' => 3,
                'sort_order' => 1,
            ],
            [
                'type' => 'grounding',
                'themes' => [CopingTheme::Anxiety->value, CopingTheme::Stress->value],
                'title_en' => '5-4-3-2-1 Grounding',
                'title_ar' => 'تأريض 5-4-3-2-1',
                'summary_en' => 'Reconnect with the present moment using your five senses.',
                'summary_ar' => 'أعد الاتصال باللحظة الحالية باستخدام حواسك الخمس.',
                'steps_en' => 'Name 5 things you can see, 4 you can touch, 3 you can hear, 2 you can smell, and 1 you can taste.',
                'steps_ar' => 'سمِّ 5 أشياء تراها، و4 يمكنك لمسها، و3 تسمعها، و2 تشمّها، وواحدًا تتذوّقه.',
                'duration_minutes' => 5,
                'sort_order' => 2,
            ],
            [
                'type' => 'behavioral',
                'themes' => [CopingTheme::Sadness->value, CopingTheme::Hopelessness->value],
                'title_en' => 'Behavioral Activation',
                'title_ar' => 'التنشيط السلوكي',
                'summary_en' => 'Schedule one small, meaningful activity to lift low mood.',
                'summary_ar' => 'خطّط لنشاط صغير ذي معنى لرفع المزاج المنخفض.',
                'steps_en' => "1. Pick one small activity that used to bring you joy or a sense of accomplishment.\n2. Schedule a specific time for it today.\n3. Do it even if you don't feel motivated — motivation often follows action.",
                'steps_ar' => "1. اختر نشاطًا صغيرًا كان يمنحك المتعة أو الإنجاز.\n2. حدّد وقتًا معيّنًا له اليوم.\n3. قم به حتى لو لم تشعر بالحافز — فالحافز غالبًا يأتي بعد الفعل.",
                'duration_minutes' => 10,
                'sort_order' => 3,
            ],
            [
                'type' => 'cognitive',
                'themes' => [CopingTheme::Hopelessness->value, CopingTheme::Sadness->value, CopingTheme::Anxiety->value],
                'title_en' => 'Thought Reframing',
                'title_ar' => 'إعادة صياغة الأفكار',
                'summary_en' => 'Challenge an unhelpful thought and find a more balanced one.',
                'summary_ar' => 'تحدَّ فكرة غير مفيدة وابحث عن فكرة أكثر توازنًا.',
                'steps_en' => "1. Write down the upsetting thought.\n2. Ask: what is the evidence for and against it?\n3. Write a kinder, more balanced alternative thought.",
                'steps_ar' => "1. اكتب الفكرة المزعجة.\n2. اسأل: ما الدليل المؤيّد والمعارض لها؟\n3. اكتب فكرة بديلة أكثر لطفًا وتوازنًا.",
                'duration_minutes' => 10,
                'sort_order' => 4,
            ],
            [
                'type' => 'behavioral',
                'themes' => [CopingTheme::Loneliness->value, CopingTheme::Sadness->value],
                'title_en' => 'Reach Out to One Person',
                'title_ar' => 'تواصل مع شخص واحد',
                'summary_en' => 'A small social step to counter loneliness.',
                'summary_ar' => 'خطوة اجتماعية صغيرة لمواجهة الشعور بالوحدة.',
                'steps_en' => "1. Think of one person you trust.\n2. Send them a short message or suggest a quick call.\n3. You don't need a reason — connection itself is the goal.",
                'steps_ar' => "1. فكّر في شخص واحد تثق به.\n2. أرسل له رسالة قصيرة أو اقترح مكالمة سريعة.\n3. لا تحتاج إلى سبب — التواصل نفسه هو الهدف.",
                'duration_minutes' => 5,
                'sort_order' => 5,
            ],
            [
                'type' => 'mindfulness',
                'themes' => [CopingTheme::Stress->value, CopingTheme::General->value],
                'title_en' => 'Body Scan',
                'title_ar' => 'مسح الجسد',
                'summary_en' => 'Release physical tension with a short mindful body scan.',
                'summary_ar' => 'تخلّص من التوتر الجسدي بمسح ذهني قصير للجسد.',
                'steps_en' => "1. Sit or lie comfortably and close your eyes.\n2. Slowly move your attention from your toes to your head.\n3. Notice any tension and gently let it soften with each breath.",
                'steps_ar' => "1. اجلس أو استلقِ بشكل مريح وأغمض عينيك.\n2. انقل انتباهك ببطء من أصابع قدميك إلى رأسك.\n3. لاحظ أي توتر ودعه يلين بلطف مع كل نفَس.",
                'duration_minutes' => 8,
                'sort_order' => 6,
            ],
        ];
    }
}
