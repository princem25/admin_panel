<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class ExternalApiService
{
    /**
     * Get a pre-configured HTTP client instance.
     *
     * @return PendingRequest
     */
    public function client(): PendingRequest
    {
        return Http::baseUrl(config('services.external_api.base_url'))
            ->withToken(config('services.external_api.token'))
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->timeout(10)
            ->retry(3, 100);
    }
}
