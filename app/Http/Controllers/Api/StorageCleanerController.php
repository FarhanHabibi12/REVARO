<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StorageCleanerService;
use Illuminate\Http\Request;

class StorageCleanerController extends Controller
{
    protected StorageCleanerService $cleanerService;

    public function __construct(StorageCleanerService $cleanerService)
    {
        $this->cleanerService = $cleanerService;
    }

    /**
     * Get orphan files scan metrics & details.
     */
    public function getOrphans()
    {
        $scanResult = $this->cleanerService->scanOrphans();
        return response()->json($scanResult);
    }

    /**
     * Delete orphan files safely.
     */
    public function cleanOrphans()
    {
        $cleanResult = $this->cleanerService->cleanOrphans();

        \App\Services\SecurityLoggerService::log(
            event: 'STORAGE_CLEANUP',
            details: "Cleaned {$cleanResult['deleted_count']} orphan files freed {$cleanResult['freed_space']}"
        );

        return response()->json([
            'message' => 'Pembersihan berkas orphan penyimpanan berhasil dilakukan.',
            'result' => $cleanResult
        ]);
    }
}
