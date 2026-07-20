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
            $predictedPart = $aiResult['predicted_part']; // Contoh output: "Kampas Rem Depan"
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal terhubung ke server AI. Pastikan server Python (uvicorn) sedang berjalan.']);
        }

        // 3. Ambil Harga Suku Cadang dan Jasa dari Database MariaDB
        $motorType = $request->motor_type;

        // Pencarian harga berdasarkan tipe motor dan tebakan AI
        $sparepart = Sparepart::where('motor_type', $motorType)
            ->where('part_name', 'LIKE', '%' . $predictedPart . '%')
            ->first();

        $service = Service::where('motor_type', $motorType)
            ->where('service_name', 'LIKE', '%' . $predictedPart . '%')
            ->first();

        // 4. Kalkulasi Biaya (Gunakan harga estimasi default jika part tidak ditemukan di database)
        $partName = $sparepart ? $sparepart->part_name : $predictedPart . ' (Estimasi Umum)';
        $partPrice = $sparepart ? (float) $sparepart->price : 65000; 
        $servicePrice = $service ? (float) $service->price : 35000;

        $totalPrice = $partPrice + $servicePrice;

        // 5. Kembalikan data hasil kalkulasi ke tampilan Vue.js beserta daftar tipe motor agar dropdown tidak hilang
        $motorTypes = Sparepart::select('motor_type')->distinct()->orderBy('motor_type', 'asc')->pluck('motor_type');

        return Inertia::render('Diagnosis/Index', [
            'motorTypes' => $motorTypes,
            'result' => [
                'diagnosis' => $predictedPart,
                'part_name' => $partName,
                'part_price' => $partPrice,
                'service_price' => $servicePrice,
                'total_price' => $totalPrice
            ]
        ]);
    }
}