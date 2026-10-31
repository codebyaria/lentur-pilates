import { useTranslate } from '@/lib/i18n';
import { cn } from '@/lib/utils';
import type { Level, ScheduleDay, ScheduleSession } from '@/types/site';
import { SectionHeading, pillClassName } from './ui';

const STRIPE: Record<ScheduleSession['color'], string> = {
    lime: 'bg-lentur-lime',
    pink: 'bg-lentur-pink',
    blue: 'bg-lentur-blue',
};

function chipClassName(active: boolean) {
    return cn(
        'inline-flex min-h-11 shrink-0 items-center gap-2 rounded-full px-4 text-sm font-semibold transition-elastic focus-visible:ring-4 focus-visible:ring-lentur-blue/40 focus-visible:outline-none',
        active
            ? 'bg-lentur-blue text-white'
            : 'bg-white text-lentur-ink ring-1 ring-lentur-ink/10 hover:ring-lentur-ink/30',
    );
}

export function Schedule({
    days,
    sessions,
    levels,
    day,
    level,
    onDayChange,
    onLevelChange,
}: {
    days: ScheduleDay[];
    sessions: ScheduleSession[];
    levels: { value: Level; label: string }[];
    day: string;
    level: Level | null;
    onDayChange: (day: string) => void;
    onLevelChange: (level: Level | null) => void;
}) {
    const t = useTranslate();

    // Kelas penuh tetap tampil (dengan saran jam lain), hanya disaring per hari & level.
    const visible = sessions.filter(
        (session) =>
            session.day === day && (level === null || session.level === level),
    );
    const levelLabel = levels.find((item) => item.value === level)?.label ?? '';

    return (
        <section
            id="jadwal"
            aria-labelledby="schedule-title"
            className="scroll-mt-20 bg-white/60 py-20"
        >
            <div className="mx-auto max-w-6xl px-4 sm:px-8">
                <SectionHeading
                    id="schedule-title"
                    eyebrow={t('schedule.eyebrow')}
                    title={t('schedule.title')}
                />

                <div
                    className="-mx-4 mt-8 overflow-x-auto px-4 pb-2"
                    role="tablist"
                    aria-label={t('nav.schedule')}
                >
                    <div className="flex gap-2">
                        {days.map((item) => {
                            const count = sessions.filter(
                                (session) =>
                                    session.day === item.key &&
                                    (level === null || session.level === level),
                            ).length;

                            return (
                                <button
                                    key={item.key}
                                    type="button"
                                    role="tab"
                                    aria-selected={item.key === day}
                                    onClick={() => onDayChange(item.key)}
                                    className={cn(
                                        chipClassName(item.key === day),
                                        'flex-col gap-0 py-2 leading-tight',
                                    )}
                                >
                                    <span>
                                        {item.isToday
                                            ? t('schedule.today')
                                            : item.shortLabel}
                                    </span>
                                    <span className="text-xs font-normal opacity-75">
                                        {item.date} · {count}
                                    </span>
                                </button>
                            );
                        })}
                    </div>
                </div>

                <div className="-mx-4 mt-3 flex gap-2 overflow-x-auto px-4 pb-2">
                    <button
                        type="button"
                        onClick={() => onLevelChange(null)}
                        className={chipClassName(level === null)}
                        aria-pressed={level === null}
                    >
                        {t('schedule.allLevels')}
                    </button>
                    {levels.map((item) => (
                        <button
                            key={item.value}
                            type="button"
                            onClick={() => onLevelChange(item.value)}
                            className={chipClassName(level === item.value)}
                            aria-pressed={level === item.value}
                        >
                            {item.label}
                        </button>
                    ))}
                </div>

                <ul className="mt-8 grid gap-4" role="tabpanel">
                    {visible.length === 0 && (
                        <li className="rounded-blob bg-lentur-cream p-8 text-center text-lg text-lentur-ink/70">
                            {level
                                ? t('schedule.empty', { level: levelLabel })
                                : t('schedule.emptyAll')}
                        </li>
                    )}
                    {visible.map((session) => (
                        <ScheduleCard key={session.id} session={session} />
                    ))}
                </ul>
            </div>
        </section>
    );
}

function ScheduleCard({ session }: { session: ScheduleSession }) {
    const t = useTranslate();

    return (
        <li
            className={cn(
                'relative grid gap-4 overflow-hidden rounded-blob bg-white p-5 pl-7 ring-1 ring-lentur-ink/5 transition-elastic sm:grid-cols-[6rem_1fr_auto] sm:items-center sm:p-6 sm:pl-8',
                !session.isFull &&
                    'hover:-translate-y-0.5 hover:shadow-[0_12px_30px_-18px_rgba(31,42,68,0.45)]',
            )}
        >
            <span
                aria-hidden="true"
                className={cn(
                    'absolute inset-y-0 left-0 w-2',
                    STRIPE[session.color],
                )}
            />

            <div className="flex items-baseline gap-2 sm:block">
                <p className="font-display text-3xl leading-none">
                    {session.time}
                </p>
                <p className="text-sm text-lentur-ink/60 sm:mt-1">
                    –{session.endTime}
                </p>
            </div>

            <div>
                <div className="flex flex-wrap items-center gap-2">
                    <h3 className="text-xl font-bold">{session.className}</h3>
                    {session.isIntro && (
                        <span className="rounded-full bg-lentur-lime px-2.5 py-0.5 text-xs font-semibold">
                            {t('schedule.firstClassBadge')}
                        </span>
                    )}
                </div>
                <p className="mt-1 text-lentur-ink/70">
                    {session.levelLabel} · {session.kindLabel} ·{' '}
                    {t('schedule.with', { name: session.instructor })}
                </p>
                {session.isFull && (
                    <div className="mt-3 rounded-blob-sm bg-lentur-pink/30 p-4">
                        <p className="font-semibold">
                            {t('schedule.fullMessage')}
                        </p>
                        {session.alternative ? (
                            <a
                                href={session.alternative.bookingUrl}
                                className="mt-2 inline-flex min-h-11 items-center gap-2 font-semibold text-lentur-blue underline decoration-2 underline-offset-4 hover:decoration-lentur-pink"
                            >
                                {t('schedule.alternative', {
                                    day: session.alternative.dayLabel,
                                    time: session.alternative.time,
                                    className: session.alternative.className,
                                    count: session.alternative.spotsLeft,
                                })}
                                <span aria-hidden="true">→</span>
                            </a>
                        ) : (
                            <p className="mt-1 text-sm">
                                {t('schedule.noAlternative')}
                            </p>
                        )}
                    </div>
                )}
            </div>

            <div className="flex items-center gap-3 sm:flex-col sm:items-end">
                {session.isFull ? (
                    <span className="rounded-full bg-lentur-pink px-4 py-2 text-sm font-semibold">
                        {t('schedule.full')}
                    </span>
                ) : (
                    <>
                        <span
                            className={cn(
                                'text-sm font-semibold',
                                session.spotsLeft === 1
                                    ? 'text-lentur-ink'
                                    : 'text-lentur-ink/60',
                            )}
                        >
                            {session.spotsLeft === 1
                                ? t('schedule.lastSpot')
                                : t('schedule.spotsLeft', {
                                      count: session.spotsLeft,
                                  })}
                        </span>
                        <a
                            href={session.bookingUrl}
                            className={pillClassName('primary', 'px-6 py-2.5')}
                            aria-label={`${t('schedule.book')}: ${session.className} ${session.time}`}
                        >
                            {t('schedule.book')}
                        </a>
                    </>
                )}
            </div>
        </li>
    );
}
