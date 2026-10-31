import { usePage } from '@inertiajs/react';

/**
 * Copy situs publik. Indonesia = sumber; Inggris harus punya kunci yang sama.
 * Nada: hangat, sedikit bercanda, kalimat pendek, banyak kata kerja.
 * Jangan pernah: foto sebelum–sesudah, "bakar kalori", "body goals", nada menuntut.
 */
const id = {
    'meta.title': 'Lentur Pilates · Gerak dulu. Sempurna belakangan.',
    'meta.description':
        'Studio pilates mat & reformer dengan kelas kecil di Jakarta Selatan. Untuk semua bentuk tubuh. Booking online, batal gratis sampai {hours} jam sebelum kelas.',

    'nav.schedule': 'Jadwal',
    'nav.pricing': 'Harga',
    'nav.instructors': 'Instruktur',
    'nav.book': 'Pesan kelas',
    'nav.switchLanguage': 'English',
    'nav.switchLanguageShort': 'EN',
    'nav.skip': 'Langsung ke jadwal',

    'hero.eyebrow': 'Studio pilates · mat & reformer · kelas kecil',
    'hero.pullHint': 'Tarik kata ini. Serius, coba saja.',
    'hero.tagline': 'Gerak dulu. Sempurna belakangan.',
    'hero.lead':
        'Pilates untuk semua bentuk tubuh. Maksimal sepuluh orang per kelas, instruktur yang hafal namamu, dan tidak ada yang menghitung.',
    'hero.ctaIntro': 'Coba kelas pertama · {price}',
    'hero.ctaSchedule': 'Lihat jadwal',
    'hero.figuresLabel':
        'Enam orang dengan bentuk tubuh berbeda, termasuk pengguna kursi roda, sedang bergerak bersama',

    'which.eyebrow': 'Kamu yang mana?',
    'which.title': 'Pilih level yang paling terasa seperti kamu.',
    'which.lead': 'Bukan ujian. Kamu bisa pindah kapan saja.',
    'which.baru_kenal.who': 'Belum pernah, atau sudah lama sekali.',
    'which.baru_kenal.promise':
        'Kita mulai dari napas. Semua gerakan dijelaskan, pelan-pelan.',
    'which.mulai_akrab.who': 'Sudah hafal dasarnya, mau sedikit tantangan.',
    'which.mulai_akrab.promise': 'Gerakan mulai bersambung. Masih banyak tawa.',
    'which.sudah_lengket.who': 'Reformer sudah seperti teman lama.',
    'which.sudah_lengket.promise':
        'Rangkaian panjang, kelas kecil, perhatian penuh.',
    'which.classes': 'Kelasnya',
    'which.cta': 'Lihat jadwal {level}',

    'schedule.eyebrow': 'Jadwal 7 hari ke depan',
    'schedule.title': 'Pilih jam yang pas. Sisanya kami siapkan.',
    'schedule.allLevels': 'Semua level',
    'schedule.today': 'Hari ini',
    'schedule.classesCount': '{count} kelas',
    'schedule.spotsLeft': 'sisa {count} tempat',
    'schedule.lastSpot': 'Tinggal 1 tempat',
    'schedule.full': 'Penuh',
    'schedule.fullMessage': 'Kelas ini sudah ramai. Jam berikutnya masih lega.',
    'schedule.alternative': '{day} {time} · {className} · sisa {count}',
    'schedule.noAlternative':
        'Jam lain minggu ini juga sudah ramai. Coba minggu depan, ya.',
    'schedule.book': 'Pesan',
    'schedule.empty': 'Belum ada kelas {level} di hari ini. Coba hari lain?',
    'schedule.emptyAll': 'Studio istirahat hari ini. Kamu juga boleh.',
    'schedule.firstClassBadge': 'Kelas pertama',
    'schedule.with': 'bersama {name}',

    'pricing.eyebrow': 'Harga jelas',
    'pricing.title': 'Tanpa biaya pendaftaran. Tanpa kontrak.',
    'pricing.startHere': 'Mulai di sini',
    'pricing.forTwo': 'Berdua',
    'pricing.classes': '{count} kelas',
    'pricing.class': '1 kelas',
    'pricing.perClass': '{price} per kelas',
    'pricing.validity': 'berlaku {days} hari',
    'pricing.choose': 'Pilih paket ini',
    'pricing.anyClass':
        'Semua paket bisa dipakai di kelas mat maupun reformer.',
    'pricing.rulesTitle': 'Aturan batal, tanpa huruf kecil',
    'pricing.rule1':
        'Batal sampai {hours} jam sebelum kelas: gratis, kelasnya kembali ke paket.',
    'pricing.rule2':
        'Kurang dari {hours} jam: kelas tetap terhitung. Tapi tetap kabari kami, ya.',
    'pricing.rule3': 'Studio yang membatalkan? Kelasmu kembali utuh, otomatis.',

    'notHere.eyebrow': 'Yang tidak akan kamu temukan di sini',
    'notHere.title': 'Beberapa hal sengaja kami tinggalkan di luar.',
    'notHere.photos.title': 'Foto "sebelum dan sesudah"',
    'notHere.photos.body':
        'Tidak ada tubuh yang dipajang sebagai contoh. Semua tubuh sudah cukup.',
    'notHere.counting.title': 'Hitung-hitungan kalori',
    'notHere.counting.body':
        'Kamu datang untuk bergerak dan bersenang-senang, bukan untuk menghitung.',
    'notHere.shouting.title': 'Teriakan "satu lagi!"',
    'notHere.shouting.body':
        'Instruktur kami mengajak, bukan memerintah. "Boleh pelan?" selalu boleh.',
    'notHere.mirrors.title': 'Cermin di setiap dinding',
    'notHere.mirrors.body': 'Fokus ke rasa di badan, bukan ke pantulan.',
    'notHere.contracts.title': 'Kontrak tahunan',
    'notHere.contracts.body': 'Beli paket kalau mau, berhenti kapan saja.',
    'notHere.fees.title': 'Biaya tersembunyi',
    'notHere.fees.body': 'Harga yang kamu lihat di atas itu sudah semuanya.',

    'instructors.eyebrow': 'Instruktur',
    'instructors.title': 'Teman yang mengajak jalan sore.',
    'instructors.lead':
        'Bukan pelatih yang berteriak. Mereka hafal namamu, dan cerita lututmu.',

    'friend.eyebrow': 'Ajak teman, gerak bareng',
    'friend.title': 'Lebih berani kalau berdua.',
    'friend.body':
        '{name}: {count} kelas untuk kamu dan satu teman, {price}. Datang bareng, pulang bareng, tertawa di tengahnya.',
    'friend.cta': 'Ajak teman',
    'friend.copy': 'Salin tautan untuk teman',
    'friend.copied': 'Tautan tersalin. Kirim ke temanmu!',

    'footer.visit': 'Mampir',
    'footer.hours': 'Senin–Minggu, 07.00–21.00',
    'footer.demo':
        'Demo konsep untuk portofolio Ari Zain (No. 02). Semua data, nama, dan harga fiktif.',
    'footer.language': 'Bahasa',

    'booking.title': 'Alur booking menyusul',
    'booking.body':
        'Halaman pesan kelas sedang kami siapkan. Sebentar lagi kamu bisa pilih jadwal, paket, dan kenalan di sini.',
    'booking.selected': 'Kelas yang kamu pilih',
    'booking.back': 'Kembali ke jadwal',
} as const;

