<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KebijakanKomisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KebijakanKomisiController extends Controller
{
    /**
     * Get the active commission policy.
     */
    public function getPolicy()
    {
        $policy = KebijakanKomisi::firstOrCreate(
            ['id' => 1],
            [
                'persen_komisi_jasa' => 10.00,
                'persen_fee_sparepart' => 5.00,
            ]
        );

        return response()->json($policy);
    }

    /**
     * Update the commission policy.
     */
    public function updatePolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persen_komisi_jasa' => 'required|numeric|between:0,100',
            'persen_fee_sparepart' => 'required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policy = KebijakanKomisi::firstOrCreate(['id' => 1]);
        $policy->update([
            'persen_komisi_jasa' => $request->persen_komisi_jasa,
            'persen_fee_sparepart' => $request->persen_fee_sparepart,
        ]);

        return response()->json([
            'message' => 'Kebijakan komisi berhasil diperbarui.',
            'policy' => $policy
        ], 200);
    }
}
