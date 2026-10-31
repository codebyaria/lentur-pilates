import { useTranslate } from '@/lib/i18n';
import { cn } from '@/lib/utils';

const STEPS = [
    'booking.steps.schedule',
    'booking.steps.package',
    'booking.steps.hello',
    'booking.steps.done',
] as const;

/** Empat langkah sebagai pil yang memanjang saat aktif. */
export function StepIndicator({ current }: { current: 1 | 2 | 3 | 4 }) {
    const t = useTranslate();

    return (
        <nav aria-label={t('booking.stepOf', { current, total: STEPS.length })}>
            <ol className="flex items-center gap-2">
                {STEPS.map((key, index) => {
                    const step = index + 1;
                    const state =
                        step < current
                            ? 'done'
                            : step === current
                              ? 'current'
                              : 'todo';

                    return (
                        <li
                            key={key}
                            aria-current={
                                state === 'current' ? 'step' : undefined
                            }
                            className={cn(
                                'flex h-9 min-w-9 items-center justify-center gap-2 rounded-full px-3 text-sm font-semibold transition-elastic',
                                state === 'current' &&
                                    'bg-lentur-blue px-4 text-white',
                                state === 'done' &&
                                    'bg-lentur-lime text-lentur-ink',
                                state === 'todo' &&
                                    'bg-lentur-ink/5 text-lentur-ink/50',
                            )}
                        >
                            <span aria-hidden="true">
                                {state === 'done' ? '✓' : step}
                            </span>
                            <span
                                className={cn(
                                    state === 'current' ? 'inline' : 'sr-only',
                                )}
                            >
                                {t(key)}
                            </span>
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}
