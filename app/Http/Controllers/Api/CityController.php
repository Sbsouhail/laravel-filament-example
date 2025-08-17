<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\CursorPaginateRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class CityController extends Controller
{
    /** Display a listing of the resource.
     * @response AnonymousResourceCollection<CursorPaginator<CityResource>>
     */
    public function index(CursorPaginateRequest $request): ResourceCollection
    {
        $validated = $request->validated();

        /** @var int|null */
        $limit = $validated['limit'];

        $data = City::orderBy('order', 'asc')
            ->cursorPaginate($limit ?? 10);

        return CityResource::collection($data);
    }
}
