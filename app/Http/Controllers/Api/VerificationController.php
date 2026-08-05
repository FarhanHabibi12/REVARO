<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\PendaftaranVerifikasi;
use App\Models\Teknisi;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    /**
     * Display a queue of profiles awaiting verification.
     */
    public function index()
    {
        $technicians = Teknisi::where('status', 'PENDING_REVIEW')->get();
        $shops = Toko::where('status', 'PENDING_REVIEW')->get();

        return response()->json([
            'technicians' => $technicians,
            'shops' => $shops,
        ]);
    }

    /**
     * Submit a verification decision for a technician or shop.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target_type' => 'required|in:teknisi,toko',
            'target_id' => 'required|integer',
            'status' => 'required|in:APPROVED,REJECTED,NEEDS_REVISION',
            'catatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = $request->user();
        $targetType = $request->target_type;
        $targetId = $request->target_id;
        $status = $request->status;
        $catatan = $request->catatan;

        return DB::transaction(function () use ($admin, $targetType, $targetId, $status, $catatan, $request) {
            $profile = null;
            $userId = null;

            if ($targetType === 'teknisi') {
                $profile = Teknisi::find($targetId);
                if (!$profile) {
                    return response()->json(['message' => 'Profil Teknisi tidak ditemukan.'], 404);
                }
                $userId = $profile->user_id;

                // Map verification decision to profile status
                if ($status === 'APPROVED') {
                    $profile->status = 'ACTIVE';
                } elseif ($status === 'NEEDS_REVISION') {
                    $profile->status = 'NEEDS_REVISION';
                } elseif ($status === 'REJECTED') {
                    $profile->status = 'REJECTED';
                    $profile->user->update(['is_active' => false]);
                }
                $profile->save();

                // Save verification record
                PendaftaranVerifikasi::create([
                    'teknisi_id' => $profile->id,
                    'toko_id' => null,
                    'admin_id' => $admin->id,
                    'status' => $status,
                    'catatan' => $catatan,
                    'verified_at' => now(),
                ]);

            } else {
                $profile = Toko::find($targetId);
                if (!$profile) {
                    return response()->json(['message' => 'Profil Toko tidak ditemukan.'], 404);
                }
                $userId = $profile->user_id;

                // Map verification decision to profile status
                if ($status === 'APPROVED') {
                    $profile->status = 'ACTIVE';
                } elseif ($status === 'NEEDS_REVISION') {
                    $profile->status = 'NEEDS_REVISION';
                } elseif ($status === 'REJECTED') {
                    $profile->status = 'REJECTED';
                    $profile->user->update(['is_active' => false]);
                }
                $profile->save();

                // Save verification record
                PendaftaranVerifikasi::create([
                    'teknisi_id' => null,
                    'toko_id' => $profile->id,
                    'admin_id' => $admin->id,
                    'status' => $status,
                    'catatan' => $catatan,
                    'verified_at' => now(),
                ]);
            }

            // Create notification for the user
            Notifikasi::create([
                'user_id' => $userId,
                'judul' => 'Status Verifikasi Pendaftaran',
                'pesan' => "Pendaftaran Anda telah diproses. Hasil: {$status}. Catatan: {$catatan}",
                'dibaca' => false,
            ]);

            // Create activity log
            ActivityLog::create([
                'user_id' => $admin->id,
                'aktivitas' => "Melakukan keputusan verifikasi {$status} untuk {$targetType} ID: {$targetId}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Keputusan verifikasi berhasil disimpan.',
                'profile' => $profile,
            ]);
        });
    }
}
