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
                'tagline_en' => 'Your first friend on the mat.',
                'bio_en' => 'Sekar teaches the first-timer class almost every week. She believes the most important move today is showing up.',
                'specialties_en' => ['Mat', 'First class'],
                'tagline' => 'Teman pertama kamu di matras.',
                'bio' => 'Sekar mengajar kelas pertama hampir setiap minggu. Dia percaya gerakan paling penting hari ini adalah datang.',
                'specialties' => ['Mat', 'Kelas pertama'],
                'figure' => 1,
            ],
            [
                'name' => 'Bima',
                'slug' => 'bima',
                'tagline_en' => 'A reformer is a swing for grown-ups.',
                'bio_en' => 'Bima explains the equipment with playground metaphors. His classes are full of laughter, not counting.',
                'specialties_en' => ['Reformer'],
                'tagline' => 'Reformer itu ayunan untuk orang dewasa.',
                'bio' => 'Bima suka menjelaskan alat dengan perumpamaan taman bermain. Kelasnya ramai tawa, jarang ramai hitungan.',
                'specialties' => ['Reformer'],
                'figure' => 0,
            ],
            [
                'name' => 'Nadia',
                'slug' => 'nadia',
                'tagline_en' => 'Breathe first, the rest will follow.',
                'bio_en' => 'Nadia started pilates after a back injury. She knows what hesitation feels like, and always has another version of every move.',
                'specialties_en' => ['Mat', 'Mobility'],
                'tagline' => 'Napas dulu, sisanya menyusul.',
                'bio' => 'Nadia memulai pilates setelah cedera punggung. Dia paham rasanya ragu, dan selalu punya versi lain dari setiap gerakan.',
                'specialties' => ['Mat', 'Mobilitas'],
                'figure' => 5,
            ],
            [
                'name' => 'Yosef',
                'slug' => 'yosef',
                'tagline_en' => 'Slow still gets you there.',
                'bio_en' => 'Yosef notices the small things: where your feet are, a shoulder quietly creeping up. His cues are gentle, never loud.',
                'specialties_en' => ['Reformer', 'Posture'],
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
                'name_en' => 'Say Hello',
                'summary_en' => 'First class: meet the mat, your breath, and us.',
                'description_en' => 'We start from zero. How to breathe, how to lie down, how to say "can we go slower?". Nothing to prove.',
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
                'name_en' => 'Easy Mat',
                'summary_en' => 'Basic mat moves at a relaxed pace.',
                'description_en' => 'A basic sequence, repeated patiently. Good for lazy mornings or tired evenings.',
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
                'name_en' => 'Light Reformer',
                'summary_en' => 'Try the reformer for the first time, with light springs.',
                'description_en' => 'Meet the gliding carriage. We show you every strap and spring before we start.',
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
                'name_en' => 'Flowing Mat',
                'summary_en' => 'Moves that link together with few pauses.',
                'description_en' => 'Know the basics already? Here the moves start to connect, like a slow dance.',
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
                'name_en' => 'Playful Reformer',
                'summary_en' => 'Bolder reformer variations, still cheerful.',
                'description_en' => 'Small jumps on the jumpboard, balance on the carriage. Lots of surprises, little counting.',
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
                'name_en' => 'Close-Friends Reformer',
                'summary_en' => 'For those already at home on the machine.',
                'description_en' => 'Long sequences and quick transitions. Small class, so the instructor can stay with everyone.',
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
                'name_en' => 'First Class',
                'summary_en' => 'Any one class to get to know us. Once per person.',
                'summary' => 'Satu kelas apa saja untuk kenalan. Sekali per orang.',
                'class_count' => 1,
                'price' => 99_000,
                'validity_days' => 14,
                'is_intro' => true,
            ],
            [
                'name' => 'Sekali Datang',
                'slug' => 'sekali-datang',
                'name_en' => 'Drop-in',
                'summary_en' => 'One class, no commitment.',
                'summary' => 'Satu kelas, tanpa komitmen.',
                'class_count' => 1,
                'price' => 175_000,
                'validity_days' => 30,
            ],
            [
                'name' => 'Gerak Berdua',
                'slug' => 'gerak-berdua',
                'name_en' => 'Move Together',
                'summary_en' => 'Two classes for you and a friend. Arrive together, leave together.',
                'summary' => 'Dua kelas untuk kamu dan teman. Datang bareng, pulang bareng.',
                'class_count' => 2,
                'price' => 300_000,
                'validity_days' => 30,
                'is_shareable' => true,
            ],
            [
                'name' => 'Paket 5 Kelas',
                'slug' => 'paket-5',
                'name_en' => '5-Class Pack',
                'summary_en' => 'Five classes in two months. Just right for a new routine.',
                'summary' => 'Lima kelas dalam dua bulan. Pas untuk mulai rutin.',
                'class_count' => 5,
                'price' => 800_000,
                'validity_days' => 60,
            ],
            [
                'name' => 'Paket 10 Kelas',
                'slug' => 'paket-10',
                'name_en' => '10-Class Pack',
                'summary_en' => 'Ten classes in three months. Best price per class.',
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
