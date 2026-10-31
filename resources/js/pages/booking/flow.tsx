import { Head, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import { BookingHeader } from '@/components/booking/booking-header';
import { BookingSummary } from '@/components/booking/booking-summary';
import {
    ChoicePills,
    ErrorText,
    Field,
    inputClassName,
} from '@/components/booking/fields';
import { PackagePicker } from '@/components/booking/package-picker';
import { StepIndicator } from '@/components/booking/step-indicator';
import { ScheduleBrowser } from '@/components/landing/schedule';
import { pillClassName } from '@/components/landing/ui';
import { useTranslate } from '@/lib/i18n';
import { prefersReducedMotion } from '@/lib/motion';
import { cn } from '@/lib/utils';
import type { BookingFlowProps, BookingForm, Level } from '@/types/site';

type Step = 1 | 2 | 3;

const FIELDS_BY_STEP: Record<Step, (keyof BookingForm)[]> = {
    1: ['class_session_id'],
    2: ['package'],
    3: ['nickname', 'whatsapp', 'is_new_to_pilates', 'injury_notes'],
};

/**
 * Langkah 1–3 di sini (state di browser); langkah 4 = halaman konfirmasi setelah tersimpan.
 * Datang dari tombol "Pesan" di landing (?jadwal=) langsung ke langkah 2.
 */
export default function BookingFlow({
    days,
    schedule,
    levels,
    packages,
    existingPackageChoice,
    cancellationHours,
    initial,
    urls,
}: BookingFlowProps) {
    const t = useTranslate();
    const form = useForm<BookingForm>({
        class_session_id: initial.sessionId,
        package: initial.package ?? '',
        nickname: '',
        whatsapp: '',
        is_new_to_pilates: null,
        injury_notes: '',
    });
    const { data, setData, errors, processing } = form;

    const session = schedule.find((item) => item.id === data.class_session_id);
    const [step, setStep] = useState<Step>(initial.sessionId ? 2 : 1);
    const [level, setLevel] = useState<Level | null>(null);
    const [day, setDay] = useState(
        () =>
            session?.day ??
            days.find((item) => schedule.some((s) => s.day === item.key))
                ?.key ??
            days[0]?.key ??
            '',
    );
    const [localError, setLocalError] = useState<string | null>(null);
    const headingRef = useRef<HTMLHeadingElement>(null);
    const firstRender = useRef(true);

    const selectedPackage = packages.find((item) => item.slug === data.package);
    const usesExistingPackage = data.package === existingPackageChoice;
    const sessionDay = days.find((item) => item.key === session?.day);

    // Fokus ke judul langkah setiap kali pindah langkah (pembaca layar & keyboard).
    useEffect(() => {
        if (firstRender.current) {
            firstRender.current = false;

            return;
        }

        headingRef.current?.focus({ preventScroll: true });
        window.scrollTo({
            top: 0,
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    }, [step]);

    const goTo = (next: Step) => {
        setLocalError(null);
        setStep(next);
    };

    const chooseSession = (id: number) => {
        setData('class_session_id', id);
        form.clearErrors('class_session_id');
        const chosen = schedule.find((item) => item.id === id);

        if (chosen) {
            setDay(chosen.day);
        }

        goTo(2);
    };

    const nextFromPackage = () => {
        if (!data.package) {
            setLocalError(t('booking.pickPackageFirst'));

            return;
        }

        goTo(3);
    };

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        form.post(urls.submit, {
            // Tetap di posisi saat ada error; ke atas saat pindah ke konfirmasi.
            preserveScroll: (page) =>
                Object.keys(page.props.errors ?? {}).length > 0,
            onError: (serverErrors) => {
                const failedStep = ([1, 2, 3] as Step[]).find((candidate) =>
                    FIELDS_BY_STEP[candidate].some(
                        (field) => field in serverErrors,
                    ),
                );

                if (failedStep && failedStep !== 3) {
                    setStep(failedStep);
                }
            },
        });
    };

    const stepTitle = {
        1: t('booking.scheduleTitle'),
        2: t('booking.packageTitle'),
        3: t('booking.helloTitle'),
    }[step];

    const stepLead = {
        1: t('booking.scheduleLead'),
        2: t('booking.packageLead'),
        3: t('booking.helloLead'),
    }[step];

    return (
        <>
            <Head title={t('booking.metaTitle')} />
            <div className="min-h-screen bg-lentur-cream font-sans text-lentur-ink antialiased">
                <BookingHeader
                    homeUrl={urls.home}
                    alternateUrl={urls.alternate}
                />

                <main className="mx-auto max-w-6xl px-4 pt-8 pb-20 sm:px-8">
                    <StepIndicator current={step} />

                    <div className="mt-8 grid gap-8 lg:grid-cols-[1fr_22rem] lg:items-start">
                        <section
                            key={step}
                            className="animate-step-in"
                            aria-labelledby="step-title"
                        >
                            <p className="text-sm font-semibold text-lentur-ink/60">
                                {t('booking.stepOf', {
                                    current: step,
                                    total: 4,
                                })}
                            </p>
                            <h1
                                id="step-title"
                                ref={headingRef}
                                tabIndex={-1}
                                className="mt-2 font-display text-4xl leading-[1.05] outline-none sm:text-5xl"
                            >
                                {stepTitle}
                            </h1>
                            <p className="mt-3 max-w-xl text-lg text-lentur-ink/75">
                                {stepLead}
                            </p>

                            {step === 1 && (
                                <div className="mt-8">
                                    {errors.class_session_id && (
                                        <ErrorText className="mb-4">
                                            {errors.class_session_id}
                                        </ErrorText>
                                    )}
                                    <ScheduleBrowser
                                        days={days}
                                        sessions={schedule}
                                        levels={levels}
                                        day={day}
                                        level={level}
                                        onDayChange={setDay}
                                        onLevelChange={setLevel}
                                        selection={{
                                            selectedId: data.class_session_id,
                                            onSelect: chooseSession,
                                        }}
                                    />
                                </div>
                            )}

                            {step === 2 && (
                                <div className="mt-8">
                                    {(errors.package || localError) && (
                                        <ErrorText className="mb-4">
                                            {errors.package ?? localError}
                                        </ErrorText>
                                    )}
                                    <PackagePicker
                                        packages={packages}
                                        existingChoice={existingPackageChoice}
                                        value={data.package}
                                        onChange={(value) => {
                                            setData('package', value);
                                            setLocalError(null);
                                            form.clearErrors('package');
                                        }}
                                    />
                                    <StepActions
                                        onBack={() => goTo(1)}
                                        onNext={nextFromPackage}
                                    />
                                </div>
                            )}

                            {step === 3 && (
                                <form
                                    onSubmit={submit}
                                    className="mt-8 grid max-w-xl gap-6"
                                    noValidate
                                >
                                    <Field
                                        label={t('booking.nickname')}
                                        error={errors.nickname}
                                    >
                                        {(props) => (
                                            <input
                                                {...props}
                                                type="text"
                                                autoComplete="nickname"
                                                maxLength={40}
                                                placeholder={t(
                                                    'booking.nicknamePlaceholder',
                                                )}
                                                value={data.nickname}
                                                onChange={(event) =>
                                                    setData(
                                                        'nickname',
                                                        event.target.value,
                                                    )
                                                }
                                                className={inputClassName}
                                            />
                                        )}
                                    </Field>
                                    <Field
                                        label={t('booking.whatsapp')}
                                        hint={t('booking.whatsappHelp')}
                                        error={errors.whatsapp}
                                    >
                                        {(props) => (
                                            <input
                                                {...props}
                                                type="tel"
                                                inputMode="tel"
                                                autoComplete="tel"
                                                placeholder="0812 3456 7890"
                                                value={data.whatsapp}
                                                onChange={(event) =>
                                                    setData(
                                                        'whatsapp',
                                                        event.target.value,
                                                    )
                                                }
                                                className={inputClassName}
                                            />
                                        )}
                                    </Field>
                                    <ChoicePills
                                        name="is_new_to_pilates"
                                        legend={t('booking.firstTime')}
                                        options={[
                                            {
                                                value: true,
                                                label: t(
                                                    'booking.firstTimeYes',
                                                ),
                                            },
                                            {
                                                value: false,
                                                label: t('booking.firstTimeNo'),
                                            },
                                        ]}
                                        value={data.is_new_to_pilates}
                                        onChange={(value) =>
                                            setData('is_new_to_pilates', value)
                                        }
                                        error={errors.is_new_to_pilates}
                                    />
                                    <Field
                                        label={t('booking.injury')}
                                        optionalLabel={t('booking.optional')}
                                        hint={t('booking.injuryHelp')}
                                        error={errors.injury_notes}
                                    >
                                        {(props) => (
                                            <textarea
                                                {...props}
                                                rows={3}
                                                maxLength={500}
                                                value={data.injury_notes}
                                                onChange={(event) =>
                                                    setData(
                                                        'injury_notes',
                                                        event.target.value,
                                                    )
                                                }
                                                className={cn(
                                                    inputClassName,
                                                    'resize-y',
                                                )}
                                            />
                                        )}
                                    </Field>

                                    <p className="text-sm text-lentur-ink/60">
                                        {t('booking.simulated')}
                                    </p>

                                    <StepActions
                                        onBack={() => goTo(2)}
                                        submitLabel={
                                            processing
                                                ? t('booking.submitting')
                                                : t('booking.submit')
                                        }
                                        disabled={processing}
                                    />
                                </form>
                            )}
                        </section>

                        <div className="lg:sticky lg:top-6">
                            <BookingSummary
                                session={session}
                                day={sessionDay}
                                selectedPackage={selectedPackage}
                                usesExistingPackage={usesExistingPackage}
                                cancellationHours={cancellationHours}
                                onChangeClass={
                                    step > 1 ? () => goTo(1) : undefined
                                }
                            />
                        </div>
                    </div>
                </main>
            </div>
        </>
    );
}

function StepActions({
    onBack,
    onNext,
    submitLabel,
    disabled = false,
}: {
    onBack: () => void;
    onNext?: () => void;
    submitLabel?: string;
    disabled?: boolean;
}) {
    const t = useTranslate();

    return (
        <div className="mt-8 flex flex-wrap items-center gap-3">
            {submitLabel ? (
                <button
                    type="submit"
                    disabled={disabled}
                    className={pillClassName('primary', 'disabled:opacity-60')}
                >
                    {submitLabel}
                </button>
            ) : (
                <button
                    type="button"
                    onClick={onNext}
                    className={pillClassName('primary')}
                >
                    {t('booking.next')}
                </button>
            )}
            <button
                type="button"
                onClick={onBack}
                className={pillClassName('outline')}
            >
                {t('booking.back')}
            </button>
        </div>
    );
}
