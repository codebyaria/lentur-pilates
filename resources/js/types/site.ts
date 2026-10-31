export type Level = 'baru_kenal' | 'mulai_akrab' | 'sudah_lengket';

export type CardColor = 'lime' | 'pink' | 'blue';

export type ScheduleDay = {
    key: string;
    label: string;
    shortLabel: string;
    date: string;
    isToday: boolean;
};

export type ScheduleSession = {
    id: number;
    day: string;
    time: string;
    endTime: string;
    className: string;
    classSlug: string;
    kind: 'mat' | 'reformer';
    kindLabel: string;
    level: Level;
    levelLabel: string;
    color: CardColor;
    isIntro: boolean;
    instructor: string;
    capacity: number;
    spotsLeft: number;
    isFull: boolean;
    bookingUrl: string;
    alternative: {
        id: number;
        day: string;
        dayLabel: string;
        time: string;
        className: string;
        spotsLeft: number;
        bookingUrl: string;
    } | null;
};

export type SiteClassType = {
    slug: string;
    name: string;
    summary: string;
    kind: 'mat' | 'reformer';
    kindLabel: string;
    level: Level;
    levelLabel: string;
    durationMinutes: number;
    capacity: number;
    isIntro: boolean;
    color: CardColor;
};

export type SitePackage = {
    slug: string;
    name: string;
    summary: string;
    classCount: number;
    price: number;
    priceLabel: string;
    perClassLabel: string;
    validityDays: number;
    isIntro: boolean;
    isShareable: boolean;
    bookingUrl: string;
};

export type SiteInstructor = {
    slug: string;
    name: string;
    tagline: string;
    bio: string;
    specialties: string[];
    figure: number;
};

export type SiteUrls = {
    home: string;
    booking: string;
    alternateHome: string;
};

export type LandingProps = {
    days: ScheduleDay[];
    schedule: ScheduleSession[];
    levels: { value: Level; label: string }[];
    classTypes: SiteClassType[];
    packages: SitePackage[];
    cancellationHours: number;
    instructors: SiteInstructor[];
    studio: { name: string; address: string };
    urls: SiteUrls;
};

export type BookingFlowProps = {
    days: ScheduleDay[];
    schedule: ScheduleSession[];
    levels: { value: Level; label: string }[];
    packages: SitePackage[];
    existingPackageChoice: string;
    cancellationHours: number;
    initial: { sessionId: number | null; package: string | null };
    urls: { home: string; submit: string; alternate: string };
};

export type BookingForm = {
    class_session_id: number | null;
    package: string;
    nickname: string;
    whatsapp: string;
    is_new_to_pilates: boolean | null;
    injury_notes: string;
};

export type ConfirmedBooking = {
    code: string;
    nickname: string;
    className: string;
    dayLabel: string;
    dateLabel: string;
    time: string;
    endTime: string;
    instructor: string;
    room: string;
    isFirstClass: boolean;
    packageName: string | null;
    classesRemaining: number | null;
    cancelDeadline: string;
};
