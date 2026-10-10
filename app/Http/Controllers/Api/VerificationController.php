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
        $technicians = Teknisi::with('user')->where('status', 'PENDING_REVIEW')->get();
        $shops = Toko::with('user')->where('status', 'PENDING_REVIEW')->get();

        $newTechs = [];
        $resubTechs = [];
        $reverifTechs = [];

        foreach ($technicians as $tech) {
            $latest = PendaftaranVerifikasi::where('teknisi_id', $tech->id)
                ->latest('id')
                ->first();

            if (!$latest) {
                $newTechs[] = $tech;
            } elseif ($latest->status === 'NEEDS_REVISION') {
                $resubTechs[] = $tech;
            } else {
                $reverifTechs[] = $tech;
            }
        }

        $newShops = [];
        $resubShops = [];
        $reverifShops = [];

        foreach ($shops as $shop) {
            $latest = PendaftaranVerifikasi::where('toko_id', $shop->id)
                ->latest('id')
                ->first();

            if (!$latest) {
                $newShops[] = $shop;
            } elseif ($latest->status === 'NEEDS_REVISION') {
                $resubShops[] = $shop;
            } else {
                $reverifShops[] = $shop;
            }
        }

        return response()->json([
            'new_registrations' => [
                'technicians' => $newTechs,
                'shops' => $newShops,
            ],
            'resubmitted' => [
                'technicians' => $resubTechs,
                'shops' => $resubShops,
            ],
            'reverifications' => [
                'technicians' => $reverifTechs,
                'shops' => $reverifShops,
            ],
            // Backward compatibility for tests
            'technicians' => array_merge($newTechs, $resubTechs, $reverifTechs),
            'shops' => array_merge($newShops, $resubShops, $reverifShops),
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
                    $roleTeknisi = \App\Models\Role::firstOrCreate(['nama' => 'Teknisi']);
                    if ($profile->user) {
                        $profile->user->update(['role_id' => $roleTeknisi->id, 'is_active' => true]);
                    }
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
                    $roleToko = \App\Models\Role::firstOrCreate(['nama' => 'Toko Sparepart']);
                    if ($profile->user) {
                        $profile->user->update(['role_id' => $roleToko->id, 'is_active' => true]);
                    }
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

            // Terjemahkan status database ke pesan ramah bahasa Indonesia
            $statusText = match ($status) {
                'APPROVED' => 'Disetujui (Aktif)',
                'NEEDS_REVISION' => 'Perlu Revisi Dokumen',
                'REJECTED' => 'Ditolak',
                default => 'Sedang Diproses'
            };

            $pesanNotif = match ($status) {
                'APPROVED' => "Selamat! Pendaftaran kemitraan Anda telah disetujui oleh tim verifikator. Akun Anda kini aktif.",
                'NEEDS_REVISION' => "Dokumen pendaftaran kemitraan Anda memerlukan perbaikan. Catatan admin: {$catatan}. Silakan unggah berkas yang diminta melalui aplikasi.",
                'REJECTED' => "Mohon maaf, pendaftaran kemitraan Anda belum dapat disetujui. Catatan admin: {$catatan}",
                default => "Pendaftaran Anda telah diperbarui ke status {$statusText}."
            };

            // Create notification for the user
            Notifikasi::create([
                'user_id' => $userId,
                'judul' => 'Status Verifikasi Berkas: ' . $statusText,
                'pesan' => $pesanNotif,
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

    /**
     * Get all onboarding verifications list.
     */
    public function getAllVerifications()
    {
        $verifications = PendaftaranVerifikasi::with(['teknisi', 'toko', 'admin'])
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($verifications);
    }

    /**
     * Get decision history for the logged-in admin.
     */
    public function myDecisions(Request $request)
    {
        $admin = $request->user();
        $decisions = PendaftaranVerifikasi::with(['teknisi', 'toko'])
            ->where('admin_id', $admin->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($decisions);
    }
}
