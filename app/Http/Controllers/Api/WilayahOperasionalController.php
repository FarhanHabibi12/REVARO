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
        if (WilayahOperasional::count() === 0) {
            $defaultRegions = [
                ['nama_wilayah' => 'DKI Jakarta (Pusat, Barat, Selatan, Timur, Utara)', 'kode_pos_prefix' => '10-14'],
                ['nama_wilayah' => 'Kota & Kabupaten Tangerang', 'kode_pos_prefix' => '15'],
                ['nama_wilayah' => 'Kota Tangerang Selatan (Tangsel)', 'kode_pos_prefix' => '154'],
                ['nama_wilayah' => 'Kota & Kabupaten Bekasi', 'kode_pos_prefix' => '17'],
                ['nama_wilayah' => 'Kota Depok & Sekitarnya', 'kode_pos_prefix' => '164'],
                ['nama_wilayah' => 'Kota & Kabupaten Bogor', 'kode_pos_prefix' => '16'],
            ];
            foreach ($defaultRegions as $def) {
                WilayahOperasional::create([
                    'nama_wilayah' => $def['nama_wilayah'],
                    'kode_pos_prefix' => $def['kode_pos_prefix'],
                    'is_active' => true,
                ]);
            }
        }

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
     * Update existing region.
     */
    public function update(Request $request, $id)
    {
        $region = WilayahOperasional::find($id);
        if (!$region) {
            return response()->json(['message' => 'Wilayah operasional tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_wilayah' => 'required|string|max:255',
            'kode_pos_prefix' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $region->update([
            'nama_wilayah' => $request->nama_wilayah,
            'kode_pos_prefix' => $request->kode_pos_prefix,
        ]);

        return response()->json([
            'message' => 'Wilayah operasional berhasil diperbarui.',
            'region' => $region
        ], 200);
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
