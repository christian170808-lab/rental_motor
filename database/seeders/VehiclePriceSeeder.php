<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle; // Pastikan Model Vehicle sudah ada

class VehiclePriceSeeder extends Seeder
{
    public function run()
    {
        // Masukkan data motor baru (insert)
        $vehicles = [
            ['name' => 'Yamaha Fazzio', 'type' => 'skuter', 'plate_number' => 'DK 9722 WEW', 'price_per_day' => 75000, 'status' => 'available', 'image' => 'fazio.webp'],
            ['name' => 'Honda PCX 160', 'type' => 'skuter', 'plate_number' => 'DK 9200 ROT', 'price_per_day' => 75000, 'status' => 'available', 'image' => 'pcx160.webp'],
            ['name' => 'Yamaha NMAX 155', 'type' => 'skuter', 'plate_number' => 'DK 4733 ZHZ', 'price_per_day' => 75000, 'status' => 'available', 'image' => 'yamahanmax155.webp'],
            ['name' => 'Honda Scoopy', 'type' => 'skuter', 'plate_number' => 'DK 4575 LLV', 'price_per_day' => 75000, 'status' => 'available', 'image' => 'scoopy.webp'],
            ['name' => 'Honda Stylo 160', 'type' => 'skuter', 'plate_number' => 'DK 1817 LWC', 'price_per_day' => 75000, 'status' => 'available', 'image' => 'stylo160.webp'],
            ['name' => 'Yamaha MT-25', 'type' => 'sport', 'plate_number' => 'DK 8770 DTL', 'price_per_day' => 150000, 'status' => 'available', 'image' => 'yamahamt25.webp'],
            ['name' => 'Honda CBR150R', 'type' => 'sport', 'plate_number' => 'DK 1398 FIF', 'price_per_day' => 150000, 'status' => 'available', 'image' => 'hondacbr150r.webp'],
            ['name' => 'Honda CBR250RR', 'type' => 'sport', 'plate_number' => 'DK 9078 CGU', 'price_per_day' => 150000, 'status' => 'available', 'image' => 'hondacbr250rr.webp'],
            ['name' => 'Kawasaki Ninja ZX-25R', 'type' => 'sport', 'plate_number' => 'DK 6031 BYN', 'price_per_day' => 150000, 'status' => 'available', 'image' => 'kawasakininjazx25r.webp'],
            ['name' => 'Suzuki GSX-R150', 'type' => 'sport', 'plate_number' => 'DK 6631 FIB', 'price_per_day' => 150000, 'status' => 'available', 'image' => 'suzukigsxr150.webp'],
            ['name' => 'Benelli TRK 251', 'type' => 'trail', 'plate_number' => 'DK 7855 BQB', 'price_per_day' => 175000, 'status' => 'available', 'image' => 'benellitrk251.webp'],
            ['name' => 'BMW G310GS', 'type' => 'trail', 'plate_number' => 'DK 9811 WXZ', 'price_per_day' => 175000, 'status' => 'available', 'image' => 'bmwg310gs.webp'],
            ['name' => 'Honda CB150X', 'type' => 'trail', 'plate_number' => 'DK 3570 ZFA', 'price_per_day' => 175000, 'status' => 'available', 'image' => 'hondacb150x.webp'],
            ['name' => 'Kawasaki Versys-X 250', 'type' => 'trail', 'plate_number' => 'DK 8458 YWK', 'price_per_day' => 175000, 'status' => 'available', 'image' => 'kawasakiversysx250.webp'],
            ['name' => 'Suzuki V-Strom 250 SX', 'type' => 'trail', 'plate_number' => 'DK 5667 WNT', 'price_per_day' => 175000, 'status' => 'available', 'image' => 'suzukivstrom250x.webp'],
        ];

        DB::table('vehicles')->insert($vehicles);
    }
}