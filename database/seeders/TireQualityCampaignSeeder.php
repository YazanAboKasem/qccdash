<?php

namespace Database\Seeders;

use App\Models\AnswerOption;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Database\Seeder;

class TireQualityCampaignSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Campaign ──────────────────────────────────────────
        $campaign = Campaign::updateOrCreate(
            ['uuid' => '550e8400-e29b-41d4-a716-446655440001'],
            [
                'title' => [
                    'en' => 'Tire Quality Inspection Campaign',
                    'ar' => 'حملة فحص جودة الإطارات',
                ],
                'description' => [
                    'en' => 'Abu Dhabi Quality & Conformity Council campaign to assess tire quality awareness and compliance across the emirate.',
                    'ar' => 'حملة مجلس أبوظبي للجودة والمطابقة لتقييم الوعي بجودة الإطارات والامتثال في جميع أنحاء الإمارة.',
                ],
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonths(3),
                'settings' => [
                    'primary_color' => '#003B73',
                    'accent_color' => '#D4AF37',
                    'thank_you_duration' => 5,
                ],
            ]
        );

        // ─── Survey ────────────────────────────────────────────
        $survey = Survey::updateOrCreate(
            ['uuid' => '550e8400-e29b-41d4-a716-446655440002'],
            [
                'campaign_id' => $campaign->id,
                'title' => [
                    'en' => 'Tire Quality Awareness Survey',
                    'ar' => 'استبيان الوعي بجودة الإطارات',
                ],
                'description' => [
                    'en' => 'Help us understand your awareness about tire quality standards.',
                    'ar' => 'ساعدنا في فهم مدى وعيك بمعايير جودة الإطارات.',
                ],
                'status' => 'active',
                'version' => 1,
                'settings' => [
                    'show_progress' => true,
                    'allow_back' => true,
                    'require_all' => true,
                ],
            ]
        );

        // ─── Questions & Answer Options ────────────────────────

        $questions = [
            // D1
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440010',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Age',
                    'ar' => 'العمر',
                ],
                'sort_order' => 1,
                'settings' => [
                    'code' => 'D1',
                    'section' => 'Respondent Information',
                    'section_ar' => 'بيانات المشارك',
                ],
                'options' => [
                    ['label' => ['en' => 'Under 25', 'ar' => 'أقل من ٢٥'], 'value' => 'under_25', 'icon' => '👶', 'color' => '#2E7D32', 'score' => 0],
                    ['label' => ['en' => '25–40', 'ar' => '٢٥–٤٠'], 'value' => '25_40', 'icon' => '👨', 'color' => '#1565C0', 'score' => 0],
                    ['label' => ['en' => '41–60', 'ar' => '٤١–٦٠'], 'value' => '41_60', 'icon' => '🧔', 'color' => '#F57F17', 'score' => 0],
                    ['label' => ['en' => 'Over 60', 'ar' => 'أكثر من ٦٠'], 'value' => 'over_60', 'icon' => '🧓', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // D2
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440011',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Gender',
                    'ar' => 'الجنس',
                ],
                'sort_order' => 2,
                'settings' => [
                    'code' => 'D2',
                    'section' => 'Respondent Information',
                    'section_ar' => 'بيانات المشارك',
                ],
                'options' => [
                    ['label' => ['en' => 'Male', 'ar' => 'ذكر'], 'value' => 'male', 'icon' => '👨', 'color' => '#1565C0', 'score' => 0],
                    ['label' => ['en' => 'Female', 'ar' => 'أنثى'], 'value' => 'female', 'icon' => '👩', 'color' => '#E91E63', 'score' => 0],
                ],
            ],
            // D3
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440012',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Place of residence',
                    'ar' => 'مكان الإقامة',
                ],
                'sort_order' => 3,
                'settings' => [
                    'code' => 'D3',
                    'section' => 'Respondent Information',
                    'section_ar' => 'بيانات المشارك',
                ],
                'options' => [
                    ['label' => ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'], 'value' => 'abu_dhabi', 'icon' => '🏙️', 'color' => '#2E7D32', 'score' => 0],
                    ['label' => ['en' => 'Al Ain', 'ar' => 'العين'], 'value' => 'al_ain', 'icon' => '🌴', 'color' => '#1565C0', 'score' => 0],
                    ['label' => ['en' => 'Al Dhafra', 'ar' => 'الظفرة'], 'value' => 'al_dhafra', 'icon' => '🏜️', 'color' => '#F57F17', 'score' => 0],
                    ['label' => ['en' => 'Other Emirate', 'ar' => 'إمارة أخرى'], 'value' => 'other_emirate', 'icon' => '🇦🇪', 'color' => '#7B1FA2', 'score' => 0],
                ],
            ],
            // D4
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440013',
                'type' => 'yes_no',
                'text' => [
                    'en' => 'Do you own a car?',
                    'ar' => 'هل تملك سيارة؟',
                ],
                'sort_order' => 4,
                'settings' => [
                    'code' => 'D4',
                    'section' => 'Respondent Information',
                    'section_ar' => 'بيانات المشارك',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '🚗', 'color' => '#2E7D32', 'score' => 0],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '🚶', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q1
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440014',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Did you know tires carry a quality label showing compliance with standards?',
                    'ar' => 'هل كنت تعرف أن للإطار ملصق هيئة الامارات للمواصفات يوضح مطابقته للمعايير؟',
                ],
                'sort_order' => 5,
                'settings' => [
                    'code' => 'Q1',
                    'section' => 'Axis 1: Safety knowledge & awareness BEFORE the visit',
                    'section_ar' => 'المحور الأول: المعرفة والوعي بالسلامة قبل زيارة المنصة',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '👍', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Somewhat', 'ar' => 'نوعاً ما'], 'value' => 'somewhat', 'icon' => '🤔', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q2
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440015',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Did you check the tire\'s production date before buying?',
                    'ar' => 'هل كنت تتحقق من تاريخ إنتاج الإطار قبل الشراء؟',
                ],
                'sort_order' => 6,
                'settings' => [
                    'code' => 'Q2',
                    'section' => 'Axis 1: Safety knowledge & awareness BEFORE the visit',
                    'section_ar' => 'المحور الأول: المعرفة والوعي بالسلامة قبل زيارة المنصة',
                ],
                'options' => [
                    ['label' => ['en' => 'Always', 'ar' => 'دائماً'], 'value' => 'always', 'icon' => '🔄', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Sometimes', 'ar' => 'أحياناً'], 'value' => 'sometimes', 'icon' => '⏳', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'Never', 'ar' => 'أبداً'], 'value' => 'never', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q5
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440016',
                'type' => 'yes_no',
                'text' => [
                    'en' => 'Did you know a tire has a shelf life even when unused?',
                    'ar' => 'هل تعلم أن الإطار له عمر افتراضي حتى دون استخدام؟',
                ],
                'sort_order' => 7,
                'settings' => [
                    'code' => 'Q5',
                    'section' => 'Axis 1: Safety knowledge & awareness BEFORE the visit',
                    'section_ar' => 'المحور الأول: المعرفة والوعي بالسلامة قبل زيارة المنصة',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '💡', 'color' => '#2E7D32', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q6
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440017',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'How do you rate your tire-safety knowledge BEFORE the visit?',
                    'ar' => 'كيف تقيم معرفتك بسلامة الإطارات قبل الزيارة؟',
                ],
                'sort_order' => 8,
                'settings' => [
                    'code' => 'Q6',
                    'section' => 'Axis 1: Safety knowledge & awareness BEFORE the visit',
                    'section_ar' => 'المحور الأول: المعرفة والوعي بالسلامة قبل زيارة المنصة',
                ],
                'options' => [
                    ['label' => ['en' => 'Poor', 'ar' => 'ضعيف'], 'value' => 'poor', 'icon' => '😞', 'color' => '#B71C1C', 'score' => 1],
                    ['label' => ['en' => 'Fair', 'ar' => 'متوسط'], 'value' => 'fair', 'icon' => '😐', 'color' => '#F57F17', 'score' => 2],
                    ['label' => ['en' => 'Good', 'ar' => 'جيد'], 'value' => 'good', 'icon' => '😊', 'color' => '#1565C0', 'score' => 3],
                    ['label' => ['en' => 'Excellent', 'ar' => 'ممتاز'], 'value' => 'excellent', 'icon' => '😍', 'color' => '#2E7D32', 'score' => 4],
                ],
            ],
            // Q7
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440018',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Are you now more aware of how tire quality affects your safety?',
                    'ar' => 'هل أصبحت أكثر إدراكاً لأهمية جودة الإطار في سلامتك؟',
                ],
                'sort_order' => 9,
                'settings' => [
                    'code' => 'Q7',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'A lot', 'ar' => 'كثيراً'], 'value' => 'a_lot', 'icon' => '📈', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Somewhat', 'ar' => 'نوعاً ما'], 'value' => 'somewhat', 'icon' => '🤔', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q8
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440030',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Can you now read the tire data label?',
                    'ar' => 'هل تستطيع الآن قراءة بطاقة بيانات الإطار؟',
                ],
                'sort_order' => 10,
                'settings' => [
                    'code' => 'Q8',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '👍', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Somewhat', 'ar' => 'نوعاً ما'], 'value' => 'somewhat', 'icon' => '🤔', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q9
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440019',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Did the booth give you new information you did not know?',
                    'ar' => 'هل أضافت لك المنصة معلومات جديدة لم تكن تعرفها؟',
                ],
                'sort_order' => 11,
                'settings' => [
                    'code' => 'Q9',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'A lot', 'ar' => 'نعم كثيرة'], 'value' => 'a_lot', 'icon' => '📚', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Some', 'ar' => 'بعضها'], 'value' => 'some', 'icon' => '📄', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q10
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440020',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Has your view on regular tire checks changed?',
                    'ar' => 'هل تغيرت نظرتك لأهمية الفحص الدوري للإطارات؟',
                ],
                'sort_order' => 12,
                'settings' => [
                    'code' => 'Q10',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '🔄', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Somewhat', 'ar' => 'نوعاً ما'], 'value' => 'somewhat', 'icon' => '🤔', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q11
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440021',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Will you check the quality label on your next purchase?',
                    'ar' => 'هل ستتحقق من ملصق الجودة عند شرائك القادم؟',
                ],
                'sort_order' => 13,
                'settings' => [
                    'code' => 'Q11',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'Definitely', 'ar' => 'بالتأكيد'], 'value' => 'definitely', 'icon' => '✅', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Maybe', 'ar' => 'ربما'], 'value' => 'maybe', 'icon' => '❓', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q12
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440031',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Will you share what you learned with family and friends?',
                    'ar' => 'هل ستشارك ما تعلمته مع عائلتك وأصدقائك؟',
                ],
                'sort_order' => 14,
                'settings' => [
                    'code' => 'Q12',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '👍', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Maybe', 'ar' => 'ربما'], 'value' => 'maybe', 'icon' => '❓', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q13
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440022',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'How do you rate your tire-safety awareness AFTER the visit?',
                    'ar' => 'كيف تقيم مستوى وعيك بسلامة الإطارات بعد الزيارة؟',
                ],
                'sort_order' => 15,
                'settings' => [
                    'code' => 'Q13',
                    'section' => 'Axis 2: Impact of the booth on awareness AFTER the visit',
                    'section_ar' => 'المحور الثاني: أثر المنصة في رفع الوعي بعد الزيارة',
                ],
                'options' => [
                    ['label' => ['en' => 'Poor', 'ar' => 'ضعيف'], 'value' => 'poor', 'icon' => '😞', 'color' => '#B71C1C', 'score' => 1],
                    ['label' => ['en' => 'Fair', 'ar' => 'متوسط'], 'value' => 'fair', 'icon' => '😐', 'color' => '#F57F17', 'score' => 2],
                    ['label' => ['en' => 'Good', 'ar' => 'جيد'], 'value' => 'good', 'icon' => '😊', 'color' => '#1565C0', 'score' => 3],
                    ['label' => ['en' => 'Excellent', 'ar' => 'ممتاز'], 'value' => 'excellent', 'icon' => '😍', 'color' => '#2E7D32', 'score' => 4],
                ],
            ],
            // Q14
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440023',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Do you know the Council certifies devices and verifies measuring accuracy?',
                    'ar' => 'هل تعرف أن المجلس يعتمد الأجهزة ويتحقق من دقة أدوات القياس؟',
                ],
                'sort_order' => 16,
                'settings' => [
                    'code' => 'Q14',
                    'section' => 'Axis 3: The Council\'s role in raising quality of life in Abu Dhabi',
                    'section_ar' => 'المحور الثالث: دور المجلس في رفع جودة الحياة في أبوظبي',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '🤝', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Learned today', 'ar' => 'عرفت اليوم'], 'value' => 'learned_today', 'icon' => '💡', 'color' => '#1565C0', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q15
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440032',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Do you realize the Council protects consumers and ensures fair trade?',
                    'ar' => 'هل تدرك أن دور المجلس يشمل حماية المستهلك وضمان التجارة العادلة؟',
                ],
                'sort_order' => 17,
                'settings' => [
                    'code' => 'Q15',
                    'section' => 'Axis 3: The Council\'s role in raising quality of life in Abu Dhabi',
                    'section_ar' => 'المحور الثالث: دور المجلس في رفع جودة الحياة في أبوظبي',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '🛡️', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Somewhat', 'ar' => 'نوعاً ما'], 'value' => 'somewhat', 'icon' => '🤔', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q16
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440033',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Do you know the Council develops quality infrastructure to boost Abu Dhabi\'s competitiveness?',
                    'ar' => 'هل تعلم أن المجلس يطوّر البنية التحتية للجودة لتعزيز تنافسية أبوظبي؟',
                ],
                'sort_order' => 18,
                'settings' => [
                    'code' => 'Q16',
                    'section' => 'Axis 3: The Council\'s role in raising quality of life in Abu Dhabi',
                    'section_ar' => 'المحور الثالث: دور المجلس في رفع جودة الحياة في أبوظبي',
                ],
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '📈', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Learned today', 'ar' => 'عرفت اليوم'], 'value' => 'learned_today', 'icon' => '💡', 'color' => '#1565C0', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q17
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440034',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'To what extent does the Council\'s work reflect on your quality of life and daily safety?',
                    'ar' => 'إلى أي مدى ترى أن عمل المجلس ينعكس على جودة حياتك وسلامتك اليومية؟',
                ],
                'sort_order' => 19,
                'settings' => [
                    'code' => 'Q17',
                    'section' => 'Axis 3: The Council\'s role in raising quality of life in Abu Dhabi',
                    'section_ar' => 'المحور الثالث: دور المجلس في رفع جودة الحياة في أبوظبي',
                ],
                'options' => [
                    ['label' => ['en' => 'Greatly', 'ar' => 'بشكل كبير'], 'value' => 'greatly', 'icon' => '🌟', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Somewhat', 'ar' => 'إلى حدٍّ ما'], 'value' => 'somewhat', 'icon' => '🤔', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'Limited', 'ar' => 'محدود'], 'value' => 'limited', 'icon' => '📉', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q18
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440035',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'How much do you trust products carrying the Council\'s quality mark?',
                    'ar' => 'ما مدى ثقتك بالمنتجات الحاملة لعلامة الجودة من المجلس؟',
                ],
                'sort_order' => 20,
                'settings' => [
                    'code' => 'Q18',
                    'section' => 'Axis 3: The Council\'s role in raising quality of life in Abu Dhabi',
                    'section_ar' => 'المحور الثالث: دور المجلس في رفع جودة الحياة في أبوظبي',
                ],
                'options' => [
                    ['label' => ['en' => 'High', 'ar' => 'عالية'], 'value' => 'high', 'icon' => '💎', 'color' => '#2E7D32', 'score' => 2],
                    ['label' => ['en' => 'Medium', 'ar' => 'متوسطة'], 'value' => 'medium', 'icon' => '⚖️', 'color' => '#F57F17', 'score' => 1],
                    ['label' => ['en' => 'Low', 'ar' => 'منخفضة'], 'value' => 'low', 'icon' => '⚠️', 'color' => '#B71C1C', 'score' => 0],
                ],
            ],
            // Q19
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440036',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'After your visit, how do you rate the Council\'s role in enhancing quality?',
                    'ar' => 'بعد زيارتك، كيف تقيم دور المجلس في تعزيز الجودة بالإمارة؟',
                ],
                'sort_order' => 21,
                'settings' => [
                    'code' => 'Q19',
                    'section' => 'Axis 3: The Council\'s role in raising quality of life in Abu Dhabi',
                    'section_ar' => 'المحور الثالث: دور المجلس في رفع جودة الحياة في أبوظبي',
                ],
                'options' => [
                    ['label' => ['en' => 'Excellent', 'ar' => 'ممتاز'], 'value' => 'excellent', 'icon' => '😍', 'color' => '#2E7D32', 'score' => 4],
                    ['label' => ['en' => 'Good', 'ar' => 'جيد'], 'value' => 'good', 'icon' => '😊', 'color' => '#1565C0', 'score' => 3],
                    ['label' => ['en' => 'Fair', 'ar' => 'متوسط'], 'value' => 'fair', 'icon' => '😐', 'color' => '#F57F17', 'score' => 2],
                    ['label' => ['en' => 'Poor', 'ar' => 'ضعيف'], 'value' => 'poor', 'icon' => '😞', 'color' => '#B71C1C', 'score' => 1],
                ],
            ],
        ];

        foreach ($questions as $qData) {
            $options = $qData['options'];
            unset($qData['options']);

            $question = Question::updateOrCreate(
                ['uuid' => $qData['uuid']],
                array_merge($qData, ['survey_id' => $survey->id, 'is_required' => true, 'is_active' => true])
            );

            foreach ($options as $index => $optData) {
                AnswerOption::updateOrCreate(
                    ['question_id' => $question->id, 'value' => $optData['value']],
                    array_merge($optData, [
                        'question_id' => $question->id,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ])
                );
            }
        }

        // ─── Sample Device ─────────────────────────────────────
        Device::updateOrCreate(
            ['device_identifier' => 'KIOSK-LOBBY-001'],
            [
                'name' => 'Main Lobby Kiosk',
                'api_token' => 'qcc-dev-token-2025-lobby-001',
                'campaign_id' => $campaign->id,
                'status' => 'active',
                'device_info' => [
                    'model' => 'Samsung Galaxy View 2',
                    'screen_size' => '32 inch',
                    'os_version' => 'Android 13',
                    'app_version' => '1.0.0',
                ],
            ]
        );

        Device::updateOrCreate(
            ['device_identifier' => 'KIOSK-ENTRANCE-002'],
            [
                'name' => 'Entrance Kiosk',
                'api_token' => 'qcc-dev-token-2025-entrance-002',
                'campaign_id' => $campaign->id,
                'status' => 'active',
                'device_info' => [
                    'model' => 'Samsung Galaxy View 2',
                    'screen_size' => '32 inch',
                    'os_version' => 'Android 13',
                    'app_version' => '1.0.0',
                ],
            ]
        );
    }
}
