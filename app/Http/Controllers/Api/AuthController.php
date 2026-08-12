<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($data): User {
            $company = Company::create([
                'name' => $data['company_name'],
                'email' => $data['email'],
                'trial_ends_at' => now()->addDays(14),
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'owner',
                'email_verified_at' => null,
            ]);

            $user->sendEmailVerificationNotification();

            return $user->load('company');
        });

        return $this->tokenResponse($user, $data['device_name'] ?? 'mobile-app', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        return $this->tokenResponse($user->load('company'), $data['device_name'] ?? 'mobile-app');
    }

    public function googleLogin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_token' => ['nullable', 'string'],
            'access_token' => ['nullable', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($data['id_token']) && empty($data['access_token'])) {
            throw ValidationException::withMessages([
                'id_token' => ['id_token atau access_token dari Google Sign-In wajib diisi.'],
            ]);
        }

        $email = null;
        $name = null;
        $googleId = null;

        if (! empty($data['id_token'])) {
            $response = \Illuminate\Support\Facades\Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $data['id_token'],
            ]);

            if (! $response->successful()) {
                throw ValidationException::withMessages([
                    'id_token' => ['Token Google ID tidak valid atau telah kadaluarsa.'],
                ]);
            }

            $payload = $response->json();
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? $payload['given_name'] ?? null;
            $googleId = $payload['sub'] ?? null;
        } elseif (! empty($data['access_token'])) {
            try {
                $socialiteUser = \Laravel\Socialite\Facades\Socialite::driver('google')->userFromToken($data['access_token']);
                $email = $socialiteUser->getEmail();
                $name = $socialiteUser->getName();
                $googleId = $socialiteUser->getId();
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'access_token' => ['Token akses Google tidak valid.'],
                ]);
            }
        }

        if (! $email) {
            throw ValidationException::withMessages([
                'email' => ['Gagal mengidentifikasi email pengguna dari Google.'],
            ]);
        }

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            $user = DB::transaction(function () use ($name, $email, $googleId): User {
                $company = Company::create([
                    'name' => $name ? 'Perusahaan '.$name : 'Perusahaan Saya',
                    'email' => $email,
                    'trial_ends_at' => now()->addDays(14),
                ]);

                return User::create([
                    'company_id' => $company->id,
                    'name' => $name ?: \Illuminate\Support\Str::before($email, '@'),
                    'email' => $email,
                    'password' => Hash::make(\Illuminate\Support\Str::random(24)),
                    'role' => 'owner',
                    'google_id' => $googleId,
                    'email_verified_at' => now(),
                ]);
            });
        } else {
            if (! $user->google_id && $googleId) {
                $user->update(['google_id' => $googleId]);
            }
            if (! $user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }
        }

        return $this->tokenResponse($user->load('company'), $data['device_name'] ?? 'google-auth');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('company'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Berhasil keluar.',
        ]);
    }

    private function tokenResponse(User $user, string $deviceName, int $status = 200): JsonResponse
    {
        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $user->createToken($deviceName)->plainTextToken,
            'user' => $user,
        ], $status);
    }
}
