<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasienProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'alamat'          => 'required|string',
            'telepon'         => 'required|string',
            'nik'             => 'nullable|string',
            'tanggal_lahir'   => 'nullable|date',
            'jenis_kelamin'   => 'nullable|in:L,P',
            'golongan_darah'  => 'nullable|string',
            'alergi'          => 'nullable|string',
        ]);

        if ($request->jenis_kelamin) {
            $request->merge([
                'jenis_kelamin' => $request->jenis_kelamin === 'L' ? 'laki-laki' : 'perempuan',
            ]);
        }

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'phone' => $request->telepon,
            ]);

            $user->pasien()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only([
                    'alamat',
                    'telepon',
                    'nik',
                    'tanggal_lahir',
                    'jenis_kelamin',
                    'golongan_darah',
                    'alergi',
                ])
            );
        });

        return response()->json([
            'message' => 'Profil pasien berhasil diperbarui',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'pasien' => $user->pasien,
            ],
        ]);
    }
}
