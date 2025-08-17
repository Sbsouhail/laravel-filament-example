<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\CursorPaginateRequest;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class VenueController extends Controller
{
    /** Display a listing of the resource.
     * @response AnonymousResourceCollection<CursorPaginator<VenueResource>>
     */
    public function index(CursorPaginateRequest $request): ResourceCollection
    {
        $validated = $request->validated();

        /** @var int|null */
        $limit = $validated['limit'];

        $data = Venue::orderBy('id', 'asc')
            ->cursorPaginate($limit ?? 10);

        return VenueResource::collection($data);
    }
}
