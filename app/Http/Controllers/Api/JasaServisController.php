<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JasaServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JasaServisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = JasaServis::orderBy('id', 'desc')->get();
        return response()->json($services);
    }

    /**
     * Get distinct active service categories and active services.
     */
    public function categories()
    {
        $services = JasaServis::where('is_active', true)->orderBy('nama_jasa', 'asc')->get();

        $categories = $services->pluck('kategori')
            ->filter()
            ->map(fn($item) => trim($item))
            ->unique()
            ->values();

        return response()->json([
            'categories' => $categories,
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jasa' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga_baseline' => 'required|numeric|min:0',
            'garansi_hari' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = JasaServis::create([
            'nama_jasa' => $request->nama_jasa,
            'kategori' => $request->kategori,
            'harga_baseline' => $request->harga_baseline,
            'garansi_hari' => $request->garansi_hari,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Kategori servis baru berhasil dibuat.',
            'service' => $service
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $service = JasaServis::find($id);
        if (!$service) {
            return response()->json(['message' => 'Kategori servis tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_jasa' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga_baseline' => 'required|numeric|min:0',
            'garansi_hari' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service->update([
            'nama_jasa' => $request->nama_jasa,
            'kategori' => $request->kategori,
            'harga_baseline' => $request->harga_baseline,
            'garansi_hari' => $request->garansi_hari,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'message' => 'Kategori servis berhasil diperbarui.',
            'service' => $service
        ], 200);
    }

    /**
     * Toggle active state.
     */
    public function toggleActive($id)
    {
        $service = JasaServis::find($id);
        if (!$service) {
            return response()->json(['message' => 'Kategori servis tidak ditemukan.'], 404);
        }

        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'message' => 'Status keaktifan kategori servis berhasil diperbarui.',
            'service' => $service
        ], 200);
    }
}
