<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\CursorPaginateRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class OfferController extends Controller
{
    /** Display a listing of the resource.
     * @response AnonymousResourceCollection<CursorPaginator<OfferResource>>
     */
    public function index(CursorPaginateRequest $request): ResourceCollection
    {
        $validated = $request->validated();

        /** @var array<string, string> */
        $validatedFilters = $request->validate([
            'restaurant_id' => 'integer|exists:restaurants,id',
        ]);

        $query = Offer::query();

        if (isset($validatedFilters['restaurant_id'])) {
            $query->where('restaurant_id', $validatedFilters['restaurant_id']);
        }

        /** @var int|null */
        $limit = $validated['limit'];

        $data = $query
            ->with('restaurant')
            ->orderBy('id', 'asc')
            ->cursorPaginate($limit ?? 10);

        return OfferResource::collection($data);
    }

    /** Display the specified resource. */
    public function show(Offer $offer): OfferResource
    {
        return new OfferResource($offer->load('restaurant'));
    }
}
