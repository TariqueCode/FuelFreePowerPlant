<?php

namespace Tests\Unit;

use App\Models\HomepageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_sections_can_be_enabled_disabled_and_ordered(): void
    {
        HomepageSection::create([
            'key' => 'test-hero',
            'label' => 'Hero & Slider',
            'is_enabled' => true,
            'sort_order' => 1,
        ]);

        HomepageSection::create([
            'key' => 'test-welcome',
            'label' => 'Company Introduction',
            'is_enabled' => false,
            'sort_order' => 0,
        ]);

        $sections = HomepageSection::ordered()->get();

        $this->assertSame(['test-welcome', 'test-hero'], $sections->pluck('key')->all());
        $this->assertFalse($sections->first()->is_enabled);
        $this->assertTrue($sections->last()->is_enabled);
    }
}
