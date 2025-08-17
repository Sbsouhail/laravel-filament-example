<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\CursorPaginateRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class RestaurantController extends Controller
{
    /** Display a listing of the resource.
     * @response AnonymousResourceCollection<CursorPaginator<RestaurantResource>>
     */
    public function index(CursorPaginateRequest $request): ResourceCollection
    {

        $validated = $request->validated();

        /** @var array<string, string> */
        $validatedFilters = $request->validate([
            'city_id' => 'integer|exists:cities,id',
            'search' => 'string',
        ]);

        $query = Restaurant::query();

        if (isset($validatedFilters['city_id'])) {
            $query->where('city_id', $validatedFilters['city_id']);
        }

        if (isset($validatedFilters['search'])) {
            $query->where('name', 'like', '%' .$validatedFilters['search'] . '%')->orWhere('description', 'like', '%' .  $validatedFilters['search'] . '%');
        }

        /** @var int|null */
        $limit = $validated['limit'];

        $data = $query
            ->where('is_active', true)
            ->with('city', 'cuisine', 'venue', 'coupon_offer')->orderBy('id', 'asc')
            ->cursorPaginate($limit ?? 10);

        return RestaurantResource::collection($data);
    }

    /**
     * Get restaurants by cities
     *
     * @response array<array{city_id: int, city_name: string|null, restaurants: array<RestaurantResource>}>
     */
    public function restaurantsByCities(Request $request): JsonResponse
    {
        $restaurants = Restaurant::where('is_active', true)
            ->with(['city:id,name,order', 'cuisine', 'venue', 'coupon_offer']) // Load only needed city fields
            ->get()
            ->groupBy('city_id')
            ->map(function ($group) {
                return [
                    'city_id' => $group->first()?->city_id,
                    'city_name' => $group->first()?->city?->name,
                    'city_order' => $group->first()?->city?->order,
                    'restaurants' => RestaurantResource::collection($group->take(5))->resolve(),
                ];
            })
            ->sortBy('city_order')
            ->values();

        return response()->json($restaurants);
    }

    /** Display the specified resource. */
    public function show(Restaurant $restaurant): RestaurantResource
    {
        return new RestaurantResource($restaurant->load('city', 'cuisine', 'venue', 'coupon_offer'));
    }
}
