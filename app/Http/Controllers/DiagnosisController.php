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
        // Mengambil daftar tipe motor yang unik dan diurutkan sesuai abjad dari database
        $motorTypes = Sparepart::select('motor_type')
            ->distinct()
            ->orderBy('motor_type', 'asc')
            ->pluck('motor_type');

        // Menampilkan halaman Vue.js sambil mengirim data tipe motor
        return Inertia::render('Diagnosis/Index', [
            'motorTypes' => $motorTypes
        ]);
    }

    public function calculate(Request $request)
    {
        // 1. Validasi input dari Vue.js
        $request->validate([
            'symptoms' => 'required|array',
            'motor_type' => 'required|string'
        ]);

        // 2. Kirim data array gejala ke API Python (AI Microservice)
        try {
            $response = Http::post('http://127.0.0.1:8001/predict', [
                'symptoms' => $request->symptoms
            ]);
            
            $aiResult = $response->json();
            $predictedParts = $aiResult['predicted_parts']; // Array dari Python (Multi-Diagnosis)
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal terhubung ke server AI. Pastikan server Python (uvicorn) sedang berjalan.']);
        }

        // 3. NORMALISASI MAPPING (Diperbarui untuk 10 Kategori)
        
        // A. Mapping khusus untuk tabel Sparepart
        $partKeywordMap = [
            'Kampas Ganda CVT'       => 'WEIGHT SET',
            'Roller CVT'             => 'ROLLER',
            'Kampas Rem Depan'       => 'PAD SET',
            'Busi'                   => 'SPARK PLUG',
            'Aki / Baterai'          => 'BATTERY',
            'Dinamo Starter'         => 'MOTOR UNIT',
            'Komstir / Steering'     => 'RACE STEERING',
            'Oli & Seal Shock Depan' => 'SHOCK',
            'Air Radiator (Coolant)' => 'COOLANT',
            'Filter Udara'           => 'ELEMENT',
        ];

        // B. Mapping khusus untuk tabel Service
        $serviceKeywordMap = [
            'Kampas Ganda CVT'       => 'CVT',
            'Roller CVT'             => 'CVT',
            'Kampas Rem Depan'       => 'KANVAS REM DEPAN',
            'Busi'                   => 'BUSI',
            'Aki / Baterai'          => 'AKI',
            'Dinamo Starter'         => 'STATOR',
            'Komstir / Steering'     => 'STEER',
            'Oli & Seal Shock Depan' => 'OLI SHOCK',
            'Air Radiator (Coolant)' => 'RADIATOR',
            'Filter Udara'           => 'SARINGAN UDARA',
        ];

        $motorType = $request->motor_type;
        $diagnosisDetails = [];
        $grandTotal = 0;

        // 4. LOOPING: Cari harga untuk SETIAP kerusakan yang ditebak AI
        foreach ($predictedParts as $predictedPart) {
            
            $partKeyword = $partKeywordMap[$predictedPart] ?? $predictedPart;
            $serviceKeyword = $serviceKeywordMap[$predictedPart] ?? $predictedPart;

            // Pencarian Sparepart
            $sparepart = Sparepart::where('motor_type', $motorType)
                ->where('part_name', 'LIKE', '%' . $partKeyword . '%')
                ->first();

            // Pencarian Service
            $service = Service::where('motor_type', $motorType)
                ->where('service_name', 'LIKE', '%' . $serviceKeyword . '%')
                ->first();

            // Kalkulasi per item
            $partName = $sparepart ? $sparepart->part_name : $predictedPart . ' (Estimasi Umum)';
            $partPrice = $sparepart ? (float) $sparepart->price : 65000; 
            $servicePrice = $service ? (float) $service->price : 35000;
            
            $subTotal = $partPrice + $servicePrice;
            $grandTotal += $subTotal; // Tambahkan ke Grand Total

            // Simpan rincian ke dalam array
            $diagnosisDetails[] = [
                'diagnosis_name' => $predictedPart,
                'part_name'      => $partName,
                'part_price'     => $partPrice,
                'service_price'  => $servicePrice,
                'sub_total'      => $subTotal
            ];
        }

        // 5. Kembalikan data hasil kalkulasi ke tampilan Vue.js
        $motorTypes = Sparepart::select('motor_type')->distinct()->orderBy('motor_type', 'asc')->pluck('motor_type');

        return Inertia::render('Diagnosis/Index', [
            'motorTypes' => $motorTypes,
            'result' => [
                'details' => $diagnosisDetails,   
                'grand_total' => $grandTotal      
            ]
        ]);
    }
}