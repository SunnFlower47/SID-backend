<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\Models\PendudukDomisili;
use Illuminate\Http\Request;

class CheckDomisiliController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $nik = $request->get('nik');
        
        if (!$nik || strlen($nik) < 16) {
            return response()->json(['exists' => false]);
        }

        $domisili = PendudukDomisili::where('nik', $nik)->first();

        if ($domisili) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'nama' => $domisili->nama,
                    'tempat_lahir' => $domisili->tempat_lahir,
                    'tanggal_lahir' => $domisili->tanggal_lahir?->toDateString(),
                    'jenis_kelamin' => $domisili->jenis_kelamin,
                    'agama' => $domisili->agama,
                    'status_perkawinan' => $domisili->status_perkawinan,
                    'pekerjaan' => $domisili->pekerjaan,
                    'kewarganegaraan' => $domisili->kewarganegaraan,
                    'alamat_asal' => $domisili->alamat_asal,
                    'alamat_tinggal' => $domisili->alamat_tinggal,
                    'rt_id' => $domisili->rt_id,
                    'rw_id' => $domisili->rw_id,
                    'dusun_id' => $domisili->dusun_id,
                    'tanggal_masuk' => $domisili->tanggal_masuk?->toDateString(),
                    'tanggal_berlaku' => $domisili->tanggal_berlaku?->toDateString(),
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }
}
