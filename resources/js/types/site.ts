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
