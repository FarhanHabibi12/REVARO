<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartSearchController extends Controller
{
    /**
     * Search spareparts by query, showing prices and shops.
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        $tokoId = $request->query('toko_id');
        $includeAll = $request->query('all') || $tokoId;

        $spareparts = Sparepart::with('toko')
            ->when(!$includeAll, function ($q) {
                return $q->where('stok', '>', 0);
            })
            ->when($tokoId, function ($q) use ($tokoId) {
                return $q->where('toko_id', $tokoId);
            })
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($sub) use ($query) {
                    $sub->where('nama', 'like', "%{$query}%")
                        ->orWhere('merk', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->get();

        return response()->json($spareparts);
    }
}
