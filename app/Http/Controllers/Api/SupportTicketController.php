<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportTicket\CreateSupportTicketRequest;
use App\Models\User;
use App\Notifications\SupportTicketCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;

class SupportTicketController extends Controller
{
    /** Store a newly created resource in storage. */
    public function store(CreateSupportTicketRequest $request): JsonResponse
    {
        /** @var array<string, string> */
        $validated = $request->validated();

        /** @var User $currentUser */
        $currentUser = $request->user();

        // Simulate a support ticket object for the notification (use model if available)
        $ticket = (object) [
            'subject' => (string) $validated['subject'],
            'description' => (string) $validated['description'],
            'user' => $currentUser,
        ];

        // Notify all admins
        $admins = User::where('is_admin', true)->get();

        Notification::send($admins, new SupportTicketCreated($ticket));

        return response()->json(['success' => true]);
    }
}
