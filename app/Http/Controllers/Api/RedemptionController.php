<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Redemption;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\CouponResource;

class RedemptionController extends Controller
{
    /**
     * Get the remaining redemptions for the user in a specific restaurant
     * (resets every calendar month).
     */
    public function remainingRedemptions(Request $request, Restaurant $restaurant): JsonResponse
    {
        $user = $request->user();

        $redemptions = Redemption::where('user_id', $user?->id)
            ->where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderBy('created_at')
            ->get();

        $allowed = $restaurant->coupon_per_user;
        $usedCount = $redemptions->count();
        $remaining = max(0, $allowed - $usedCount);

        $couponResources = CouponResource::collection($redemptions)->toArray($request);

        // Add placeholder entries for unused coupons
        $placeholders = collect()->times($remaining, fn () => [
            'code' => null,
            'used_at' => null,
            'created_at' => null,
        ]);

        $allCoupons = array_merge((array) $couponResources, $placeholders->toArray());

        return response()->json([
            'total_allowed' => $allowed,
            'used' => $usedCount,
            'remaining' => $remaining,
            'month' => now()->month,
            'year' => now()->year,
            'coupons' => $allCoupons,
        ]);
    }

    /**
     * Redeem a coupon for the user in a specific restaurant
     * (resets every calendar month).
     */
    public function redeem(Request $request, Restaurant $restaurant): JsonResponse
    {
        $user = $request->user();

        $usedCount = Redemption::where('user_id', $user?->id)
            ->where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($usedCount >= $restaurant->coupon_per_user) {
            return response()->json([
                'message' => 'Redemption limit reached for this month.',
            ], 403);
        }

        // $code = substr_replace(strtoupper(Str::random(8)), '-', 4, 0);
        $code = time();

        $redemption = Redemption::create([
            'user_id' => $user?->id,
            'restaurant_id' => $restaurant->id,
            'code' => $code,
            'used_at' => now(),
        ]);

        return response()->json([
            'message' => 'Coupon redeemed.',
            'coupon' => new CouponResource($redemption),
        ]);
    }
}
