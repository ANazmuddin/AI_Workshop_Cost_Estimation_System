<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Data Sparepart
        $sparepartsCsv = database_path('data/master_spareparts.csv');
        if (file_exists($sparepartsCsv)) {
            $file = fopen($sparepartsCsv, 'r');
            $isHeader = true;
            while (($data = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }
                DB::table('spareparts')->insert([
                    'part_id' => $data[0],
                    'part_name' => $data[1],
                    'motor_type' => $data[2],
                    'price' => $data[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            fclose($file);
            $this->command->info('Data Sparepart berhasil di-seed!');
        }

        // 2. Seed Data Service (Jasa)
        $servicesCsv = database_path('data/master_services.csv');
        if (file_exists($servicesCsv)) {
            $file = fopen($servicesCsv, 'r');
            $isHeader = true;
            while (($data = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }
                DB::table('services')->insert([
                    'service_name' => $data[0],
                    'motor_type' => $data[1],
                    'price' => $data[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            fclose($file);
            $this->command->info('Data Service berhasil di-seed!');
        }
    }
}