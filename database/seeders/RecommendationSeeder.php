<?php

namespace Database\Seeders;

use App\Models\Recommendation;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $recommendations = [
            // Minimal severity (PHQ-9: 0-4, GAD-7: 0-4)
            [
                'title_en' => 'Maintain Your Well-Being',
                'title_ar' => 'حافظ على صحتك النفسية',
                'body_en' => 'Your screening suggests minimal distress. Continue practicing healthy habits like regular exercise, adequate sleep, and staying connected with friends and family.',
                'body_ar' => 'يشير الفحص إلى ضائقة بسيطة. استمر في ممارسة العادات الصحية مثل التمارين المنتظمة والنوم الكافي والتواصل مع الأصدقاء والعائلة.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 4, 'min_gad7' => 0, 'max_gad7' => 4,
            ],
            [
                'title_en' => 'Mindfulness & Relaxation',
                'title_ar' => 'التأمل والاسترخاء',
                'body_en' => 'Practice mindfulness or deep breathing exercises for 5-10 minutes daily to maintain your mental balance.',
                'body_ar' => 'مارس التأمل أو تمارين التنفس العميق لمدة 5-10 دقائق يومياً للحفاظ على توازنك النفسي.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 4, 'min_gad7' => 0, 'max_gad7' => 4,
            ],

            // Mild depression (PHQ-9: 5-9)
            [
                'title_en' => 'Physical Activity for Mood',
                'title_ar' => 'النشاط البدني لتحسين المزاج',
                'body_en' => 'Regular physical activity, even a 30-minute walk, can significantly improve mood. Try to exercise 3-4 times per week.',
                'body_ar' => 'النشاط البدني المنتظم، حتى المشي لمدة 30 دقيقة، يمكن أن يحسن المزاج بشكل كبير. حاول ممارسة الرياضة 3-4 مرات أسبوعياً.',
                'url' => null,
                'min_phq9' => 5, 'max_phq9' => 9, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Talk to Someone You Trust',
                'title_ar' => 'تحدث مع شخص تثق به',
                'body_en' => 'Sharing your feelings with a trusted friend, family member, or mentor can help lighten emotional burden.',
                'body_ar' => 'مشاركة مشاعرك مع صديق موثوق أو أحد أفراد العائلة أو مرشد يمكن أن يخفف من العبء العاطفي.',
                'url' => null,
                'min_phq9' => 5, 'max_phq9' => 9, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Establish a Routine',
                'title_ar' => 'ضع روتيناً يومياً',
                'body_en' => 'A consistent daily routine with regular sleep, meals, and study times can provide structure and improve your mood.',
                'body_ar' => 'روتين يومي منتظم يشمل النوم والوجبات وأوقات الدراسة يمكن أن يوفر هيكلاً ويحسن مزاجك.',
                'url' => null,
                'min_phq9' => 5, 'max_phq9' => 9, 'min_gad7' => 0, 'max_gad7' => 21,
            ],

            // Mild anxiety (GAD-7: 5-9)
            [
                'title_en' => 'Progressive Muscle Relaxation',
                'title_ar' => 'استرخاء العضلات التدريجي',
                'body_en' => 'Practice tensing and releasing muscle groups to reduce physical tension associated with anxiety.',
                'body_ar' => 'مارس شد وإرخاء مجموعات العضلات لتقليل التوتر الجسدي المرتبط بالقلق.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 27, 'min_gad7' => 5, 'max_gad7' => 9,
            ],
            [
                'title_en' => 'Limit Caffeine & Social Media',
                'title_ar' => 'قلل من الكافيين ووسائل التواصل',
                'body_en' => 'Reducing caffeine intake and limiting social media use, especially before bed, can help reduce anxiety symptoms.',
                'body_ar' => 'تقليل تناول الكافيين والحد من استخدام وسائل التواصل الاجتماعي، خاصة قبل النوم، يمكن أن يساعد في تقليل أعراض القلق.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 27, 'min_gad7' => 5, 'max_gad7' => 9,
            ],

            // Moderate severity (PHQ-9: 10-14 or GAD-7: 10-14)
            [
                'title_en' => 'University Counseling Services',
                'title_ar' => 'خدمات الإرشاد الجامعي',
                'body_en' => 'Your university offers free counseling services. Consider booking an appointment with a counselor who can provide professional guidance.',
                'body_ar' => 'تقدم جامعتك خدمات إرشاد مجانية. فكر في حجز موعد مع مرشد يمكنه تقديم توجيه مهني.',
                'url' => null,
                'min_phq9' => 10, 'max_phq9' => 14, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Structured Problem-Solving',
                'title_ar' => 'حل المشكلات المنظم',
                'body_en' => 'Break down overwhelming problems into smaller, manageable steps. Tackling one thing at a time reduces feelings of being overwhelmed.',
                'body_ar' => 'قسّم المشكلات الكبيرة إلى خطوات أصغر يمكن إدارتها. معالجة شيء واحد في كل مرة يقلل من الشعور بالإرهاق.',
                'url' => null,
                'min_phq9' => 10, 'max_phq9' => 14, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Anxiety Management Techniques',
                'title_ar' => 'تقنيات إدارة القلق',
                'body_en' => 'Try grounding techniques like the 5-4-3-2-1 method: identify 5 things you see, 4 you touch, 3 you hear, 2 you smell, and 1 you taste.',
                'body_ar' => 'جرب تقنيات التأريض مثل طريقة 5-4-3-2-1: حدد 5 أشياء تراها، 4 تلمسها، 3 تسمعها، 2 تشمها، و1 تتذوقها.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 27, 'min_gad7' => 10, 'max_gad7' => 14,
            ],

            // Moderately severe (PHQ-9: 15-19)
            [
                'title_en' => 'Seek Professional Support',
                'title_ar' => 'اطلب الدعم المتخصص',
                'body_en' => 'Your results suggest moderately severe symptoms. We strongly recommend speaking with a mental health professional. Your university health center is a good starting point.',
                'body_ar' => 'تشير نتائجك إلى أعراض متوسطة إلى شديدة. نوصي بشدة بالتحدث مع متخصص في الصحة النفسية. المركز الصحي في جامعتك نقطة بداية جيدة.',
                'url' => null,
                'min_phq9' => 15, 'max_phq9' => 19, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Nefsy Online Platform',
                'title_ar' => 'منصة نفسي الإلكترونية',
                'body_en' => 'Nefsy.com offers online mental health support and resources specifically for Arabic-speaking communities.',
                'body_ar' => 'تقدم منصة نفسي دعماً وموارد للصحة النفسية عبر الإنترنت مخصصة للمجتمعات الناطقة بالعربية.',
                'url' => 'https://nefsy.com',
                'min_phq9' => 15, 'max_phq9' => 27, 'min_gad7' => 0, 'max_gad7' => 21,
            ],

            // Severe (PHQ-9: 20-27 or GAD-7: 15-21)
            [
                'title_en' => 'Urgent: Contact a Professional',
                'title_ar' => 'عاجل: تواصل مع متخصص',
                'body_en' => 'Your results indicate severe distress. Please reach out to a mental health professional as soon as possible. Call the Egypt Mental Health Hotline: 08008880700.',
                'body_ar' => 'تشير نتائجك إلى ضائقة شديدة. يرجى التواصل مع متخصص في الصحة النفسية في أقرب وقت ممكن. اتصل بخط نجدة الصحة النفسية: 08008880700.',
                'url' => null,
                'min_phq9' => 20, 'max_phq9' => 27, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Egypt Mental Health Hotline',
                'title_ar' => 'خط نجدة الصحة النفسية في مصر',
                'body_en' => 'Free, confidential mental health support is available 24/7. Call 08008880700 to speak with a trained counselor.',
                'body_ar' => 'دعم مجاني وسري للصحة النفسية متاح على مدار الساعة. اتصل بـ 08008880700 للتحدث مع مرشد مدرب.',
                'url' => null,
                'min_phq9' => 20, 'max_phq9' => 27, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Severe Anxiety: Get Help Now',
                'title_ar' => 'قلق شديد: احصل على المساعدة الآن',
                'body_en' => 'Severe anxiety can be debilitating, but it is treatable. A mental health professional can help you develop an effective management plan.',
                'body_ar' => 'القلق الشديد يمكن أن يكون معوقاً، لكنه قابل للعلاج. يمكن لمتخصص الصحة النفسية مساعدتك في وضع خطة إدارة فعالة.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 27, 'min_gad7' => 15, 'max_gad7' => 21,
            ],

            // General (all ranges)
            [
                'title_en' => 'Sleep Hygiene Tips',
                'title_ar' => 'نصائح لنظافة النوم',
                'body_en' => 'Good sleep is essential for mental health. Aim for 7-9 hours, maintain a consistent schedule, and avoid screens 30 minutes before bed.',
                'body_ar' => 'النوم الجيد ضروري للصحة النفسية. استهدف 7-9 ساعات، حافظ على جدول منتظم، وتجنب الشاشات قبل النوم بـ 30 دقيقة.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 27, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
            [
                'title_en' => 'Social Connection',
                'title_ar' => 'التواصل الاجتماعي',
                'body_en' => 'Spending time with supportive people can improve your mental health. Join a university club or study group to build connections.',
                'body_ar' => 'قضاء الوقت مع أشخاص داعمين يمكن أن يحسن صحتك النفسية. انضم إلى نادٍ جامعي أو مجموعة دراسية لبناء علاقات.',
                'url' => null,
                'min_phq9' => 0, 'max_phq9' => 27, 'min_gad7' => 0, 'max_gad7' => 21,
            ],
        ];

        foreach ($recommendations as $rec) {
            Recommendation::updateOrCreate(
                ['title_en' => $rec['title_en']],
                $rec,
            );
        }
    }
}
