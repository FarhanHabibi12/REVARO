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

        $spareparts = Sparepart::with('toko')
            ->where('stok', '>', 0)
            ->when($query, function ($q) use ($query) {
                return $q->where('nama', 'like', "%{$query}%")
                         ->orWhere('merk', 'like', "%{$query}%");
            })
            ->get();

        return response()->json($spareparts);
    }
}
