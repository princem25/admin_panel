<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    /** @test */
    public function it_can_display_arabic_translations()
    {
        app()->setLocale('ar');

        $this->assertEquals('أضف إلى السلة', __('Add to Cart'));
        $this->assertEquals('إتمام الشراء', __('Checkout'));
        $this->assertEquals('نفذت الكمية', __('Out of Stock'));
        $this->assertEquals('الكمية', __('Quantity'));
        $this->assertEquals('إزالة', __('Remove'));
    }

    /** @test */
    public function it_can_display_arabic_validation_messages()
    {
        app()->setLocale('ar');

        $this->assertEquals('حقل الاسم مطلوب.', __('validation.required', ['attribute' => 'الاسم']));
    }
}
