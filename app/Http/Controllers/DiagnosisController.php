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
            $predictedPart = $aiResult['predicted_part']; // Contoh output: "Aki / Baterai"
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal terhubung ke server AI. Pastikan server Python (uvicorn) sedang berjalan.']);
        }

        // 3. NORMALISASI MAPPING (Menerjemahkan output AI ke Database)
        
        // A. Mapping khusus untuk tabel Sparepart (Menggunakan standar istilah Honda/Inggris di CSV)
        $partKeywordMap = [
            'Kampas Ganda CVT' => 'WEIGHT SET', // Akan cocok dengan "WEIGHT SET, CLUTCH"
            'Roller CVT'       => 'ROLLER',     // Akan cocok dengan "ROLLER WEIGHT SET"
            'Kampas Rem Depan' => 'PAD SET',    // Akan cocok dengan "PAD SET, FR BRAKE"
            'Busi'             => 'SPARK PLUG', // Akan cocok dengan "SPARK PLUG CPR9EA9"
            'Aki / Baterai'    => 'BATTERY',    // Akan cocok dengan "BATTERY(GTZ6V)"
            'Dinamo Starter'   => 'MOTOR UNIT', // Akan cocok dengan "MOTOR UNIT START"
        ];

        // B. Mapping khusus untuk tabel Service (Menggunakan istilah Indonesia di CSV)
        $serviceKeywordMap = [
            'Kampas Ganda CVT' => 'CVT',              // Akan cocok dengan "PEMBERSIHAN CVT"
            'Roller CVT'       => 'CVT',              // Akan cocok dengan "PENGGANTIAN CVT"
            'Kampas Rem Depan' => 'KANVAS REM DEPAN', // Akan cocok dengan "PENGGANTIAN KANVAS REM DEPAN"
            'Busi'             => 'BUSI',             // Akan cocok dengan "PENGGANTIAN BUSI"
            'Aki / Baterai'    => 'AKI',              // Akan cocok dengan "PENGGANTIAN AKI"
            'Dinamo Starter'   => 'STATOR',           // Akan cocok dengan "PERBAIKAN STATOR COMP"
        ];

        // Tentukan keyword pencarian masing-masing
        $partKeyword = $partKeywordMap[$predictedPart] ?? $predictedPart;
        $serviceKeyword = $serviceKeywordMap[$predictedPart] ?? $predictedPart;

        // 4. Ambil Harga Suku Cadang dan Jasa dari Database MariaDB
        $motorType = $request->motor_type;

        // Pencarian Sparepart menggunakan $partKeyword
        $sparepart = Sparepart::where('motor_type', $motorType)
            ->where('part_name', 'LIKE', '%' . $partKeyword . '%')
            ->first();

        // Pencarian Service menggunakan $serviceKeyword
        $service = Service::where('motor_type', $motorType)
            ->where('service_name', 'LIKE', '%' . $serviceKeyword . '%')
            ->first();

        // 5. Kalkulasi Biaya 
        $partName = $sparepart ? $sparepart->part_name : $predictedPart . ' (Estimasi Umum)';
        $partPrice = $sparepart ? (float) $sparepart->price : 65000; 
        $servicePrice = $service ? (float) $service->price : 35000;

        $totalPrice = $partPrice + $servicePrice;

        // 6. Kembalikan data hasil kalkulasi ke tampilan Vue.js
        $motorTypes = Sparepart::select('motor_type')->distinct()->orderBy('motor_type', 'asc')->pluck('motor_type');

        return Inertia::render('Diagnosis/Index', [
            'motorTypes' => $motorTypes,
            'result' => [
                'diagnosis' => $predictedPart, // Tetap tampilkan nama cantik dari AI di nota
                'part_name' => $partName,      // Tampilkan nama spesifik part dari database
                'part_price' => $partPrice,
                'service_price' => $servicePrice,
                'total_price' => $totalPrice
            ]
        ]);
    }
}