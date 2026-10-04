<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Teknisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeknisiController extends Controller
{
    /**
     * Get the logged-in technician's current profile and domicile location.
     */
    public function getLocation(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $teknisi = $user->teknisi;
        if (!$teknisi) {
            // Auto create profile if user has role teknisi
            $teknisi = Teknisi::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->nama,
                    'hp' => $user->telepon ?? '081234567890',
                    'kategori' => 'AC, Kulkas, Mesin Cuci',
                    'latitude' => -6.20880000,
                    'longitude' => 106.84560000,
                    'alamat' => 'Jakarta, Indonesia',
                    'status' => 'PENDING_REVIEW',
                    'tersedia' => false,
                ]
            );
        }

        return response()->json([
            'teknisi' => $teknisi,
            'latitude' => (float) ($teknisi->latitude ?? -6.20880000),
            'longitude' => (float) ($teknisi->longitude ?? 106.84560000),
            'alamat' => $teknisi->alamat ?? 'Lokasi Belum Diatur',
        ]);
    }

    /**
     * Update the technician's domicile base coordinates and address.
     */
    public function updateLocation(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'alamat' => 'nullable|string|max:500',
        ], [
            'latitude.required' => 'Koordinat latitude wajib ditentukan.',
            'longitude.required' => 'Koordinat longitude wajib ditentukan.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teknisi = $user->teknisi;
        if (!$teknisi) {
            $teknisi = Teknisi::create([
                'user_id' => $user->id,
                'nama' => $user->nama,
                'hp' => $user->telepon ?? '081234567890',
                'kategori' => 'AC, Kulkas, Mesin Cuci',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'alamat' => $request->alamat,
                'status' => 'PENDING_REVIEW',
                'tersedia' => false,
            ]);
        } else {
            $teknisi->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'alamat' => $request->alamat ?? $teknisi->alamat,
            ]);
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'aktivitas' => "Memperbarui titik patokan domisili teknisi (Lat: {$request->latitude}, Lng: {$request->longitude})",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Titik patokan domisili tempat tinggal berhasil disimpan.',
            'teknisi' => $teknisi->fresh(),
            'latitude' => (float) $teknisi->latitude,
            'longitude' => (float) $teknisi->longitude,
            'alamat' => $teknisi->alamat,
        ]);
    }

    /**
     * Update full technician profile including category, contact and domicile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'hp' => 'nullable|string|max:30',
            'kategori' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'alamat' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update user
        $userUpdates = [
            'nama' => $request->nama,
            'telepon' => $request->hp ?? $user->telepon,
        ];
        if ($request->filled('new_password')) {
            $userUpdates['password'] = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }
        $user->update($userUpdates);

        // Update or create teknisi profile
        $teknisiData = [
            'nama' => $request->nama,
            'hp' => $request->hp ?? $user->telepon,
            'kategori' => $request->kategori ?? 'AC, Kulkas, Mesin Cuci',
        ];

        if ($request->filled('latitude')) {
            $teknisiData['latitude'] = $request->latitude;
        }
        if ($request->filled('longitude')) {
            $teknisiData['longitude'] = $request->longitude;
        }
        if ($request->filled('alamat')) {
            $teknisiData['alamat'] = $request->alamat;
        }

        $teknisi = Teknisi::updateOrCreate(
            ['user_id' => $user->id],
            $teknisiData
        );

        ActivityLog::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memperbarui profil mitra teknisi dan informasi kontak',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Profil mitra teknisi berhasil diperbarui.',
            'user' => $user->fresh(),
            'teknisi' => $teknisi->fresh(),
        ]);
    }
}
