import { Head, useForm } from '@inertiajs/react';
import { BookingHeader } from '@/components/booking/booking-header';
import { ErrorText } from '@/components/booking/fields';
import { BodyFigure } from '@/components/lentur/body-figures';
import { pillClassName } from '@/components/landing/ui';
import { useTranslate } from '@/lib/i18n';

type CancelPageProps = {
    booking: {
        code: string;
        nickname: string;
        className: string;
        dateLabel: string;
        time: string;
        endTime: string;
        instructor: string;
        status: 'confirmed' | 'attended' | 'cancelled' | 'no_show';
        canCancel: boolean;
        isFree: boolean;
        deadline: string;
        packageName: string | null;
        classesRemaining: number | null;
    };
    justCancelled: { refunded: boolean } | null;
    urls: {
        submit: string;
        confirmation: string;
        home: string;
        bookAnother: string;
    };
};

/** Batal sendiri: jelas soal kuota, tanpa nada menyalahkan. */
export default function BookingCancel({
    booking,
    justCancelled,
    urls,
}: CancelPageProps) {
    const t = useTranslate();
    // Tanpa isian; `booking` hanya untuk menampung pesan error dari server.
    const form = useForm<{ booking?: string }>({});

    const cancel = () => form.post(urls.submit, { preserveScroll: true });

    return (
        <>
            <Head title={t('cancel.metaTitle')} />
            <div className="min-h-screen bg-lentur-cream font-sans text-lentur-ink antialiased">
                <BookingHeader homeUrl={urls.home} />

                <main className="mx-auto max-w-2xl px-4 pt-10 pb-20 sm:px-8">
                    <BodyFigure
                        index={justCancelled ? 2 : 4}
                        className="w-24"
                    />

                    {justCancelled ? (
                        <section className="animate-step-in" aria-live="polite">
                            <h1 className="mt-6 font-display text-4xl sm:text-5xl">
                                {t('cancel.doneTitle')}
                            </h1>
                            <p className="mt-4 text-lg text-lentur-ink/80">
                                {justCancelled.refunded
                                    ? t('cancel.doneRefunded')
                                    : t('cancel.doneCounted')}
                            </p>
                            {booking.packageName &&
                                booking.classesRemaining !== null && (
                                    <p className="mt-2 text-lentur-ink/70">
                                        {t('cancel.remaining', {
                                            name: booking.packageName,
                                            count: booking.classesRemaining,
                                        })}
                                    </p>
                                )}
                        </section>
                    ) : (
                        <section>
                            <h1 className="mt-6 font-display text-4xl sm:text-5xl">
                                {t('cancel.title', { name: booking.nickname })}
                            </h1>
                            {booking.canCancel && (
                                <p className="mt-4 text-lg text-lentur-ink/80">
                                    {t('cancel.lead')}
                                </p>
                            )}
                        </section>
                    )}

                    <div className="mt-8 rounded-blob-lg bg-white p-6 ring-1 ring-lentur-ink/5">
                        <p className="text-xl font-bold">{booking.className}</p>
                        <p className="mt-1 text-lentur-ink/75">
                            <span className="capitalize">
                                {booking.dateLabel}
                            </span>{' '}
                            · {booking.time}–{booking.endTime} ·{' '}
                            {t('schedule.with', { name: booking.instructor })}
                        </p>
                        <p className="mt-3 font-mono text-sm font-semibold tracking-wider text-lentur-ink/60">
                            {booking.code}
                        </p>
                    </div>

                    {!justCancelled && booking.canCancel && (
                        <>
                            <p className="mt-5 rounded-blob-sm bg-lentur-pink/40 p-5 font-semibold">
                                {booking.isFree
                                    ? t('cancel.free', {
                                          deadline: booking.deadline,
                                      })
                                    : t('cancel.late', {
                                          deadline: booking.deadline,
                                      })}
                            </p>
                            {form.errors.booking && (
                                <ErrorText>{form.errors.booking}</ErrorText>
                            )}
                            <div className="mt-8 flex flex-wrap gap-3">
                                <button
                                    type="button"
                                    onClick={cancel}
                                    disabled={form.processing}
                                    className={pillClassName(
                                        'pink',
                                        'disabled:opacity-60',
                                    )}
                                >
                                    {form.processing
                                        ? t('cancel.cancelling')
                                        : t('cancel.confirm')}
                                </button>
                                <a
                                    href={urls.confirmation}
                                    className={pillClassName('primary')}
                                >
                                    {t('cancel.keep')}
                                </a>
                            </div>
                        </>
                    )}

                    {!justCancelled && !booking.canCancel && (
                        <p className="mt-5 rounded-blob-sm bg-lentur-ink/5 p-5 font-semibold">
                            {booking.status === 'cancelled'
                                ? t('cancel.alreadyCancelled')
                                : t('cancel.notCancellable')}
                        </p>
                    )}

                    {(justCancelled || !booking.canCancel) && (
                        <div className="mt-8 flex flex-wrap gap-3">
                            <a
                                href={urls.bookAnother}
                                className={pillClassName('primary')}
                            >
                                {t('cancel.bookAnother')}
                            </a>
                            <a
                                href={urls.home}
                                className={pillClassName('outline')}
                            >
                                {t('confirmed.home')}
                            </a>
                        </div>
                    )}
                </main>
            </div>
        </>
    );
}
