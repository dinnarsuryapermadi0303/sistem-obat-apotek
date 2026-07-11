<?php

namespace Tests\Unit;

use App\Models\RecommendationValidation;
use Carbon\Carbon;
use Tests\TestCase;

class RecommendationValidationTest extends TestCase
{
    public function test_uses_approved_at_when_present_for_display_timestamp(): void
    {
        $approvedAt = Carbon::create(2026, 6, 27, 10, 15, 0, 'Asia/Jakarta');
        $createdAt = Carbon::create(2026, 6, 25, 8, 0, 0, 'Asia/Jakarta');

        $model = new RecommendationValidation();
        $model->created_at = $createdAt;
        $model->updated_at = $createdAt;
        $model->approved_at = $approvedAt;

        $this->assertSame($approvedAt->format('Y-m-d H:i:s'), $model->display_timestamp->format('Y-m-d H:i:s'));
        $this->assertSame($approvedAt->format('d-m-Y H:i:s'), $model->display_timestamp_formatted);
    }
}
