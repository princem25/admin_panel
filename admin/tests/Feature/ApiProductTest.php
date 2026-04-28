<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('api product page displays products successfully', function () {
    // Create an admin user to bypass middleware
    $admin = User::factory()->create(['role' => 'admin']);

    // Fake external API responses
    Http::fake([
        'fakestoreapi.com/products/categories*' => Http::response(['electronics', 'jewelery']),
        'fakestoreapi.com/products*' => Http::response([
            [
                'id' => 1,
                'title' => 'Faked Test Product',
                'price' => 109.95,
                'description' => 'A faked test product description',
                'category' => 'electronics',
                'image' => 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg',
                'rating' => ['rate' => 3.9, 'count' => 120]
            ]
        ], 200),
    ]);

    // Make the request
    $response = $this->actingAs($admin)->get('/admin/api-products');

    // Assert successful load and correct display
    $response->assertStatus(200);
    $response->assertSee('Faked Test Product');

    // Assert that requests were sent with expected headers
    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/products/categories') 
            && $request->hasHeader('Accept', 'application/json');
    });
});

test('api product page handles server error gracefully', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Simulate a 500 Server Error
    Http::fake([
        '*' => Http::response([], 500),
    ]);

    $response = $this->actingAs($admin)->get('/admin/api-products');

    // Assert that the exception was caught and gracefully redirected with an error flash message
    $response->assertRedirect();
    $response->assertSessionHas('error');
});
