<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\ClassLevel;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Member fiktif, paket yang mereka beli (pembayaran disimulasikan),
 * dan booking untuk jadwal dari ScheduleSeeder.
 */
class MemberSeeder extends Seeder
{
    /**
     * Nama panggilan, level kenyamanan, dan apakah baru pertama kali.
     * Urutan menentukan level: lihat levelFor().
     *
     * @var list<string>
     */
    private const NICKNAMES = [
        'Ayu', 'Dimas', 'Rara', 'Tasya', 'Bayu', 'Wulan', 'Gilang', 'Intan',
        'Fajar', 'Nana', 'Putri', 'Raka', 'Sinta', 'Tio', 'Vina', 'Yuni',
        'Kevin', 'Mei', 'Laras', 'Andre', 'Citra', 'Dewi', 'Eko', 'Fitri',
        'Hana', 'Ikhsan', 'Joko', 'Kirana', 'Adit', 'Bella', 'Cahya', 'Dina',
        'Elsa', 'Farhan', 'Gita', 'Hendra', 'Indah', 'Jihan', 'Kiki', 'Lukas',
        'Maya', 'Nadine', 'Opal', 'Pras', 'Qori', 'Reza', 'Sasha', 'Teguh',
        'Lina', 'Mira', 'Nisa', 'Oki', 'Rina', 'Sari', 'Tari', 'Umar',
    ];

    /** Delapan nama terakhir: baru pertama kali pilates. */
    private const NEWCOMERS = 8;

    /**
     * @var array<string, string>
     */
    private const INJURY_NOTES = [
        'Dimas' => 'Lutut kiri agak rewel kalau ditekuk dalam.',
        'Intan' => 'Pernah cedera punggung bawah, sudah pulih tapi masih hati-hati.',
        'Raka' => 'Bahu kanan kaku, belum bisa angkat tangan penuh.',
        'Laras' => 'Pergelangan tangan sensitif, lebih nyaman bertumpu pakai kepalan.',
        'Mira' => 'Baru pulih dari keseleo pergelangan kaki.',
        'Tari' => 'Skoliosis ringan.',
    ];

    /**
     * [yang mengajak, yang diajak]: memakai paket Gerak Berdua.
     *
     * @var list<array{string, string}>
     */
    private const FRIEND_PAIRS = [
        ['Putri', 'Oki'],
        ['Citra', 'Sari'],
    ];

    private const MAX_CLASSES_PER_WEEK = 3;

    private Randomizer $random;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->random = new Randomizer(new Mt19937(2026));

