<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username'    => ['required','string'],
            'password' => ['required','string','min:6'],
        ]);

        // Cari user berdasarkan email
        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 422);
        }

        // Cek status & role
        if ($user->status !== 'active') {
            return response()->json(['message' => 'Akun belum terverifikasi.'], 403);
        }

        if ((int)$user->role_id !== 1) {
            return response()->json(['message' => 'Akses ditolak untuk role ini.'], 403);
        }

        // Login & regenerate session
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message'  => 'Berhasil masuk.',
            'redirect' => route('homepage'),
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/signin'); // atau return response()->json([...])
    }

    public function regist(Request $request)
    {
        try {
            $data = $request->validate([
                'name'     => 'required|string|max:100',
                'username' => 'required|string|max:50|unique:users,username',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'status'   => 'nullable|in:active,inactive,suspended,pending',
                'role_id'  => 'nullable|integer',
            ]);

            // set default yang valid
            $data['status']  = $data['status'] ?? 'pending'; // ← enum valid
            $data['role_id'] = $data['role_id'] ?? 2;        // ← pastikan role id=2 ADA
            $data['password'] = Hash::make($data['password']);

            $user = User::create([
                'name'     => $data['name'],
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'role_id'  => $data['role_id'],
                'status'   => $data['status'],
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user'    => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            \Log::error('Register DB error: '.$e->getMessage());
            return response()->json(['message' => 'Database error'], 500);
        } catch (\Throwable $e) {
            \Log::error('Register fatal: '.$e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}