<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Device;
use App\Models\Survey;
use Illuminate\Database\Seeder;

/** Seeds the default MY SEPHORA loyalty-quiz content. */
class SephoraQuizSeeder extends Seeder
{
    public function run(): void
    {
        $campaign = Campaign::updateOrCreate(
            ['uuid' => '550e8400-e29b-41d4-a716-446655440001'],
            [
                'title' => ['en' => 'MY SEPHORA', 'ar' => 'ماي سيفورا'],
                'description' => ['en' => 'MY SEPHORA loyalty programme quiz.', 'ar' => 'اختبار برنامج ولاء ماي سيفورا.'],
                'status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addYear(),
                'settings' => ['primary_color' => '#1A1A1A', 'accent_color' => '#E2001A', 'thank_you_duration' => 5],
            ],
        );

        $survey = Survey::updateOrCreate(
            ['uuid' => '550e8400-e29b-41d4-a716-446655440002'],
            [
                'campaign_id' => $campaign->id,
                'title' => ['en' => 'MY SEPHORA Loyalty Quiz', 'ar' => 'اختبار ولاء ماي سيفورا'],
                'description' => ['en' => 'Test your MY SEPHORA knowledge.', 'ar' => 'اختبر معلوماتك عن ماي سيفورا.'],
                'status' => 'active', 'version' => 1,
                'settings' => ['show_progress' => true, 'allow_back' => false, 'require_all' => true],
            ],
        );

        // The seeder is intentionally idempotent: re-running it restores the supplied defaults.
        $survey->questions()->delete();

        $questions = [
            // [Question Text, Options, Correct Index]
            ['What are the 3 MY SEPHORA tiers?', ['Silver', 'Bronze, Silver, Gold', 'Gold'], 1],
            ['How many points do you need to Unlock Silver?', ['200 points', '300 points', '750 points'], 0],
            ['Where can you redeem your Welcome Gift?', ['Only Online', 'Only Instore', 'Online, Instore and on the App.'], 2],
            ['How many points do you need to Unlock Gold?', ['1000 points', '500 points', '2000 points'], 0],
            ['Fill in the blank:', ['600 points', '1000 points', '200 points'], 2, 'Every …. points you unlock a gift in the Silver tier.'],
            ['Fill in the blank:', ['1000 points', '2000 points', '700 points'], 0, 'Every …. points you unlock a gift in the Gold tier.'],
            ['How long is the birthday gift valid for?', ['1 Year', '1 Month', '1 Week'], 1],
            ['True or False:', ['True', 'False', 'None of the above'], 0, 'The Welcome Gift can be redeemed on your first purchase.'],
            ['True or False:', ['True', 'False', 'None of the above'], 0, 'Gold members get free delivery with online purchases.'],
            ['Which tier(s) get access to a Private Sale?', ['Gold', 'Silver, Gold', 'Bronze, Silver, Gold'], 2],
            ['Which tier(s) receive a Birthday Gift?', ['Silver, Gold', 'Gold', 'Silver'], 0],
            ['How many points do you need to Unlock Bronze?', ['0 Points', '200 Points', '1000 Points'], 0],
            ['Which tier(s) get invited to Events?', ['Bronze', 'Bronze, Silver, Gold', 'Silver, Gold'], 1],
        ];

        foreach ($questions as $index => $qData) {
            $text = $qData[0];
            $options = $qData[1];
            $correctIndex = $qData[2];
            $subtitle = count($qData) > 3 ? $qData[3] : '';

            $question = $survey->questions()->create([
                'type' => 'single_choice',
                'text' => ['en' => $text, 'ar' => $text],
                'description' => ['en' => $subtitle, 'ar' => $subtitle],
                'sort_order' => $index + 1,
                'is_required' => true,
                'is_active' => true,
                'settings' => ['code' => 'Q'.($index + 1), 'section' => 'MY SEPHORA Loyalty', 'section_ar' => 'ولاء ماي سيفورا'],
            ]);

            foreach ($options as $optionIndex => $label) {
                $question->answerOptions()->create([
                    'label' => ['en' => $label, 'ar' => $label],
                    'value' => 'option_'.($optionIndex + 1),
                    'is_correct' => $optionIndex === $correctIndex,
                    'sort_order' => $optionIndex + 1,
                    'is_active' => true,
                ]);
            }
        }

        Device::updateOrCreate(
            ['device_identifier' => 'KIOSK-LOBBY-001'],
            [
                'name' => 'MY SEPHORA Kiosk Lobby',
                'api_token' => 'qcc-dev-token-2025-lobby-001',
                'campaign_id' => $campaign->id,
                'status' => 'active',
            ],
        );

        Device::updateOrCreate(
            ['device_identifier' => 'SEPHORA-KIOSK-001'],
            [
                'name' => 'MY SEPHORA Kiosk Lobby 2',
                'api_token' => 'sephora-dev-token-2026-kiosk-001',
                'campaign_id' => $campaign->id,
                'status' => 'active',
            ],
        );
    }
}