        $members = $this->createMembers();
        $memberships = $this->createMemberships($members);
        $this->createBookings($members, $memberships);
    }

    private function levelFor(int $index): ClassLevel
    {
        return match (true) {
            $index < 12 => ClassLevel::SudahLengket,
            $index < 32 => ClassLevel::MulaiAkrab,
            default => ClassLevel::BaruKenal,
        };
    }

    private function isNewcomer(int $index): bool
    {
        return $index >= count(self::NICKNAMES) - self::NEWCOMERS;
    }

    /**
     * @return Collection<int, array{member: Member, level: ClassLevel, newcomer: bool}>
     */
    private function createMembers(): Collection
    {
        $members = collect(self::NICKNAMES)->map(function (string $nickname, int $index) {
            $newcomer = $this->isNewcomer($index);
            $joinedAt = $newcomer
                ? now()->subDays($this->random->getInt(1, 6))
                : now()->subDays($this->random->getInt(20, 200));

            $member = Member::query()->create([
                'nickname' => $nickname,
                'whatsapp' => sprintf('0812-0000-%04d', $index + 1),
                'email' => $this->random->getInt(0, 2) === 0 ? Str::lower($nickname).'@contoh.test' : null,
                'is_new_to_pilates' => $newcomer,
                'injury_notes' => self::INJURY_NOTES[$nickname] ?? null,
            ]);
            $member->forceFill(['created_at' => $joinedAt, 'updated_at' => $joinedAt])->save();

            return ['member' => $member, 'level' => $this->levelFor($index), 'newcomer' => $newcomer];
        })->keyBy(fn (array $entry) => $entry['member']->nickname);

        foreach (self::FRIEND_PAIRS as [$inviter, $friend]) {
            $members[$friend]['member']->update(['referred_by_id' => $members[$inviter]['member']->id]);
        }

        return $members->values();
    }

    /**
     * Satu paket aktif per member. Kuota "Gerak Berdua" dibagi: tiap orang mendapat satu kelas.
     *
     * @param  Collection<int, array{member: Member, level: ClassLevel, newcomer: bool}>  $members
     * @return array<int, Membership> keyed by member id
     */
    private function createMemberships(Collection $members): array
    {
        $packages = Package::query()->get()->keyBy('slug');
        $inPairs = collect(self::FRIEND_PAIRS)->flatten()->all();
        $memberships = [];

        foreach ($members as $entry) {
            $member = $entry['member'];

            $package = match (true) {
                in_array($member->nickname, $inPairs, true) => $packages['gerak-berdua'],
                $entry['newcomer'] => $packages['kelas-pertama'],
                $entry['level'] === ClassLevel::BaruKenal => $packages[$this->random->getInt(0, 3) ? 'paket-5' : 'sekali-datang'],
                default => $packages[$this->random->getInt(0, 2) ? 'paket-10' : 'paket-5'],
            };

            $purchasedAt = $entry['newcomer']
                ? $member->created_at ?? now()
                : now()->subDays($this->random->getInt(3, 30));
            $classesTotal = $package->is_shareable ? 1 : $package->class_count;
            // Member lama sudah memakai sebagian kuotanya sebelum minggu ini.
            $usedBefore = $classesTotal >= 5 ? $this->random->getInt(0, intdiv($classesTotal, 4)) : 0;

            $memberships[$member->id] = Membership::query()->create([
                'member_id' => $member->id,
                'package_id' => $package->id,
                'classes_total' => $classesTotal,
                'classes_remaining' => $classesTotal - $usedBefore,
                'price_paid' => $package->is_shareable ? intdiv($package->price, 2) : $package->price,
                'payment_status' => PaymentStatus::Paid,
                'payment_reference' => 'SIM-'.Str::upper(Str::random(10)),
                'purchased_at' => $purchasedAt,
                'expires_at' => CarbonImmutable::parse($purchasedAt)->addDays($package->validity_days),
            ]);
        }

        return $memberships;
    }

    /**
     * @param  Collection<int, array{member: Member, level: ClassLevel, newcomer: bool}>  $members
     * @param  array<int, Membership>  $memberships
     */
    private function createBookings(Collection $members, array $memberships): void
    {
        $levelRank = fn (ClassLevel $level) => array_search($level, ClassLevel::cases(), true);
        /** @var array<int, list<string>> $bookedDays */
        $bookedDays = [];

        /** @var array<int, array<string, int>> $perWeek */
        $perWeek = [];

        // Jadwal "penuh" diisi lebih dulu, sisanya diacak supaya kuota member tersebar rata.
        $sessions = collect($this->random->shuffleArray(
            ClassSession::query()->with('classType')->get()->all(),
        ))->sortBy(fn (ClassSession $session) => ScheduleSeeder::isFullSlot($session->starts_at) ? 0 : 1);

        foreach ($sessions as $session) {
            $capacity = $session->effectiveCapacity();
            $target = ScheduleSeeder::isFullSlot($session->starts_at)
                ? $capacity
                : (int) round($capacity * $this->random->getFloat(0.35, 0.8));
            $day = $session->starts_at->toDateString();
            $week = $session->starts_at->format('o-W');
            $isPast = $session->starts_at->isPast();

            $candidates = $members
                ->filter(fn (array $entry) => $levelRank($entry['level']) >= $levelRank($session->classType->level))
                ->filter(fn (array $entry) => ! $entry['newcomer'] || $session->classType->level === ClassLevel::BaruKenal);
            $candidates = collect($this->random->shuffleArray($candidates->all()))
                // Pendatang baru diarahkan ke kelas pertama.
                ->sortBy(fn (array $entry) => $session->classType->is_intro && $entry['newcomer'] ? 0 : 1);

            $booked = 0;

            foreach ($candidates as $entry) {
                if ($booked >= $target) {
                    break;
                }

                $member = $entry['member'];
                $membership = $memberships[$member->id];

                if (
                    $membership->classes_remaining < 1
                    || in_array($day, $bookedDays[$member->id] ?? [], true)
                    || ($perWeek[$member->id][$week] ?? 0) >= self::MAX_CLASSES_PER_WEEK
                ) {
                    continue;
                }

                Booking::query()->create([
                    'class_session_id' => $session->id,
                    'member_id' => $member->id,
                    'membership_id' => $membership->id,
                    'status' => match (true) {
                        ! $isPast => BookingStatus::Confirmed,
                        $this->random->getInt(1, 10) === 1 => BookingStatus::NoShow,
                        default => BookingStatus::Attended,
                    },
                ]);

                $membership->classes_remaining--;
                $bookedDays[$member->id][] = $day;
                $perWeek[$member->id][$week] = ($perWeek[$member->id][$week] ?? 0) + 1;
                $booked++;
            }

            // Sesekali ada yang batal tepat waktu: tempatnya kembali, kuotanya juga.
            if (! $isPast && $booked < $capacity && $this->random->getInt(1, 4) === 1) {
                $canceller = $candidates->first(
                    fn (array $entry) => ! in_array($day, $bookedDays[$entry['member']->id] ?? [], true),
                );

                if ($canceller !== null) {
                    Booking::query()->create([
                        'class_session_id' => $session->id,
                        'member_id' => $canceller['member']->id,
                        'membership_id' => $memberships[$canceller['member']->id]->id,
                        'status' => BookingStatus::Cancelled,
                        'cancelled_at' => $session->starts_at->copy()->subDays(1)->min(now()),
                    ]);
                }
            }
        }

        foreach ($memberships as $membership) {
            $membership->save();
        }

        $this->markFirstClasses($members);
    }

    /**
     * Booking aktif paling awal milik pendatang baru = kelas pertama mereka.
     *
     * @param  Collection<int, array{member: Member, level: ClassLevel, newcomer: bool}>  $members
     */
    private function markFirstClasses(Collection $members): void
    {
        foreach ($members->where('newcomer', true) as $entry) {
            $first = $entry['member']->bookings()
                ->holdingSpot()
                ->join('class_sessions', 'class_sessions.id', '=', 'bookings.class_session_id')
                ->orderBy('class_sessions.starts_at')
                ->select('bookings.*')
                ->first();

            $first?->update(['is_first_class' => true]);
        }
    }
}