type MessageKey = keyof typeof id;

const en: Record<MessageKey, string> = {
    'meta.title': 'Lentur Pilates · Move first. Perfect later.',
    'meta.description':
        'Small-group mat & reformer pilates in South Jakarta. For every body. Book online, cancel free up to {hours} hours before class.',

    'nav.schedule': 'Schedule',
    'nav.pricing': 'Pricing',
    'nav.instructors': 'Instructors',
    'nav.book': 'Book a class',
    'nav.switchLanguage': 'Bahasa Indonesia',
    'nav.switchLanguageShort': 'ID',
    'nav.skip': 'Skip to schedule',

    'hero.eyebrow': 'Pilates studio · mat & reformer · small classes',
    'hero.pullHint': 'Pull this word. Seriously, try it.',
    'hero.tagline': 'Move first. Perfect later.',
    'hero.lead':
        'Pilates for every body. Ten people per class at most, instructors who remember your name, and nobody counting.',
    'hero.ctaIntro': 'Try your first class · {price}',
    'hero.ctaSchedule': 'See the schedule',
    'hero.figuresLabel':
        'Six people with different bodies, including a wheelchair user, moving together',

    'which.eyebrow': 'Which one are you?',
    'which.title': 'Pick the level that feels most like you.',
    'which.lead': 'It is not a test. You can switch any time.',
    'which.baru_kenal.who': 'Never tried, or it has been a long time.',
    'which.baru_kenal.promise':
        'We start with breathing. Every move explained, slowly.',
    'which.mulai_akrab.who': 'You know the basics and want a little challenge.',
    'which.mulai_akrab.promise':
        'Moves start to flow together. Still plenty of laughs.',
    'which.sudah_lengket.who': 'The reformer feels like an old friend.',
    'which.sudah_lengket.promise':
        'Long sequences, small class, full attention.',
    'which.classes': 'Classes',
    'which.cta': 'See {level} classes',

    'schedule.eyebrow': 'Next 7 days',
    'schedule.title': 'Pick a time that works. We will handle the rest.',
    'schedule.allLevels': 'All levels',
    'schedule.today': 'Today',
    'schedule.classesCount': '{count} classes',
    'schedule.spotsLeft': '{count} spots left',
    'schedule.lastSpot': '1 spot left',
    'schedule.full': 'Full',
    'schedule.fullMessage': 'This class is busy. The next slot still has room.',
    'schedule.alternative': '{day} {time} · {className} · {count} left',
    'schedule.noAlternative':
        'Other slots this week are busy too. Try next week.',
    'schedule.book': 'Book',
    'schedule.empty': 'No {level} classes on this day yet. Try another day?',
    'schedule.emptyAll': 'The studio rests today. You can too.',
    'schedule.firstClassBadge': 'First class',
    'schedule.with': 'with {name}',

    'pricing.eyebrow': 'Clear pricing',
    'pricing.title': 'No sign-up fee. No contract.',
    'pricing.startHere': 'Start here',
    'pricing.forTwo': 'For two',
    'pricing.classes': '{count} classes',
    'pricing.class': '1 class',
    'pricing.perClass': '{price} per class',
    'pricing.validity': 'valid {days} days',
    'pricing.choose': 'Choose this pack',
    'pricing.anyClass': 'Every pack works for both mat and reformer classes.',
    'pricing.rulesTitle': 'Cancellation rules, no fine print',
    'pricing.rule1':
        'Cancel up to {hours} hours before class: free, the class goes back to your pack.',
    'pricing.rule2':
        'Less than {hours} hours: the class still counts. But do let us know.',
    'pricing.rule3':
        'The studio cancels? Your class comes back in full, automatically.',

    'notHere.eyebrow': 'What you will not find here',
    'notHere.title': 'A few things we left outside on purpose.',
    'notHere.photos.title': '"Before and after" photos',
    'notHere.photos.body':
        'No body is put on display as an example. Every body is already enough.',
    'notHere.counting.title': 'Calorie maths',
    'notHere.counting.body': 'You come to move and have fun, not to count.',
    'notHere.shouting.title': 'Shouts of "one more!"',
    'notHere.shouting.body':
        'Our instructors invite, they do not command. "Can we go slower?" is always fine.',
    'notHere.mirrors.title': 'Mirrors on every wall',
    'notHere.mirrors.body': 'Focus on how it feels, not on the reflection.',
    'notHere.contracts.title': 'Yearly contracts',
    'notHere.contracts.body': 'Buy a pack if you like, stop whenever you want.',
    'notHere.fees.title': 'Hidden fees',
    'notHere.fees.body': 'The prices above are the whole story.',

    'instructors.eyebrow': 'Instructors',
    'instructors.title': 'Friends who invite you for an evening walk.',
    'instructors.lead':
        'Not trainers who shout. They know your name, and your knee story.',

    'friend.eyebrow': 'Bring a friend, move together',
    'friend.title': 'Braver in pairs.',
    'friend.body':
        '{name}: {count} classes for you and a friend, {price}. Arrive together, leave together, laugh in between.',
    'friend.cta': 'Bring a friend',
    'friend.copy': 'Copy a link for your friend',
    'friend.copied': 'Link copied. Send it to your friend!',

    'footer.visit': 'Visit',
    'footer.hours': 'Monday–Sunday, 7am–9pm',
    'footer.demo':
        'Concept demo for Ari Zain’s portfolio (No. 02). All data, names, and prices are fictional.',
    'footer.language': 'Language',

    'booking.title': 'Booking is coming soon',
    'booking.body':
        'We are preparing the booking page. Soon you can pick a time, a pack, and say hello here.',
    'booking.selected': 'The class you picked',
    'booking.back': 'Back to the schedule',
};

const messages = { id, en } satisfies Record<
    Locale,
    Record<MessageKey, string>
>;

export type Locale = 'id' | 'en';

export type Translate = (
    key: MessageKey,
    replacements?: Record<string, string | number>,
) => string;

export function translate(
    locale: Locale,
    key: MessageKey,
    replacements: Record<string, string | number> = {},
): string {
    return Object.entries(replacements).reduce<string>(
        (text, [name, value]) => text.replaceAll(`{${name}}`, String(value)),
        messages[locale][key],
    );
}

export function useLocale(): Locale {
    const { locale } = usePage().props;

    return locale === 'en' ? 'en' : 'id';
}

export function useTranslate(): Translate {
    const locale = useLocale();

    return (key, replacements) => translate(locale, key, replacements);
}
