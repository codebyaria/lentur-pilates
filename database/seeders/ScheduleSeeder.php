<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Instructor;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;

/**
 * Jadwal mingguan, dibuat untuk minggu ini dan minggu depan
 * (relatif terhadap hari seeder dijalankan, jadi demo selalu terasa "sekarang").
 */
class ScheduleSeeder extends Seeder
{
    /**
     * [hari (1 = Senin), jam, jenis kelas, instruktur].
     *
     * @var list<array{int, string, string, string}>
     */
    private const WEEKLY = [
        [1, '07:00', 'mat-santai', 'sekar'],
        [1, '12:00', 'reformer-ringan', 'bima'],
        [1, '18:30', 'mat-mengalir', 'nadia'],
        [1, '19:45', 'reformer-main-main', 'bima'],
        [2, '07:00', 'reformer-ringan', 'yosef'],
        [2, '10:00', 'kenalan-dulu', 'sekar'],
        [2, '18:30', 'reformer-lengket', 'bima'],
        [2, '19:45', 'mat-santai', 'nadia'],
        [3, '07:00', 'mat-mengalir', 'nadia'],
        [3, '12:00', 'reformer-main-main', 'yosef'],
        [3, '18:30', 'kenalan-dulu', 'sekar'],
        [3, '19:45', 'reformer-ringan', 'bima'],
        [4, '07:00', 'mat-santai', 'sekar'],
        [4, '18:30', 'reformer-main-main', 'bima'],
        [4, '19:45', 'reformer-main-main', 'yosef'],
        [5, '07:00', 'reformer-lengket', 'yosef'],
        [5, '12:00', 'mat-santai', 'nadia'],
        [5, '18:30', 'mat-mengalir', 'sekar'],
        [6, '08:00', 'kenalan-dulu', 'sekar'],
        [6, '09:30', 'reformer-ringan', 'bima'],
        [6, '11:00', 'mat-santai', 'nadia'],
        [7, '09:00', 'mat-mengalir', 'nadia'],
        [7, '10:30', 'reformer-main-main', 'yosef'],
    ];

    /**
     * Jadwal yang sengaja dibuat penuh, untuk memperlihatkan
     * "Kelas ini sudah ramai. Jam berikutnya masih lega."
     *
     * @var list<array{int, string}>
     */
    public const FULL_SLOTS = [
        [4, '18:30'],
        [6, '09:30'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classTypes = ClassType::query()->pluck('id', 'slug');
        $instructors = Instructor::query()->pluck('id', 'slug');
        $firstMonday = now()->startOfWeek(CarbonInterface::MONDAY);

        foreach ([0, 1] as $weekOffset) {
            $monday = $firstMonday->copy()->addWeeks($weekOffset);

            foreach (self::WEEKLY as [$day, $time, $classType, $instructor]) {
                [$hour, $minute] = array_map('intval', explode(':', $time));
                $startsAt = $monday->copy()->addDays($day - 1)->setTime($hour, $minute);
                $duration = ClassType::query()->whereKey($classTypes[$classType])->value('duration_minutes');

                ClassSession::query()->create([
                    'class_type_id' => $classTypes[$classType],
                    'instructor_id' => $instructors[$instructor],
                    'starts_at' => $startsAt,
                    'ends_at' => $startsAt->copy()->addMinutes((int) $duration),
                    'room' => str_contains($classType, 'reformer') ? 'Ruang Reformer' : 'Studio Utama',
                ]);
            }
        }
    }

    public static function isFullSlot(CarbonInterface $startsAt): bool
    {
        return in_array([$startsAt->dayOfWeekIso, $startsAt->format('H:i')], self::FULL_SLOTS, true);
    }
}
