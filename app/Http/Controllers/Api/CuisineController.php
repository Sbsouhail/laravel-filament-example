<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\CursorPaginateRequest;
use App\Http\Resources\CuisineResource;
use App\Models\Cuisine;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class CuisineController extends Controller
{
    /** Display a listing of the resource.
     * @response AnonymousResourceCollection<CursorPaginator<CuisineResource>>
     */
    public function index(CursorPaginateRequest $request): ResourceCollection
    {
        $validated = $request->validated();

        /** @var int|null */
        $limit = $validated['limit'];

        $data = Cuisine::orderBy('id', 'asc')
            ->cursorPaginate($limit ?? 10);

        return CuisineResource::collection($data);
    }
}
