import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { BookingHeader } from '@/components/booking/booking-header';
import { BodyFigures } from '@/components/lentur/body-figures';
import { ElasticWord } from '@/components/lentur/elastic-word';
import { pillClassName } from '@/components/landing/ui';
import { useTranslate } from '@/lib/i18n';
import type { ConfirmedBooking } from '@/types/site';

/** Langkah 4: "Sampai ketemu Kamis! Bawa air minum, tinggalkan rasa sungkan." */
export default function BookingConfirmed({
    booking,
    studio,
    urls,
}: {
    booking: ConfirmedBooking;
    studio: { name: string; address: string };
    urls: {
        home: string;
        calendar: string;
        googleCalendar: string;
        shareClass: string;
        bookAnother: string;
    };
}) {
    const t = useTranslate();
    const [copied, setCopied] = useState(false);
    const [before, after] = t('confirmed.title', { day: '{day}' }).split(
        '{day}',
    );

    const copyLink = async () => {
        try {
            await navigator.clipboard.writeText(urls.shareClass);
            setCopied(true);
            window.setTimeout(() => setCopied(false), 3000);
        } catch {
            window.prompt(t('confirmed.copyLink'), urls.shareClass);
        }
    };

    return (
        <>
            <Head title={t('confirmed.metaTitle')} />
            <div className="min-h-screen bg-lentur-cream font-sans text-lentur-ink antialiased">
                <BookingHeader homeUrl={urls.home} />

                <main className="mx-auto max-w-4xl px-4 pt-10 pb-20 sm:px-8">
                    <p className="inline-flex rounded-full bg-lentur-lime px-3 py-1 text-sm font-semibold">
                        {t('booking.steps.done')} ✓
                    </p>

                    {/* Hari kelasnya bisa ditarik, sama seperti "lentur" di beranda. */}
                    <h1 className="mt-5 font-display text-[clamp(2.5rem,8vw,5rem)] leading-[1.02]">
                        {before}
                        <ElasticWord
                            word={booking.dayLabel}
                            className="text-lentur-blue"
                        />
                        {after}
                    </h1>
                    <p className="mt-3 font-display text-2xl sm:text-3xl">
                        {t('confirmed.subtitle')}
                    </p>
                    <p className="mt-4 text-lg text-lentur-ink/75">
                        {t('confirmed.hi', { name: booking.nickname })}
                    </p>

                    <BodyFigures label="" className="mx-auto mt-6 max-w-2xl" />

                    <section className="mt-6 grid gap-5 md:grid-cols-[1.4fr_1fr]">
                        <div className="rounded-blob-lg bg-white p-7 ring-1 ring-lentur-ink/5">
                            <h2 className="text-2xl font-bold">
                                {booking.className}
                            </h2>
                            <p className="mt-2 text-lg">
                                <span className="capitalize">
                                    {booking.dateLabel}
                                </span>{' '}
                                · {booking.time}–{booking.endTime}
                            </p>
                            <p className="text-lentur-ink/75">
                                {t('schedule.with', {
                                    name: booking.instructor,
                                })}{' '}
                                · {booking.room}
                            </p>
                            <p className="mt-1 text-lentur-ink/75">
                                {studio.name}, {studio.address}
                            </p>

                            <dl className="mt-6 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <dt className="text-sm font-semibold text-lentur-ink/60">
                                        {t('confirmed.code')}
                                    </dt>
                                    <dd className="mt-1 font-mono text-xl font-bold tracking-wider">
                                        {booking.code}
                                    </dd>
                                </div>
                                {booking.packageName && (
                                    <div>
                                        <dt className="text-sm font-semibold text-lentur-ink/60">
                                            {t('confirmed.package')}
                                        </dt>
                                        <dd className="mt-1 font-semibold">
                                            {booking.packageName}
                                            {booking.classesRemaining !==
                                                null && (
                                                <span className="block font-normal text-lentur-ink/70">
                                                    {t('confirmed.remaining', {
                                                        count: booking.classesRemaining,
                                                    })}
                                                </span>
                                            )}
                                        </dd>
                                    </div>
                                )}
                            </dl>

                            <p className="mt-6 rounded-blob-sm bg-lentur-pink/40 p-4 text-sm font-semibold">
                                {t('confirmed.cancelBy', {
                                    deadline: booking.cancelDeadline,
                                })}
                            </p>
                        </div>

                        <div className="grid gap-5">
                            {booking.isFirstClass && (
                                <p className="rounded-blob-lg bg-lentur-lime p-6 text-lg font-semibold">
                                    {t('confirmed.firstClass')}
                                </p>
                            )}
                            <div className="rounded-blob-lg bg-white p-6 ring-1 ring-lentur-ink/5">
                                <h2 className="font-display text-2xl">
                                    {t('confirmed.calendarTitle')}
                                </h2>
                                <div className="mt-4 grid gap-2">
                                    <a
                                        href={urls.calendar}
                                        className={pillClassName(
                                            'primary',
                                            'w-full',
                                        )}
                                    >
                                        {t('confirmed.calendarIcs')}
                                    </a>
                                    <a
                                        href={urls.googleCalendar}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className={pillClassName(
                                            'outline',
                                            'w-full',
                                        )}
                                    >
                                        {t('confirmed.calendarGoogle')}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Putih hanya di atas biru. */}
                    <section className="mt-5 rounded-blob-lg bg-lentur-blue p-7 text-white sm:p-10">
                        <h2 className="font-display text-3xl sm:text-4xl">
                            {t('confirmed.friendTitle')}
                        </h2>
                        <p className="mt-3 max-w-xl text-lg text-white/90">
                            {t('confirmed.friendBody')}
                        </p>
                        <div className="mt-6 flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                onClick={copyLink}
                                className={pillClassName('lime')}
                            >
                                {t('confirmed.copyLink')}
                            </button>
                            <span
                                role="status"
                                className="text-sm text-white/90"
                            >
                                {copied ? t('confirmed.copied') : ''}
                            </span>
                        </div>
                    </section>

                    <div className="mt-10 flex flex-wrap gap-3">
                        <a
                            href={urls.home}
                            className={pillClassName('outline')}
                        >
                            {t('confirmed.home')}
                        </a>
                        <a
                            href={urls.bookAnother}
                            className={pillClassName('outline')}
                        >
                            {t('confirmed.bookAnother')}
                        </a>
                    </div>
                </main>
            </div>
        </>
    );
}
