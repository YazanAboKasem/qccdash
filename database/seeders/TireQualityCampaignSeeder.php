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
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440010',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'How often do you check your tire condition?',
                    'ar' => 'كم مرة تفحص حالة إطاراتك؟',
                ],
                'sort_order' => 1,
                'options' => [
                    ['label' => ['en' => 'Weekly', 'ar' => 'أسبوعياً'], 'value' => 'weekly', 'icon' => '📅', 'color' => '#2E7D32', 'score' => 5],
                    ['label' => ['en' => 'Monthly', 'ar' => 'شهرياً'], 'value' => 'monthly', 'icon' => '📆', 'color' => '#1565C0', 'score' => 4],
                    ['label' => ['en' => 'Every 3 months', 'ar' => 'كل 3 أشهر'], 'value' => 'quarterly', 'icon' => '🗓️', 'color' => '#F57F17', 'score' => 3],
                    ['label' => ['en' => 'Rarely', 'ar' => 'نادراً'], 'value' => 'rarely', 'icon' => '⏳', 'color' => '#E65100', 'score' => 2],
                    ['label' => ['en' => 'Never', 'ar' => 'أبداً'], 'value' => 'never', 'icon' => '❌', 'color' => '#B71C1C', 'score' => 1],
                ],
            ],
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440011',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Are you aware of the ESMA tire quality standards?',
                    'ar' => 'هل أنت على دراية بمعايير جودة الإطارات الصادرة من هيئة المواصفات والمقاييس؟',
                ],
                'sort_order' => 2,
                'options' => [
                    ['label' => ['en' => 'Very well informed', 'ar' => 'على دراية تامة'], 'value' => 'very_informed', 'icon' => '✅', 'color' => '#2E7D32', 'score' => 5],
                    ['label' => ['en' => 'Somewhat informed', 'ar' => 'على دراية جزئية'], 'value' => 'somewhat', 'icon' => '📗', 'color' => '#1565C0', 'score' => 3],
                    ['label' => ['en' => 'Not informed', 'ar' => 'غير مطلع'], 'value' => 'not_informed', 'icon' => '❓', 'color' => '#E65100', 'score' => 1],
                ],
            ],
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440012',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'Where do you usually purchase your tires?',
                    'ar' => 'من أين تشتري إطاراتك عادةً؟',
                ],
                'sort_order' => 3,
                'options' => [
                    ['label' => ['en' => 'Authorized dealer', 'ar' => 'وكيل معتمد'], 'value' => 'authorized', 'icon' => '🏢', 'color' => '#2E7D32', 'score' => 5],
                    ['label' => ['en' => 'Tire specialty shop', 'ar' => 'محل إطارات متخصص'], 'value' => 'specialty', 'icon' => '🔧', 'color' => '#1565C0', 'score' => 4],
                    ['label' => ['en' => 'Online', 'ar' => 'عبر الإنترنت'], 'value' => 'online', 'icon' => '🌐', 'color' => '#7B1FA2', 'score' => 3],
                    ['label' => ['en' => 'General auto shop', 'ar' => 'محل سيارات عام'], 'value' => 'general', 'icon' => '🏪', 'color' => '#F57F17', 'score' => 2],
                ],
            ],
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440013',
                'type' => 'single_choice',
                'text' => [
                    'en' => 'What is the most important factor when buying tires?',
                    'ar' => 'ما هو العامل الأهم عند شراء الإطارات؟',
                ],
                'sort_order' => 4,
                'options' => [
                    ['label' => ['en' => 'Safety rating', 'ar' => 'تصنيف السلامة'], 'value' => 'safety', 'icon' => '🛡️', 'color' => '#2E7D32', 'score' => 5],
                    ['label' => ['en' => 'Brand reputation', 'ar' => 'سمعة العلامة التجارية'], 'value' => 'brand', 'icon' => '⭐', 'color' => '#1565C0', 'score' => 4],
                    ['label' => ['en' => 'Price', 'ar' => 'السعر'], 'value' => 'price', 'icon' => '💰', 'color' => '#F57F17', 'score' => 2],
                    ['label' => ['en' => 'Appearance', 'ar' => 'المظهر'], 'value' => 'appearance', 'icon' => '👀', 'color' => '#7B1FA2', 'score' => 1],
                ],
            ],
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440014',
                'type' => 'rating',
                'text' => [
                    'en' => 'How would you rate ADQCC\'s efforts in ensuring tire quality?',
                    'ar' => 'كيف تقيم جهود مجلس أبوظبي للجودة والمطابقة في ضمان جودة الإطارات؟',
                ],
                'sort_order' => 5,
                'options' => [
                    ['label' => ['en' => 'Excellent', 'ar' => 'ممتاز'], 'value' => '5', 'icon' => '😍', 'color' => '#2E7D32', 'score' => 5],
                    ['label' => ['en' => 'Good', 'ar' => 'جيد'], 'value' => '4', 'icon' => '😊', 'color' => '#43A047', 'score' => 4],
                    ['label' => ['en' => 'Average', 'ar' => 'متوسط'], 'value' => '3', 'icon' => '😐', 'color' => '#F57F17', 'score' => 3],
                    ['label' => ['en' => 'Below Average', 'ar' => 'أقل من المتوسط'], 'value' => '2', 'icon' => '😕', 'color' => '#E65100', 'score' => 2],
                    ['label' => ['en' => 'Poor', 'ar' => 'ضعيف'], 'value' => '1', 'icon' => '😞', 'color' => '#B71C1C', 'score' => 1],
                ],
            ],
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440015',
                'type' => 'yes_no',
                'text' => [
                    'en' => 'Would you like to receive tire safety tips from ADQCC?',
                    'ar' => 'هل ترغب في تلقي نصائح سلامة الإطارات من مجلس أبوظبي للجودة والمطابقة؟',
                ],
                'sort_order' => 6,
                'options' => [
                    ['label' => ['en' => 'Yes', 'ar' => 'نعم'], 'value' => 'yes', 'icon' => '👍', 'color' => '#2E7D32', 'score' => 1],
                    ['label' => ['en' => 'No', 'ar' => 'لا'], 'value' => 'no', 'icon' => '👎', 'color' => '#B71C1C', 'score' => 0],
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
