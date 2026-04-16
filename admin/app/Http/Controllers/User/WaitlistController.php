<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductWaitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WaitlistController extends Controller
{
    /**
     * Add the authenticated user to the product waitlist.
     * Only allowed when the product is inactive (out of stock).
     */
    public function join(Request $request, Product $product)
    {
        $user = $request->user();

        // Guard: product must be out of stock
        if ($product->stock > 0) {
            return back()->with('error', 'This product is currently available. Add it to your cart!');
        }

        // Guard: prevent duplicate entries
        $alreadyJoined = ProductWaitlist::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyJoined) {
            return back()->with('info', "You're already on the waitlist for \"{$product->name}\". We'll email you when it's back!");
        }

        // Store in waitlist
        ProductWaitlist::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
        ]);

        Log::channel('products')->info('User joined product waitlist', [
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'user_id'      => $user->id,
            'user_email'   => $user->email,
        ]);

        return back()->with('success', "You're on the waitlist! We'll notify you at {$user->email} when \"{$product->name}\" is back in stock.");
    }
}
