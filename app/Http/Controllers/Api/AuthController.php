<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:6|confirmed',

            'tanggal_lahir'    => 'required|date',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'alamat'           => 'required|string',
            'telepon'          => 'required|string',
            'nik'              => 'nullable|unique:pasien,nik',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('pasien');

            Pasien::create([
                'user_id'            => $user->id,
                'nomor_rekam_medis'  => 'RM-' . now()->format('Ymd') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'nik'                => $request->nik,
                'tanggal_lahir'      => $request->tanggal_lahir,
                'jenis_kelamin'      => $request->jenis_kelamin,
                'alamat'             => $request->alamat,
                'telepon'            => $request->telepon,
                'kontak_darurat'     => $request->telepon,
                'telepon_darurat'    => $request->telepon,
                'golongan_darah'     => $request->golongan_darah,
                'alergi'             => $request->alergi,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registrasi pasien berhasil'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$token = Auth::guard('api')->attempt(
            $request->only('email', 'password')
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = Auth::guard('api')->user()->load('pasien');

        if (!$user->hasRole('pasien')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'type'    => 'Bearer',
            'user'    => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone,
                'pasien' => $user->pasien, // 🔥 INI KUNCI
            ],
        ]);
    }


    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
