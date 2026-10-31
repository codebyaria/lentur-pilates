import { useTranslate } from '@/lib/i18n';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';
import type { SitePackage } from '@/types/site';

/** Pilihan paket sebagai kartu radio; kelas pertama paling atas, "paket aktif" paling bawah. */
export function PackagePicker({
    packages,
    existingChoice,
    value,
    onChange,
}: {
    packages: SitePackage[];
    existingChoice: string;
    value: string;
    onChange: (value: string) => void;
}) {
    const t = useTranslate();

    return (
        <fieldset>
            <legend className="sr-only">{t('booking.packageTitle')}</legend>
            <div className="grid gap-3">
                {packages.map((item) => (
                    <Option
                        key={item.slug}
                        name="package"
                        value={item.slug}
                        checked={value === item.slug}
                        onChange={onChange}
                        highlight={item.isIntro}
                    >
                        <span className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <span className="text-lg font-bold">
                                {item.name}
                            </span>
                            <span className="font-display text-2xl whitespace-nowrap">
                                {item.priceLabel}
                            </span>
                        </span>
                        <span className="mt-1 block text-lentur-ink/75">
                            {item.summary}
                        </span>
                        <span className="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                            {item.isIntro && (
                                <span className="rounded-full bg-lentur-ink/10 px-2.5 py-1">
                                    {t('booking.introNote')}
                                </span>
                            )}
                            {item.classCount > 1 && (
                                <span className="rounded-full bg-lentur-ink/5 px-2.5 py-1">
                                    {t('pricing.perClass', {
                                        price: item.perClassLabel,
                                    })}
                                </span>
                            )}
                            <span className="rounded-full bg-lentur-ink/5 px-2.5 py-1">
                                {t('pricing.validity', {
                                    days: item.validityDays,
                                })}
                            </span>
                        </span>
                    </Option>
                ))}

                <Option
                    name="package"
                    value={existingChoice}
                    checked={value === existingChoice}
                    onChange={onChange}
                    dashed
                >
                    <span className="text-lg font-bold">
                        {t('booking.existingTitle')}
                    </span>
                    <span className="mt-1 block text-lentur-ink/75">
                        {t('booking.existingBody')}
                    </span>
                </Option>
            </div>
        </fieldset>
    );
}

function Option({
    name,
    value,
    checked,
    onChange,
    highlight = false,
    dashed = false,
    children,
}: {
    name: string;
    value: string;
    checked: boolean;
    onChange: (value: string) => void;
    highlight?: boolean;
    dashed?: boolean;
    children: ReactNode;
}) {
    return (
        <label
            className={cn(
                'relative flex cursor-pointer gap-4 rounded-blob p-5 transition-elastic has-focus-visible:ring-4 has-focus-visible:ring-lentur-blue/40',
                highlight ? 'bg-lentur-lime' : 'bg-white',
                dashed
                    ? 'border-2 border-dashed border-lentur-ink/20 bg-transparent'
                    : 'ring-1 ring-lentur-ink/5',
                checked && 'ring-4 ring-lentur-blue',
                !checked && 'hover:-translate-y-0.5',
            )}
        >
            <input
                type="radio"
                name={name}
                value={value}
                checked={checked}
                onChange={() => onChange(value)}
                className="sr-only"
            />
            <span
                aria-hidden="true"
                className={cn(
                    'mt-1 flex size-6 shrink-0 items-center justify-center rounded-full ring-2 transition-elastic',
                    checked
                        ? 'scale-110 bg-lentur-blue text-white ring-lentur-blue'
                        : 'ring-lentur-ink/25',
                )}
            >
                {checked && <span className="text-xs">✓</span>}
            </span>
            <span className="min-w-0 flex-1">{children}</span>
        </label>
    );
}
