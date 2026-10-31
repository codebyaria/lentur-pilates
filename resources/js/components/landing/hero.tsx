import { BodyFigures } from '@/components/lentur/body-figures';
import { ElasticWord } from '@/components/lentur/elastic-word';
import { useTranslate } from '@/lib/i18n';
import { Eyebrow, PillLink } from './ui';

/** Hero dipimpin gerak: kata yang bisa ditarik + tubuh yang meregang saat scroll. */
export function Hero({
    introUrl,
    introPrice,
}: {
    introUrl: string;
    introPrice: string | null;
}) {
    const t = useTranslate();

    return (
        <section
            aria-labelledby="hero-title"
            className="relative overflow-hidden"
        >
            <div className="mx-auto max-w-6xl px-4 pt-10 pb-6 sm:px-8 sm:pt-16">
                <Eyebrow>{t('hero.eyebrow')}</Eyebrow>

                <h1 id="hero-title" className="mt-6 -ml-1 sm:-ml-2">
                    <ElasticWord
                        word="lentur"
                        className="text-[clamp(6rem,27vw,17rem)] leading-[0.85] tracking-[-0.04em] text-lentur-blue"
                    />
                    <span className="sr-only"> pilates. </span>
                    <span className="mt-3 block font-display text-[clamp(1.75rem,4.5vw,3rem)] leading-tight">
                        {t('hero.tagline')}
                    </span>
                </h1>

                <p
                    className="mt-2 inline-flex items-center gap-2 text-sm text-lentur-ink/60"
                    aria-hidden="true"
                >
                    <span className="inline-block size-2 animate-pulse rounded-full bg-lentur-pink motion-reduce:animate-none" />
                    {t('hero.pullHint')}
                </p>

                <div className="mt-8 grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
                    <p className="max-w-xl text-lg text-lentur-ink/80 sm:text-xl">
                        {t('hero.lead')}
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <PillLink href={introUrl}>
                            {introPrice
                                ? t('hero.ctaIntro', { price: introPrice })
                                : t('nav.book')}
                        </PillLink>
                        <PillLink href="#jadwal" variant="outline">
                            {t('hero.ctaSchedule')}
                        </PillLink>
                    </div>
                </div>
            </div>

            <div className="mx-auto max-w-6xl px-2 sm:px-8">
                <BodyFigures
                    label={t('hero.figuresLabel')}
                    className="mx-auto max-w-4xl"
                />
            </div>
        </section>
    );
}
