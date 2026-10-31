import { useId } from 'react';
import type { ComponentProps, ReactNode } from 'react';
import { cn } from '@/lib/utils';

export const inputClassName =
    'w-full rounded-blob-sm border-0 bg-white px-5 py-4 text-lg text-lentur-ink ring-1 ring-lentur-ink/15 placeholder:text-lentur-ink/35 focus:ring-4 focus:ring-lentur-blue/40 focus:outline-none aria-invalid:ring-2 aria-invalid:ring-lentur-pink';

/** Label, bantuan, dan pesan error yang terhubung ke input lewat aria. */
export function Field({
    label,
    hint,
    optionalLabel,
    error,
    children,
}: {
    label: string;
    hint?: string;
    optionalLabel?: string;
    error?: string;
    children: (props: {
        id: string;
        'aria-describedby'?: string;
        'aria-invalid'?: true;
    }) => ReactNode;
}) {
    const id = useId();
    const hintId = `${id}-hint`;
    const errorId = `${id}-error`;
    const describedBy =
        [hint ? hintId : null, error ? errorId : null]
            .filter(Boolean)
            .join(' ') || undefined;

    return (
        <div>
            <label htmlFor={id} className="block font-semibold">
                {label}
                {optionalLabel && (
                    <span className="ml-2 text-sm font-normal text-lentur-ink/55">
                        ({optionalLabel})
                    </span>
                )}
            </label>
            <div className="mt-2">
                {children({
                    id,
                    'aria-describedby': describedBy,
                    'aria-invalid': error ? true : undefined,
                })}
            </div>
            {hint && (
                <p id={hintId} className="mt-2 text-sm text-lentur-ink/60">
                    {hint}
                </p>
            )}
            {error && <ErrorText id={errorId}>{error}</ErrorText>}
        </div>
    );
}

export function ErrorText({ className, ...props }: ComponentProps<'p'>) {
    return (
        <p
            role="alert"
            className={cn(
                'mt-2 inline-flex rounded-full bg-lentur-pink px-3 py-1 text-sm font-semibold text-lentur-ink',
                className,
            )}
            {...props}
        />
    );
}

/** Dua pilihan sebagai pil (ya/tidak). */
export function ChoicePills({
    name,
    legend,
    options,
    value,
    onChange,
    error,
}: {
    name: string;
    legend: string;
    options: { value: boolean; label: string }[];
    value: boolean | null;
    onChange: (value: boolean) => void;
    error?: string;
}) {
    const errorId = useId();

    return (
        <fieldset aria-describedby={error ? errorId : undefined}>
            <legend className="font-semibold">{legend}</legend>
            <div className="mt-2 flex flex-wrap gap-2">
                {options.map((option) => (
                    <label
                        key={String(option.value)}
                        className={cn(
                            'inline-flex min-h-12 cursor-pointer items-center rounded-full px-5 font-semibold transition-elastic has-focus-visible:ring-4 has-focus-visible:ring-lentur-blue/40',
                            value === option.value
                                ? 'scale-[1.03] bg-lentur-blue text-white'
                                : 'bg-white ring-1 ring-lentur-ink/15 hover:ring-lentur-ink/40',
                        )}
                    >
                        <input
                            type="radio"
                            name={name}
                            className="sr-only"
                            checked={value === option.value}
                            onChange={() => onChange(option.value)}
                        />
                        {option.label}
                    </label>
                ))}
            </div>
            {error && <ErrorText id={errorId}>{error}</ErrorText>}
        </fieldset>
    );
}
