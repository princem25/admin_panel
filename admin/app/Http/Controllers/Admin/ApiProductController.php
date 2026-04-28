<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExternalApiService;
use App\Exceptions\ExternalApiException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class ApiProductController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 20);
        $category = $request->input('category');

        $categories = [];
        $products = [];
        $error = null;

        try {
            // Fetch categories for the filter dropdown
            $categoryResponse = $this->apiService->client()->get('/products/categories')->throw();
            $categories = $categoryResponse->json();

            // Fetch products based on selected category
            $url = '/products';
            if ($category && $category !== 'all') {
                $url .= '/category/' . urlencode($category);
            }

            $productResponse = $this->apiService->client()->get($url, [
                'limit' => $limit
            ])->throw()->throwIf(function ($response) {
                // Custom condition: manually throw if 'error' key exists
                return isset($response->json()['error']);
            });

            $products = $productResponse->json();
            Log::info('Successfully fetched API products.', [
                'count' => count($products),
                'category' => $category,
                'limit' => $limit
            ]);

        } catch (RequestException $e) {
            Log::error('API Request Exception.', [
                'status' => $e->response->status(),
                'url' => $e->request->url(),
                'message' => $e->getMessage(),
            ]);
            throw new ExternalApiException('The external service returned an error.');
        } catch (ConnectionException $e) {
            Log::error('API Connection Exception.', [
                'message' => $e->getMessage(),
            ]);
            throw new ExternalApiException('The external service is unreachable.');
        } catch (\Exception $e) {
            Log::error('API Generic Exception.', [
                'message' => $e->getMessage(),
            ]);
            throw new ExternalApiException('An unexpected error occurred.');
        }

        return view('admin.api-products.index', compact('products', 'categories', 'category', 'limit', 'error'));
    }
}
