<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Media\Storage;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6',
        ], [
            'email.exists' => 'User does not exist.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['errors' => [
                    'password' => ['Credentials do not match.'],
                ]], 422);
            }

            $token = $user->createToken($user->name, ['user:update'])->plainTextToken;
            return response()->json(['token' => $token, 'message' => 'Logged in successfully!'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ];

            $user = User::create($userData);
            $token = $user->createToken($request->name, ['user:update'])->plainTextToken;

            event(new Registered($user));

            return response()->json(['token' => $token, 'message' => 'Registered successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'password' => 'required',
            'old_password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $user = User::findOrFail($request->id);
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['errors' => [
                    'old_password' => ['Credentials do not match.'],
                ]], 422);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            $token = $user->createToken($user->name, ['user:update'])->plainTextToken;
            return response()->json(['token' => $token, 'message' => 'Password Updated successfully!'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'email' => 'required|exists:users',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($request->id);

            if ($request->hasFile('profile')) {
                $response = Storage::upload($request->profile, User::class, $user->profile_id, $request->profile);
                if ($response['status'] === 1) {
                    $media_id = $response['data']['id'] ?? null;
                    $user->profile_id = $media_id;
                } else {
                    throw new Exception($response['message']);
                }
            }
            $user->name = $request->name;
            $user->email = $request->email;
            $user->description = $request->description;
            $user->save();
            $token = $user->createToken($user->name, ['user:update'])->plainTextToken;
            return response()->json(['token' => $token, 'message' => 'Profile updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'profile' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        try {
            $user = User::findOrFail($request->id);

            if ($request->hasFile('profile')) {
                $response = Storage::upload($request->profile, User::class, $user->profile_id, $request->profile);
                if ($response['status'] === 1) {
                    $media_id = $response['data']['id'] ?? null;
                    $user->profile_id = $media_id;
                } else {
                    throw new Exception($response['message']);
                }
            }

            $user->save();
            $token = $user->createToken($user->name, ['user:update'])->plainTextToken;
            return response()->json(['token' => $token, 'message' => 'Profile uploaded successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ],500);
        }

    }

    public function logout(Request $request)
    {
        try {
            return response()->json(['message' => 'Logged out successfully!'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
}
