<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Models\Sparepart;
use App\Models\Service;

class DiagnosisController extends Controller
{
    public function index()
    {
        $motorTypes = Sparepart::select('motor_type')->distinct()->orderBy('motor_type', 'asc')->pluck('motor_type');
        return Inertia::render('Diagnosis/Index', ['motorTypes' => $motorTypes]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|array',
            'motor_type' => 'required|string'
        ]);

        try {
            $response = Http::post('http://127.0.0.1:8001/predict', [
                'symptoms' => $request->symptoms
            ]);
            
            $aiResult = $response->json();
            $predictedParts = $aiResult['predicted_parts']; // Sekarang berupa Array dari Python
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal terhubung ke server AI. Pastikan uvicorn berjalan.']);
        }

        $partKeywordMap = [
            'Kampas Ganda CVT' => 'WEIGHT SET',
            'Roller CVT'       => 'ROLLER',
            'Kampas Rem Depan' => 'PAD SET',
            'Busi'             => 'SPARK PLUG',
            'Aki / Baterai'    => 'BATTERY',
            'Dinamo Starter'   => 'MOTOR UNIT',
        ];

        $serviceKeywordMap = [
            'Kampas Ganda CVT' => 'CVT',
            'Roller CVT'       => 'CVT',
            'Kampas Rem Depan' => 'KANVAS REM DEPAN',
            'Busi'             => 'BUSI',
            'Aki / Baterai'    => 'AKI',
            'Dinamo Starter'   => 'STATOR',
        ];

        $motorType = $request->motor_type;
        $diagnosisDetails = [];
        $grandTotal = 0;

        // LOOPING: Cari harga untuk SETIAP kerusakan yang ditebak AI
        foreach ($predictedParts as $predictedPart) {
            $partKeyword = $partKeywordMap[$predictedPart] ?? $predictedPart;
            $serviceKeyword = $serviceKeywordMap[$predictedPart] ?? $predictedPart;

            $sparepart = Sparepart::where('motor_type', $motorType)
                ->where('part_name', 'LIKE', '%' . $partKeyword . '%')
                ->first();

            $service = Service::where('motor_type', $motorType)
                ->where('service_name', 'LIKE', '%' . $serviceKeyword . '%')
                ->first();

            $partName = $sparepart ? $sparepart->part_name : $predictedPart . ' (Estimasi Umum)';
            $partPrice = $sparepart ? (float) $sparepart->price : 65000; 
            $servicePrice = $service ? (float) $service->price : 35000;
            
            $subTotal = $partPrice + $servicePrice;
            $grandTotal += $subTotal; // Tambahkan ke Grand Total

            // Simpan detail per kerusakan
            $diagnosisDetails[] = [
                'diagnosis_name' => $predictedPart,
                'part_name'      => $partName,
                'part_price'     => $partPrice,
                'service_price'  => $servicePrice,
                'sub_total'      => $subTotal
            ];
        }

        $motorTypes = Sparepart::select('motor_type')->distinct()->orderBy('motor_type', 'asc')->pluck('motor_type');

        return Inertia::render('Diagnosis/Index', [
            'motorTypes' => $motorTypes,
            'result' => [
                'details' => $diagnosisDetails,   // Daftar kerusakan
                'grand_total' => $grandTotal      // Total seluruh biaya
            ]
        ]);
    }
}