import { Head } from '@inertiajs/react';
import { BodyFigure } from '@/components/lentur/body-figures';
import { PillLink } from '@/components/landing/ui';
import { useTranslate } from '@/lib/i18n';
import type { ScheduleSession } from '@/types/site';

/** Sementara: diganti alur booking 4 langkah. */
export default function BookingComingSoon({
    session,
    sessionDate,
    urls,
}: {
    session: ScheduleSession | null;
    sessionDate: string | null;
    urls: { home: string };
}) {
    const t = useTranslate();

    return (
        <>
            <Head title={t('booking.title')} />
            <main className="flex min-h-screen items-center justify-center bg-lentur-cream px-4 py-16 font-sans text-lentur-ink">
                <div className="w-full max-w-xl text-center">
                    <BodyFigure index={2} className="mx-auto w-28" />
                    <h1 className="mt-6 font-display text-4xl sm:text-5xl">
                        {t('booking.title')}
                    </h1>
                    <p className="mt-4 text-lg text-lentur-ink/75">
                        {t('booking.body')}
                    </p>

                    {session && (
                        <div className="mt-8 rounded-blob bg-white p-6 text-left ring-1 ring-lentur-ink/5">
                            <p className="text-sm font-semibold text-lentur-ink/60">
                                {t('booking.selected')}
                            </p>
                            <p className="mt-1 text-xl font-bold">
                                {session.className}
                            </p>
                            <p className="text-lentur-ink/75">
                                <span className="capitalize">
                                    {sessionDate}
                                </span>{' '}
                                · {session.time}–{session.endTime} ·{' '}
                                {t('schedule.with', {
                                    name: session.instructor,
                                })}
                            </p>
                        </div>
                    )}

                    <PillLink
                        href={`${urls.home}#jadwal`}
                        variant="outline"
                        className="mt-8"
                    >
                        {t('booking.back')}
                    </PillLink>
                </div>
            </main>
        </>
    );
}
