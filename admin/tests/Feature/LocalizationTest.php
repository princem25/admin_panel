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

    /** @test */
    public function it_can_display_parameterized_translations()
    {
        app()->setLocale('ar');
        $this->assertEquals('مرحباً، Prince!', __('Welcome, :name!', ['name' => 'Prince']));
        $this->assertEquals('تم تقديم الطلب رقم 123 في 2026-04-28', __('Order # :id placed on :date', ['id' => 123, 'date' => '2026-04-28']));
    }

    /** @test */
    public function it_can_display_plural_translations_in_english()
    {
        app()->setLocale('en');

        $this->assertEquals('No items', trans_choice('cart_items', 0));
        $this->assertEquals('1 item', trans_choice('cart_items', 1));
        $this->assertEquals('10 items', trans_choice('cart_items', 10, ['count' => 10]));
    }

    /** @test */
    public function it_can_display_plural_translations_in_arabic()
    {
        app()->setLocale('ar');

        $this->assertEquals('لا توجد عناصر', trans_choice('cart_items', 0));
        $this->assertEquals('عنصر واحد', trans_choice('cart_items', 1));
        $this->assertEquals('عنصران', trans_choice('cart_items', 2));
        $this->assertEquals('5 عناصر', trans_choice('cart_items', 5, ['count' => 5]));
        $this->assertEquals('15 عنصر', trans_choice('cart_items', 15, ['count' => 15]));
    }
}
