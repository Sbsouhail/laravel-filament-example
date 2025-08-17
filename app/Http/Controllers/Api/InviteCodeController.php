<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\CursorPaginateRequest;
use App\Http\Resources\InviteCodeResource;
use App\Http\Resources\UserResource;
use App\Models\InviteCode;
use App\Notifications\InviteCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InviteCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @response AnonymousResourceCollection<CursorPaginator<InviteCodeResource>>
     */
    public function index(CursorPaginateRequest $request): ResourceCollection
    {
        $validated = $request->validated();

        /** @var int|null */
        $limit = $validated['limit'];

        // Generate missing invite codes if needed
        $existingCount = InviteCode::where('user_id', Auth::id())->count();
        $requiredCount = Auth::user()?->invite_limit;

        if ($existingCount < $requiredCount) {
            $difference = $requiredCount - $existingCount;

            $newCodes = [];
            for ($i = 0; $i < $difference; $i++) {
                $code = substr_replace(strtoupper(Str::random(8)), '-', 3, 0);
                $newCodes[] = [
                    'code' => $code,
                    'user_id' => Auth::id(),
                ];
            }

            // You may want to use DB::transaction if this matters
            InviteCode::insert($newCodes);
        }

        // Return paginated invite codes
        $invites = InviteCode::where('user_id', Auth::id())
            ->orderBy('id', 'asc')
            ->cursorPaginate($limit ?? 10);

        return InviteCodeResource::collection($invites);
    }

    /** Verify if a code is valid
     * @unauthenticated
     */
    public function verify(Request $request): JsonResponse
    {
        /** @var array<string, string> */
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        /** @var InviteCode|null */
        $invite = InviteCode::where('code', $validated['code'])
            ->whereNull('used_by_id')
            ->with('user')
            ->first();

        abort_if( ! $invite, 403, 'Code not found or already used.');

        return new JsonResponse([
            'inviter' => new UserResource($invite->user),
        ]);
    }

    /** Send an invite code */
    public function send(Request $request, InviteCode $inviteCode): JsonResponse
    {
        /** @var array<string, string> */
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
        ]);

        abort_if($inviteCode->user_id !== $request->user()?->id || $inviteCode->used_by_id, 403, 'Code not found or already used.');

        Notification::route('mail', $validated['email'])
            ->notify(new InviteCodeNotification($inviteCode));

        return new JsonResponse([
            'sent_to' => $validated['email'],
        ]);
    }
}
