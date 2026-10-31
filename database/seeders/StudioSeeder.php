<?php

namespace Database\Seeders;

use App\Enums\ClassKind;
use App\Enums\ClassLevel;
use App\Models\ClassType;
use App\Models\Instructor;
use App\Models\Package;
use Illuminate\Database\Seeder;

/**
 * Data tetap studio: instruktur, jenis kelas, paket.
 * Semua nama dan cerita di sini fiktif.
 */
class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->instructors();
        $this->classTypes();
        $this->packages();
    }

    private function instructors(): void
    {
        $instructors = [
            [
                'name' => 'Sekar',
                'slug' => 'sekar',
                'tagline' => 'Teman pertama kamu di matras.',
                'bio' => 'Sekar mengajar kelas pertama hampir setiap minggu. Dia percaya gerakan paling penting hari ini adalah datang.',
                'specialties' => ['Mat', 'Kelas pertama'],
                'figure' => 1,
            ],
            [
                'name' => 'Bima',
                'slug' => 'bima',
                'tagline' => 'Reformer itu ayunan untuk orang dewasa.',
                'bio' => 'Bima suka menjelaskan alat dengan perumpamaan taman bermain. Kelasnya ramai tawa, jarang ramai hitungan.',
                'specialties' => ['Reformer'],
                'figure' => 0,
            ],
            [
                'name' => 'Nadia',
                'slug' => 'nadia',
                'tagline' => 'Napas dulu, sisanya menyusul.',
                'bio' => 'Nadia memulai pilates setelah cedera punggung. Dia paham rasanya ragu, dan selalu punya versi lain dari setiap gerakan.',
                'specialties' => ['Mat', 'Mobilitas'],
                'figure' => 5,
            ],
            [
                'name' => 'Yosef',
                'slug' => 'yosef',
                'tagline' => 'Pelan-pelan juga sampai.',
                'bio' => 'Yosef senang memperhatikan detail kecil: posisi kaki, bahu yang naik diam-diam. Koreksinya pelan, tidak pernah keras.',
                'specialties' => ['Reformer', 'Postur'],
                'figure' => 4,
            ],
        ];

        foreach ($instructors as $sort => $instructor) {
            Instructor::query()->updateOrCreate(
                ['slug' => $instructor['slug']],
                [...$instructor, 'sort' => $sort],
            );
        }
    }

    private function classTypes(): void
    {
        $classTypes = [
            [
                'name' => 'Kenalan Dulu',
                'slug' => 'kenalan-dulu',
                'kind' => ClassKind::Mat,
                'level' => ClassLevel::BaruKenal,
                'summary' => 'Kelas pertama: kenalan sama matras, napas, dan kami.',
                'description' => 'Kita mulai dari nol. Cara bernapas, cara berbaring, cara bilang "boleh pelan?". Tidak ada yang perlu dibuktikan.',
                'duration_minutes' => 50,
                'capacity' => 8,
                'is_intro' => true,
                'color' => 'lime',
            ],
            [
                'name' => 'Mat Santai',
                'slug' => 'mat-santai',
                'kind' => ClassKind::Mat,
                'level' => ClassLevel::BaruKenal,
                'summary' => 'Gerakan dasar di matras, tempo santai.',
                'description' => 'Rangkaian dasar yang diulang dengan sabar. Cocok untuk pagi yang malas atau sore yang penat.',
                'duration_minutes' => 50,
                'capacity' => 10,
                'is_intro' => false,
                'color' => 'pink',
            ],
            [
                'name' => 'Reformer Ringan',
                'slug' => 'reformer-ringan',
                'kind' => ClassKind::Reformer,
                'level' => ClassLevel::BaruKenal,
                'summary' => 'Coba reformer pertama kali, per-pernya dibuat ringan.',
                'description' => 'Kenalan dengan kereta yang bisa meluncur. Kita tunjukkan setiap tali dan per sebelum mulai.',
                'duration_minutes' => 50,
                'capacity' => 6,
                'is_intro' => false,
                'color' => 'blue',
            ],
            [
                'name' => 'Mat Mengalir',
                'slug' => 'mat-mengalir',
                'kind' => ClassKind::Mat,
                'level' => ClassLevel::MulaiAkrab,
                'summary' => 'Gerakan bersambung tanpa banyak jeda.',
                'description' => 'Sudah hafal dasarnya? Di sini gerakan mulai saling menyambung, seperti menari pelan.',
                'duration_minutes' => 55,
                'capacity' => 10,
                'is_intro' => false,
                'color' => 'lime',
            ],
            [
                'name' => 'Reformer Main-main',
                'slug' => 'reformer-main-main',
                'kind' => ClassKind::Reformer,
                'level' => ClassLevel::MulaiAkrab,
                'summary' => 'Variasi reformer yang lebih berani, tetap riang.',
                'description' => 'Lompat kecil di jumpboard, keseimbangan di kereta. Banyak kejutan, sedikit hitungan.',
                'duration_minutes' => 55,
                'capacity' => 6,
                'is_intro' => false,
                'color' => 'pink',
            ],
            [
                'name' => 'Reformer Lengket',
                'slug' => 'reformer-lengket',
                'kind' => ClassKind::Reformer,
                'level' => ClassLevel::SudahLengket,
                'summary' => 'Untuk yang sudah akrab dengan alatnya.',
                'description' => 'Rangkaian panjang dan transisi cepat. Kelas kecil, jadi instruktur bisa menemani tiap orang.',
                'duration_minutes' => 55,
                'capacity' => 5,
                'is_intro' => false,
                'color' => 'blue',
            ],
        ];

        foreach ($classTypes as $sort => $classType) {
            ClassType::query()->updateOrCreate(
                ['slug' => $classType['slug']],
                [...$classType, 'sort' => $sort],
            );
        }
    }

    private function packages(): void
    {
        $packages = [
            [
                'name' => 'Kelas Pertama',
                'slug' => 'kelas-pertama',
                'summary' => 'Satu kelas apa saja untuk kenalan. Sekali per orang.',
                'class_count' => 1,
                'price' => 99_000,
                'validity_days' => 14,
                'is_intro' => true,
            ],
            [
                'name' => 'Sekali Datang',
                'slug' => 'sekali-datang',
                'summary' => 'Satu kelas, tanpa komitmen.',
                'class_count' => 1,
                'price' => 175_000,
                'validity_days' => 30,
            ],
            [
                'name' => 'Gerak Berdua',
                'slug' => 'gerak-berdua',
                'summary' => 'Dua kelas untuk kamu dan teman. Datang bareng, pulang bareng.',
                'class_count' => 2,
                'price' => 300_000,
                'validity_days' => 30,
                'is_shareable' => true,
            ],
            [
                'name' => 'Paket 5 Kelas',
                'slug' => 'paket-5',
                'summary' => 'Lima kelas dalam dua bulan. Pas untuk mulai rutin.',
                'class_count' => 5,
                'price' => 800_000,
                'validity_days' => 60,
            ],
            [
                'name' => 'Paket 10 Kelas',
                'slug' => 'paket-10',
                'summary' => 'Sepuluh kelas dalam tiga bulan. Paling hemat per kelas.',
                'class_count' => 10,
                'price' => 1_450_000,
                'validity_days' => 90,
            ],
        ];

        foreach ($packages as $sort => $package) {
            Package::query()->updateOrCreate(
                ['slug' => $package['slug']],
                [
                    'is_intro' => false,
                    'is_shareable' => false,
                    ...$package,
                    'sort' => $sort,
                ],
            );
        }
    }
}
