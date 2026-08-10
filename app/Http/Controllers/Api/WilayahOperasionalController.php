<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WilayahOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WilayahOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = WilayahOperasional::orderBy('id', 'desc')->get();
        return response()->json($regions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_wilayah' => 'required|string|max:255',
            'kode_pos_prefix' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $region = WilayahOperasional::create([
            'nama_wilayah' => $request->nama_wilayah,
            'kode_pos_prefix' => $request->kode_pos_prefix,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Wilayah operasional baru berhasil ditambahkan.',
            'region' => $region
        ], 201);
    }

    /**
     * Toggle active state.
     */
    public function toggleActive($id)
    {
        $region = WilayahOperasional::find($id);
        if (!$region) {
            return response()->json(['message' => 'Wilayah operasional tidak ditemukan.'], 404);
        }

        $region->is_active = !$region->is_active;
        $region->save();

        return response()->json([
            'message' => 'Status keaktifan wilayah operasional berhasil diperbarui.',
            'region' => $region
        ], 200);
    }
}
