<?php

namespace App\Services;

use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StorageCleanerService
{
    /**
     * Scan disk and database to find orphan files.
     */
    public function scanOrphans(): array
    {
        $allFiles = Storage::disk('public')->allFiles();
        
        // Also scan local disk for private uploads if exists
        $allPrivateFiles = Storage::disk('local')->allFiles('private');

        $referencedFiles = $this->getReferencedFilesFromDatabase();

        $orphanFiles = [];
        $totalOrphanBytes = 0;
        $totalStorageBytes = 0;

        foreach ($allFiles as $file) {
            $fileSizeBytes = Storage::disk('public')->size($file);
            $totalStorageBytes += $fileSizeBytes;

            // Check if file is referenced in DB
            $isReferenced = false;
            foreach ($referencedFiles as $ref) {
                if ($ref && str_contains($ref, $file)) {
                    $isReferenced = true;
                    break;
                }
            }

            if (!$isReferenced) {
                $orphanFiles[] = [
                    'disk' => 'public',
                    'path' => $file,
                    'full_path' => Storage::disk('public')->path($file),
                    'size_bytes' => $fileSizeBytes,
                    'size_formatted' => $this->formatBytes($fileSizeBytes),
                ];
                $totalOrphanBytes += $fileSizeBytes;
            }
        }

        foreach ($allPrivateFiles as $file) {
            $fileSizeBytes = Storage::disk('local')->size($file);
            $totalStorageBytes += $fileSizeBytes;

            $isReferenced = false;
            foreach ($referencedFiles as $ref) {
                if ($ref && str_contains($ref, $file)) {
                    $isReferenced = true;
                    break;
                }
            }

            if (!$isReferenced) {
                $orphanFiles[] = [
                    'disk' => 'local',
                    'path' => $file,
                    'full_path' => Storage::disk('local')->path($file),
                    'size_bytes' => $fileSizeBytes,
                    'size_formatted' => $this->formatBytes($fileSizeBytes),
                ];
                $totalOrphanBytes += $fileSizeBytes;
            }
        }

        return [
            'total_files_scanned' => count($allFiles) + count($allPrivateFiles),
            'orphan_files_count' => count($orphanFiles),
            'total_storage_size' => $this->formatBytes($totalStorageBytes),
            'total_storage_size_bytes' => $totalStorageBytes,
            'orphan_size_freed' => $this->formatBytes($totalOrphanBytes),
            'orphan_size_freed_bytes' => $totalOrphanBytes,
            'orphan_files' => $orphanFiles,
        ];
    }

    /**
     * Delete detected orphan files safely.
     */
    public function cleanOrphans(): array
    {
        $scan = $this->scanOrphans();
        $deletedCount = 0;
        $deletedBytes = 0;
        $failedFiles = [];

        foreach ($scan['orphan_files'] as $orphan) {
            try {
                if (Storage::disk($orphan['disk'])->exists($orphan['path'])) {
                    Storage::disk($orphan['disk'])->delete($orphan['path']);
                    $deletedCount++;
                    $deletedBytes += $orphan['size_bytes'];
                }
            } catch (\Throwable $e) {
                $failedFiles[] = [
                    'path' => $orphan['path'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'deleted_count' => $deletedCount,
            'freed_space' => $this->formatBytes($deletedBytes),
            'freed_space_bytes' => $deletedBytes,
            'failed_files' => $failedFiles,
        ];
    }

    /**
     * Get list of all file paths referenced in DB.
     */
    protected function getReferencedFilesFromDatabase(): array
    {
        $paths = [];

        // Users foto
        $userFotos = User::whereNotNull('foto')->pluck('foto')->toArray();
        $paths = array_merge($paths, $userFotos);

        // Teknisi sertifikat & ktp
        $teknisiFiles = Teknisi::select('sertifikat', 'ktp')->get();
        foreach ($teknisiFiles as $t) {
            if ($t->sertifikat) $paths[] = $t->sertifikat;
            if ($t->ktp) $paths[] = $t->ktp;
        }

        // Toko dokumen_izin
        $tokoFiles = Toko::whereNotNull('dokumen_izin')->pluck('dokumen_izin')->toArray();
        $paths = array_merge($paths, $tokoFiles);

        return array_unique(array_filter($paths));
    }

    /**
     * Format bytes to readable string (KB/MB/GB).
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
