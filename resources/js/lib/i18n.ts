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

    'booking.metaTitle': 'Pesan kelas',
    'booking.steps.schedule': 'Jadwal',
    'booking.steps.package': 'Kelas & paket',
    'booking.steps.hello': 'Kenalan',
    'booking.steps.done': 'Konfirmasi',
    'booking.stepOf': 'Langkah {current} dari {total}',
    'booking.scheduleTitle': 'Mau gerak kapan?',
    'booking.scheduleLead':
        'Kelas penuh tetap kami tampilkan, lengkap dengan jam lain yang masih lega.',
    'booking.choose': 'Pilih',
    'booking.selectedClass': 'Terpilih ✓',
    'booking.yourClass': 'Kelas pilihanmu',
    'booking.change': 'Ganti',
    'booking.next': 'Lanjut',
    'booking.back': 'Kembali',
    'booking.packageTitle': 'Pakai paket apa?',
    'booking.packageLead':
        'Belum yakin? Mulai dari Kelas Pertama. Satu kelas, tanpa beban.',
    'booking.introNote': 'Khusus kunjungan pertama, sekali per orang.',
    'booking.existingTitle': 'Saya sudah punya paket aktif',
    'booking.existingBody':
        'Kami cek dari nomor WhatsApp-mu di langkah berikutnya. Satu kelas dipotong dari sisa paketmu.',
    'booking.helloTitle': 'Kenalan dulu, yuk.',
    'booking.helloLead':
        'Cukup yang perlu saja. Kami tidak akan mengirim promo bertubi-tubi.',
    'booking.nickname': 'Nama panggilan',
    'booking.nicknamePlaceholder': 'Misal: Rara',
    'booking.whatsapp': 'Nomor WhatsApp',
    'booking.whatsappHelp': 'Untuk mengabari kalau jadwal berubah. Itu saja.',
    'booking.firstTime': 'Pertama kali pilates?',
    'booking.firstTimeYes': 'Iya, pertama kali',
    'booking.firstTimeNo': 'Sudah pernah',
    'booking.injury': 'Ada cedera atau kondisi yang perlu kami tahu?',
    'booking.optional': 'opsional',
    'booking.injuryHelp':
        'Hanya dibaca instruktur. Misal: lutut kiri agak rewel kalau ditekuk.',
    'booking.summaryTitle': 'Ringkasan',
    'booking.payNow': 'Dibayar sekarang',
    'booking.payExisting': 'Dipotong 1 kelas dari paket aktif',
    'booking.simulated':
        'Demo: pembayaran disimulasikan. Tidak ada uang yang berpindah.',
    'booking.submit': 'Pesan sekarang',
    'booking.submitting': 'Sebentar…',
    'booking.cancelRule': 'Batal gratis sampai {hours} jam sebelum kelas.',
    'booking.pickClassFirst': 'Pilih jadwal dulu, ya.',
    'booking.pickPackageFirst': 'Pilih paket dulu, ya.',

    'confirmed.metaTitle': 'Sampai ketemu!',
    'confirmed.title': 'Sampai ketemu {day}!',
    'confirmed.subtitle': 'Bawa air minum, tinggalkan rasa sungkan.',
    'confirmed.hi': 'Sudah tercatat, {name}.',
    'confirmed.firstClass':
        'Ini kelas pertamamu di Lentur. Datang 10 menit lebih awal, kami ajak keliling dulu.',
    'confirmed.code': 'Kode booking',
    'confirmed.package': 'Paket',
    'confirmed.remaining': 'sisa {count} kelas',
    'confirmed.cancelBy': 'Batal gratis sampai {deadline}.',
    'confirmed.calendarTitle': 'Simpan ke kalender',
    'confirmed.calendarIcs': 'Apple / Outlook (.ics)',
    'confirmed.calendarGoogle': 'Google Calendar',
    'confirmed.friendTitle': 'Ajak teman, gerak bareng',
    'confirmed.friendBody':
        'Kirim tautan kelas ini ke teman. Selama masih ada tempat, kalian bisa datang bareng.',
    'confirmed.copyLink': 'Salin tautan kelas',
    'confirmed.copied': 'Tautan tersalin. Kirim ke temanmu!',
    'confirmed.home': 'Kembali ke beranda',
    'confirmed.bookAnother': 'Pesan kelas lain',
    'confirmed.cancelLink': 'Berhalangan? Batalkan booking',

    'cancel.metaTitle': 'Batalkan booking',
    'cancel.title': 'Berhalangan, {name}?',
    'cancel.lead':
        'Tidak apa-apa. Kabari lewat sini supaya tempatnya bisa dipakai teman lain.',
    'cancel.free':
        'Masih sebelum batas batal gratis ({deadline}). Kelasnya kembali ke paketmu.',
    'cancel.late':
        'Sudah lewat batas batal gratis ({deadline}). Kelasnya tetap terhitung, tapi tempatnya kami buka untuk orang lain. Terima kasih sudah mengabari.',
    'cancel.confirm': 'Ya, batalkan',
    'cancel.keep': 'Tidak jadi, tetap datang',
    'cancel.cancelling': 'Sebentar…',
    'cancel.doneTitle': 'Booking dibatalkan.',
    'cancel.doneRefunded':
        'Kelasnya sudah kembali ke paketmu. Sampai ketemu lain waktu!',
    'cancel.doneCounted':
        'Kelasnya tetap terhitung, tapi tempatmu sudah dibuka untuk orang lain. Sampai ketemu lain waktu!',
    'cancel.alreadyCancelled': 'Booking ini sudah dibatalkan sebelumnya.',
    'cancel.notCancellable':
        'Kelas ini sudah mulai atau sudah lewat, jadi tidak bisa dibatalkan lagi.',
    'cancel.remaining': 'Sisa paket {name}: {count} kelas.',
    'cancel.bookAnother': 'Pilih jadwal lain',
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

    'booking.metaTitle': 'Book a class',
    'booking.steps.schedule': 'Schedule',
    'booking.steps.package': 'Class & pack',
    'booking.steps.hello': 'Say hello',
    'booking.steps.done': 'Confirmation',
    'booking.stepOf': 'Step {current} of {total}',
    'booking.scheduleTitle': 'When do you want to move?',
    'booking.scheduleLead':
        'Full classes still show up, together with the next slot that has room.',
    'booking.choose': 'Choose',
    'booking.selectedClass': 'Chosen ✓',
    'booking.yourClass': 'Your class',
    'booking.change': 'Change',
    'booking.next': 'Next',
    'booking.back': 'Back',
    'booking.packageTitle': 'Which pack?',
    'booking.packageLead':
        'Not sure yet? Start with the First Class. One class, no pressure.',
    'booking.introNote': 'First visit only, once per person.',
    'booking.existingTitle': 'I already have an active pack',
    'booking.existingBody':
        'We check it with your WhatsApp number in the next step. One class comes off your pack.',
    'booking.helloTitle': 'Let’s say hello.',
    'booking.helloLead':
        'Only what we need. We will not flood you with promos.',
    'booking.nickname': 'Nickname',
    'booking.nicknamePlaceholder': 'e.g. Rara',
    'booking.whatsapp': 'WhatsApp number',
    'booking.whatsappHelp':
        'To let you know if the schedule changes. That is all.',
    'booking.firstTime': 'First time doing pilates?',
    'booking.firstTimeYes': 'Yes, first time',
    'booking.firstTimeNo': 'I have tried it',
    'booking.injury': 'Any injury or condition we should know about?',
    'booking.optional': 'optional',
    'booking.injuryHelp':
        'Only your instructor reads this. E.g. my left knee complains when bent.',
    'booking.summaryTitle': 'Summary',
    'booking.payNow': 'Paid now',
    'booking.payExisting': '1 class off your active pack',
    'booking.simulated': 'Demo: payment is simulated. No money changes hands.',
    'booking.submit': 'Book now',
    'booking.submitting': 'One moment…',
    'booking.cancelRule': 'Cancel free up to {hours} hours before class.',
    'booking.pickClassFirst': 'Pick a time first.',
    'booking.pickPackageFirst': 'Pick a pack first.',

    'confirmed.metaTitle': 'See you soon!',
    'confirmed.title': 'See you {day}!',
    'confirmed.subtitle': 'Bring water, leave the awkwardness at home.',
    'confirmed.hi': 'You are booked, {name}.',
    'confirmed.firstClass':
        'This is your first class at Lentur. Come 10 minutes early and we will show you around.',
    'confirmed.code': 'Booking code',
    'confirmed.package': 'Pack',
    'confirmed.remaining': '{count} classes left',
    'confirmed.cancelBy': 'Cancel free until {deadline}.',
    'confirmed.calendarTitle': 'Save to calendar',
    'confirmed.calendarIcs': 'Apple / Outlook (.ics)',
    'confirmed.calendarGoogle': 'Google Calendar',
    'confirmed.friendTitle': 'Bring a friend, move together',
    'confirmed.friendBody':
        'Send this class to a friend. As long as there is room, you can come together.',
    'confirmed.copyLink': 'Copy class link',
    'confirmed.copied': 'Link copied. Send it to your friend!',
    'confirmed.home': 'Back to home',
    'confirmed.bookAnother': 'Book another class',
    'confirmed.cancelLink': 'Can’t make it? Cancel this booking',

    'cancel.metaTitle': 'Cancel booking',
    'cancel.title': 'Can’t make it, {name}?',
    'cancel.lead':
        'No worries. Let us know here so a friend can take the spot.',
    'cancel.free':
        'You are still before the free cancellation deadline ({deadline}). The class goes back to your pack.',
    'cancel.late':
        'The free cancellation deadline has passed ({deadline}). The class still counts, but we will open your spot for someone else. Thanks for letting us know.',
    'cancel.confirm': 'Yes, cancel',
    'cancel.keep': 'Never mind, I’m coming',
    'cancel.cancelling': 'One moment…',
    'cancel.doneTitle': 'Booking cancelled.',
    'cancel.doneRefunded':
        'The class is back in your pack. See you another time!',
    'cancel.doneCounted':
        'The class still counts, but your spot is open for someone else now. See you another time!',
    'cancel.alreadyCancelled': 'This booking was already cancelled.',
    'cancel.notCancellable':
        'This class has started or already passed, so it can no longer be cancelled.',
    'cancel.remaining': '{name} left: {count} classes.',
    'cancel.bookAnother': 'Pick another time',
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
