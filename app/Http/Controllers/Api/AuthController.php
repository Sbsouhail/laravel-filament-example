<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterAccountRequest;
use App\Http\Requests\Auth\ResetPasswordOtpRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\Device;
use App\Models\ForgotPasswordOtp;
use App\Models\InviteCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /** Register a new user and return a token
     * @unauthenticated
     */
    public function register(RegisterAccountRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Check and handle invite code
        if ( ! empty($validated['invite_code'])) {
            /** @var InviteCode|null $invite */
            $invite = InviteCode::where('code', $validated['invite_code'])->first();

            abort_unless(
                $invite && ! $invite->used_by_id,
                403,
                'Invalid or already used invite code.'
            );
        }

        // Create the user
        $user = User::create($validated);

        // If valid invite, assign user to the invite
        if (isset($invite)) {
            $invite->update([
                'used_by_id' => $user->id,
                'used_at' => now(),
            ]);
        }

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('user-avatar');
        }

        /** @var string */
        $device_name = $validated['device_name'];

        $token = $user->createToken($device_name);

        return new JsonResponse([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user),
        ], 201);
    }

    /** Login a user and return a token
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var User|null $user */
        $user = User::whereEmail($validated['email'])->first();

        /** @var string */
        $password = $validated['password'];

        if ( ! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var string */
        $device_name = $validated['device_name'];
        $token = $user->createToken($device_name);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user),
        ], 201);
    }

    /** Logout the user and delete the token */
    public function logout(Request $request): JsonResponse
    {
        /** @var User|null $currentUser */
        $currentUser = $request->user();

        /** @var PersonalAccessToken|null $token */
        $token = $currentUser?->currentAccessToken();

        if ($token) {
            $deviceName = $token->name;

            // Delete the device with this identifier (if it exists)
            Device::where('identifier', $deviceName)->delete();

            $token->delete();
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /** Get the authenticated user */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()?->loadCount('inviteCodes', 'redemptions'));
    }

    /** Send a forgot password otp
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::whereEmail($validated['email'])->first();

        if ($user) {
            ForgotPasswordOtp::addAndSend(
                $user->id,
            );
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /** Verify a forgot password otp
     * @unauthenticated
     */
    public function verifyForgotPasswordOtp(VerifyOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $otp = ForgotPasswordOtp::where('otp', $validated['otp'])->first();

        if ( ! $otp || $otp->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /** Reset the password
     * @unauthenticated
     */
    public function resetPassword(ResetPasswordOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $otp = ForgotPasswordOtp::where('otp', $validated['otp'])->first();

        if ( ! $otp || $otp->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $otp->user;

        $user?->update([
            'password' => $validated['password'],
        ]);

        $otp->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /** Update the user's password */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        /** @var string */
        $current_password = $validated['current_password'];

        if ( ! Hash::check($current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => $validated['new_password'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    /** Update the user's profile */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $user->update($validated);

        if ($request->hasFile('avatar')) {
            // Optional: Remove previous avatar
            $user->clearMediaCollection('user-avatar');

            $user->addMediaFromRequest('avatar')->toMediaCollection('user-avatar', 's3');
        }

        return response()->json([
            'success' => true,
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /** Delete the authenticated user's account */
    public function deleteAccount(): JsonResponse
    {
        $user = Auth::user();

        abort_if( ! $user, 404, 'User not found.');

        $user->clearMediaCollection('user-avatar');
        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.',
        ]);
    }
}
