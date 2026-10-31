import { useTranslate } from '@/lib/i18n';
import type { SitePackage } from '@/types/site';
import { Reveal, SectionHeading, pillClassName } from './ui';

export function Pricing({
    packages,
    cancellationHours,
}: {
    packages: SitePackage[];
    cancellationHours: number;
}) {
    const t = useTranslate();
    const intro = packages.find((item) => item.isIntro);
    const others = packages.filter((item) => !item.isIntro);

    return (
        <section
            id="harga"
            aria-labelledby="pricing-title"
            className="mx-auto max-w-6xl scroll-mt-20 px-4 py-20 sm:px-8"
        >
            <SectionHeading
                id="pricing-title"
                eyebrow={t('pricing.eyebrow')}
                title={t('pricing.title')}
                lead={t('pricing.anyClass')}
            />

            {intro && (
                <Reveal className="mt-10">
                    <article className="grid gap-6 rounded-blob-lg bg-lentur-lime p-7 sm:p-10 lg:grid-cols-[1fr_1fr_auto] lg:items-end">
                        <div>
                            <span className="rounded-full bg-lentur-ink/10 px-3 py-1 text-xs font-semibold">
                                {t('pricing.startHere')}
                            </span>
                            <h3 className="mt-4 text-2xl font-bold">
                                {intro.name}
                            </h3>
                            <p className="mt-3 font-display text-6xl leading-none whitespace-nowrap sm:text-7xl">
                                {intro.priceLabel}
                            </p>
                        </div>
                        <div>
                            <p className="text-lg">{intro.summary}</p>
                            <p className="mt-2 text-sm text-lentur-ink/70">
                                <PackageFacts item={intro} />
                            </p>
                        </div>
                        <a
                            href={intro.bookingUrl}
                            className={pillClassName(
                                'primary',
                                'self-start lg:self-end',
                            )}
                        >
                            {t('pricing.choose')}
                        </a>
                    </article>
                </Reveal>
            )}

            <div className="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                {others.map((item, index) => (
                    <Reveal key={item.slug} delay={index * 70}>
                        <article className="flex h-full flex-col rounded-blob-lg bg-white p-7 ring-1 ring-lentur-ink/5 transition-elastic hover:-translate-y-1">
                            <div className="flex min-h-6 flex-wrap items-center gap-2">
                                {item.isShareable && (
                                    <span className="rounded-full bg-lentur-pink px-3 py-1 text-xs font-semibold">
                                        {t('pricing.forTwo')}
                                    </span>
                                )}
                            </div>
                            <h3 className="mt-3 text-xl font-bold">
                                {item.name}
                            </h3>
                            <p className="mt-3 font-display text-4xl leading-none whitespace-nowrap lg:text-3xl xl:text-4xl">
                                {item.priceLabel}
                            </p>
                            <p className="mt-2 text-sm text-lentur-ink/70">
                                <PackageFacts item={item} />
                            </p>
                            <p className="mt-4 flex-1 text-lentur-ink/80">
                                {item.summary}
                            </p>
                            <a
                                href={item.bookingUrl}
                                className={pillClassName(
                                    'outline',
                                    'mt-6 self-start',
                                )}
                            >
                                {t('pricing.choose')}
                            </a>
                        </article>
                    </Reveal>
                ))}
            </div>

            <Reveal>
                <aside
                    aria-labelledby="rules-title"
                    className="mt-8 rounded-blob-lg bg-lentur-pink p-7 sm:p-10"
                >
                    <h3 id="rules-title" className="font-display text-3xl">
                        {t('pricing.rulesTitle')}
                    </h3>
                    <ul className="mt-5 grid gap-3 text-lg sm:grid-cols-3 sm:gap-6">
                        {(
                            [
                                'pricing.rule1',
                                'pricing.rule2',
                                'pricing.rule3',
                            ] as const
                        ).map((rule, index) => (
                            <li key={rule} className="flex gap-3">
                                <span
                                    aria-hidden="true"
                                    className="font-display text-2xl leading-7"
                                >
                                    {index + 1}
                                </span>
                                <span>
                                    {t(rule, { hours: cancellationHours })}
                                </span>
                            </li>
                        ))}
                    </ul>
                </aside>
            </Reveal>
        </section>
    );
}

function PackageFacts({ item }: { item: SitePackage }) {
    const t = useTranslate();
    const facts = [
        item.classCount === 1
            ? t('pricing.class')
            : t('pricing.classes', { count: item.classCount }),
        item.classCount > 1
            ? t('pricing.perClass', { price: item.perClassLabel })
            : null,
        t('pricing.validity', { days: item.validityDays }),
    ];

    return <>{facts.filter(Boolean).join(' · ')}</>;
}
