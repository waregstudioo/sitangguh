<?php

namespace Database\Seeders;

use App\Models\AlertStatus;
use App\Models\Logistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with initial village alert status and evacuation logistics.
     */
    public function run(): void
    {
        AlertStatus::query()->updateOrCreate(
            ['id' => 1],
            [
                'level' => AlertStatus::LEVEL_SAFE,
                'headline' => 'Status Desa Kalikurmo: AMAN (Normal)',
                'message' => 'Tidak ada ancaman bencana aktif saat ini. Warga diimbau tetap memantau informasi posko dan menjaga kelancaran drainase.',
                'updated_by' => 'Satgas BPBD Desa Kalikurmo',
            ]
        );

        \App\Models\EmergencyContact::seedDefaults();

        \App\Models\User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Posko',
                'email' => 'admin@kalikurmo.desa.id',
                'password' => \Illuminate\Support\Facades\Hash::make('admin'),
            ]
        );

        $initialLogistics = [
            [
                'item_name' => 'Beras Cadangan Pangan Darurat',
                'quantity' => 450,
                'unit' => 'Kg',
                'location' => 'Posko Utama Balai Desa',
            ],
            [
                'item_name' => 'Air Mineral Kemasan Siap Minum',
                'quantity' => 85,
                'unit' => 'Kardus',
                'location' => 'Posko Utama Balai Desa',
            ],
            [
                'item_name' => 'Selimut & Matras Pengungsian',
                'quantity' => 120,
                'unit' => 'Lembar',
                'location' => 'Gudang Logistik Dusun Selatan',
            ],
            [
                'item_name' => 'Paket Obat-obatan & P3K Darurat',
                'quantity' => 35,
                'unit' => 'Paket',
                'location' => 'Pos Kesehatan Desa Kalikurmo',
            ],
        ];

        foreach ($initialLogistics as $item) {
            Logistic::query()->firstOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }
    }
}
