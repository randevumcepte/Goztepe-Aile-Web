<?php

namespace App\Http\Controllers\Api;

use App\Enums\MemberCategory;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly MembershipService $membership)
    {
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'category' => ['required', 'in:'.implode(',', array_column(MemberCategory::cases(), 'value'))],
            'password' => ['required', 'confirmed', Password::defaults()],
            'kvkk_consent' => ['accepted'],
            'commercial_consent' => ['nullable', 'boolean'],
        ]);

        $user = $this->membership->register($data);
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'E-posta veya şifre hatalı.']);
        }

        $token = $user->createToken($request->device_name ?? 'mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->userPayload($user->load('member')),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userPayload($request->user()->load('member'))]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }

    /** Push bildirimi için cihaz token kaydı. */
    public function registerDevice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform' => ['required', 'in:ios,android,web'],
            'token' => ['required', 'string'],
        ]);

        $request->user()->deviceTokens()->updateOrCreate(
            ['token' => $data['token']],
            ['platform' => $data['platform'], 'last_seen_at' => now()],
        );

        return response()->json(['message' => 'Cihaz kaydedildi.']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'member' => $user->member ? [
                'member_no' => $user->member->member_no,
                'category' => $user->member->category->value,
                'category_label' => $user->member->category->label(),
                'status' => $user->member->status,
                'has_vote' => $user->member->hasVote(),
            ] : null,
        ];
    }
}
