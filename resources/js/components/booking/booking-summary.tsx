import { useTranslate } from '@/lib/i18n';
import type { ScheduleDay, ScheduleSession, SitePackage } from '@/types/site';

/** Ringkasan pilihan: kelas, paket, yang dibayar, dan aturan batal. */
export function BookingSummary({
    session,
    day,
    selectedPackage,
    usesExistingPackage,
    cancellationHours,
    onChangeClass,
}: {
    session: ScheduleSession | undefined;
    day: ScheduleDay | undefined;
    selectedPackage: SitePackage | undefined;
    usesExistingPackage: boolean;
    cancellationHours: number;
    onChangeClass?: () => void;
}) {
    const t = useTranslate();

    return (
        <aside
            aria-label={t('booking.summaryTitle')}
            className="rounded-blob-lg bg-white p-6 ring-1 ring-lentur-ink/5"
        >
            <h2 className="font-display text-2xl">
                {t('booking.summaryTitle')}
            </h2>

            <dl className="mt-4 grid gap-4">
                <div>
                    <dt className="text-sm font-semibold text-lentur-ink/60">
                        {t('booking.yourClass')}
                    </dt>
                    <dd className="mt-1">
                        {session ? (
                            <>
                                <span className="block text-lg font-bold">
                                    {session.className}
                                </span>
                                <span className="block text-lentur-ink/75">
                                    <span className="capitalize">
                                        {day?.label}
                                    </span>
                                    , {day?.date} · {session.time}–
                                    {session.endTime}
                                </span>
                                <span className="block text-lentur-ink/75">
                                    {t('schedule.with', {
                                        name: session.instructor,
                                    })}
                                </span>
                                {onChangeClass && (
                                    <button
                                        type="button"
                                        onClick={onChangeClass}
                                        className="mt-1 inline-flex min-h-11 items-center font-semibold text-lentur-blue underline underline-offset-4"
                                    >
                                        {t('booking.change')}
                                    </button>
                                )}
                            </>
                        ) : (
                            <span className="text-lentur-ink/60">
                                {t('booking.pickClassFirst')}
                            </span>
                        )}
                    </dd>
                </div>

                {(selectedPackage || usesExistingPackage) && (
                    <div>
                        <dt className="text-sm font-semibold text-lentur-ink/60">
                            {usesExistingPackage
                                ? t('confirmed.package')
                                : t('booking.payNow')}
                        </dt>
                        <dd className="mt-1">
                            {usesExistingPackage ? (
                                <span className="font-semibold">
                                    {t('booking.payExisting')}
                                </span>
                            ) : (
                                selectedPackage && (
                                    <>
                                        <span className="block font-display text-3xl">
                                            {selectedPackage.priceLabel}
                                        </span>
                                        <span className="text-lentur-ink/75">
                                            {selectedPackage.name}
                                        </span>
                                    </>
                                )
                            )}
                        </dd>
                    </div>
                )}
            </dl>

            <p className="mt-5 rounded-blob-sm bg-lentur-pink/40 p-4 text-sm font-semibold">
                {t('booking.cancelRule', { hours: cancellationHours })}
            </p>
        </aside>
    );
}
