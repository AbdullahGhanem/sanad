<?php

namespace Database\Seeders;

use App\Models\KnowledgeArticle;
use Illuminate\Database\Seeder;

class KnowledgeArticleSeeder extends Seeder
{
    /**
     * Seed a starter set of vetted psychoeducation articles.
     *
     * Embeddings are generated asynchronously by the KnowledgeArticleObserver once
     * VOYAGEAI_API_KEY is configured and a queue worker is running.
     */
    public function run(): void
    {
        foreach ($this->articles() as $article) {
            KnowledgeArticle::query()->updateOrCreate(
                ['title_en' => $article['title_en']],
                $article,
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function articles(): array
    {
        return [
            [
                'category' => 'depression',
                'title_en' => 'Understanding Depression',
                'title_ar' => 'فهم الاكتئاب',
                'body_en' => 'Depression is more than feeling sad. It is a common, treatable condition that can affect sleep, appetite, energy, and concentration. It is not a sign of weakness or a personal failing. Small, consistent steps — regular sleep, gentle activity, and staying connected to people you trust — can help, and professional support is effective when symptoms persist for more than two weeks.',
                'body_ar' => 'الاكتئاب أكثر من مجرد الشعور بالحزن. إنها حالة شائعة وقابلة للعلاج يمكن أن تؤثر على النوم والشهية والطاقة والتركيز. إنه ليس علامة ضعف أو فشل شخصي. يمكن أن تساعد الخطوات الصغيرة والمنتظمة — النوم المنتظم والنشاط اللطيف والبقاء على تواصل مع من تثق بهم. والدعم المهني فعّال عندما تستمر الأعراض أكثر من أسبوعين.',
            ],
            [
                'category' => 'anxiety',
                'title_en' => 'Managing Anxiety and Panic',
                'title_ar' => 'التعامل مع القلق والذعر',
                'body_en' => 'Anxiety is the body\'s alarm system reacting as if there is danger. A racing heart, fast breathing, and racing thoughts are uncomfortable but not harmful, and they pass. Slowing your breathing, naming what you can see and hear around you, and reminding yourself that the feeling will peak and fade can all reduce the intensity. If anxiety regularly disrupts your studies or sleep, talking to a counselor helps.',
                'body_ar' => 'القلق هو نظام إنذار الجسم الذي يتفاعل كأن هناك خطرًا. تسارع ضربات القلب والتنفس السريع والأفكار المتسارعة غير مريحة لكنها غير ضارة، وهي تزول. إبطاء التنفس، وتسمية ما تراه وتسمعه حولك، وتذكير نفسك بأن الشعور سيبلغ ذروته ثم يخفّ، كلها تقلل من الحدة. إذا كان القلق يعطّل دراستك أو نومك بانتظام، فإن التحدث مع مختص يساعد.',
            ],
            [
                'category' => 'sleep',
                'title_en' => 'Sleep Hygiene for Students',
                'title_ar' => 'نظافة النوم للطلاب',
                'body_en' => 'Good sleep supports mood, memory, and focus. Try to keep a consistent wake-up time, get daylight in the morning, and avoid caffeine in the afternoon. Screens late at night make it harder to fall asleep, so wind down with something calm instead. If you cannot sleep after 20 minutes, get up, do something quiet, and return to bed when drowsy rather than lying awake.',
                'body_ar' => 'النوم الجيد يدعم المزاج والذاكرة والتركيز. حاول الحفاظ على وقت استيقاظ ثابت، والتعرض لضوء النهار في الصباح، وتجنب الكافيين بعد الظهر. الشاشات في وقت متأخر من الليل تجعل النوم أصعب، لذا استرخِ بشيء هادئ بدلاً من ذلك. إذا لم تستطع النوم بعد 20 دقيقة، انهض وافعل شيئًا هادئًا، وعد إلى السرير عندما تشعر بالنعاس بدلاً من الاستلقاء مستيقظًا.',
            ],
            [
                'category' => 'stress',
                'title_en' => 'Coping with Exam Stress',
                'title_ar' => 'التعامل مع ضغط الامتحانات',
                'body_en' => 'Some stress before exams is normal and can even sharpen focus. It becomes a problem when it overwhelms you. Break revision into small sessions with short breaks, study the hardest material when your energy is highest, and protect your sleep the night before — it matters more than one extra hour of cramming. Remind yourself that one exam does not define your worth or your future.',
                'body_ar' => 'بعض التوتر قبل الامتحانات أمر طبيعي وقد يزيد التركيز. يصبح مشكلة عندما يغمرك. قسّم المراجعة إلى جلسات صغيرة مع فترات راحة قصيرة، وادرس أصعب المواد عندما تكون طاقتك في أعلى مستوياتها، واحمِ نومك في الليلة السابقة — فهو أهم من ساعة إضافية من الحفظ. ذكّر نفسك أن امتحانًا واحدًا لا يحدد قيمتك أو مستقبلك.',
            ],
            [
                'category' => 'help',
                'title_en' => 'When and How to Seek Help',
                'title_ar' => 'متى وكيف تطلب المساعدة',
                'body_en' => 'Reaching out is a sign of strength, not weakness. Consider seeking help if difficult feelings last more than two weeks, interfere with daily life, or include thoughts of harming yourself. You can start with someone you trust, a university counselor, or a mental-health hotline. Support is confidential, and you do not need to be in crisis to deserve it.',
                'body_ar' => 'طلب المساعدة علامة قوة وليست ضعفًا. فكّر في طلب المساعدة إذا استمرت المشاعر الصعبة أكثر من أسبوعين، أو أثّرت على حياتك اليومية، أو تضمّنت أفكارًا لإيذاء نفسك. يمكنك البدء مع شخص تثق به، أو مرشد جامعي، أو خط مساعدة نفسية. الدعم سرّي، ولست بحاجة لأن تكون في أزمة لتستحقه.',
            ],
        ];
    }
}
