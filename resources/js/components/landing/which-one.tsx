import { BodyFigure } from '@/components/lentur/body-figures';
import { useTranslate } from '@/lib/i18n';
import { cn } from '@/lib/utils';
import type { Level, SiteClassType } from '@/types/site';
import { Reveal, SectionHeading, pillClassName } from './ui';

const LEVEL_STYLE: Record<
    Level,
    {
        card: string;
        figure: number;
        button: 'primary' | 'lime' | 'pink' | 'outline';
    }
> = {
    // Warna badan figur sengaja beda dari warna kartu.
    baru_kenal: {
        card: 'bg-lentur-lime text-lentur-ink',
        figure: 0,
        button: 'outline',
    },
    mulai_akrab: {
        card: 'bg-lentur-pink text-lentur-ink',
        figure: 4,
        button: 'outline',
    },
    sudah_lengket: {
        card: 'bg-lentur-blue text-white',
        figure: 2,
        button: 'lime',
    },
};

export function WhichOne({
    levels,
    classTypes,
    onPick,
}: {
    levels: { value: Level; label: string }[];
    classTypes: SiteClassType[];
    onPick: (level: Level) => void;
}) {
    const t = useTranslate();

    return (
        <section
            aria-labelledby="which-title"
            className="mx-auto max-w-6xl px-4 py-20 sm:px-8"
        >
            <SectionHeading
                id="which-title"
                eyebrow={t('which.eyebrow')}
                title={t('which.title')}
                lead={t('which.lead')}
            />

            <div className="mt-10 grid gap-5 md:grid-cols-3">
                {levels.map((level, index) => {
                    const style = LEVEL_STYLE[level.value];
                    const names = classTypes
                        .filter((type) => type.level === level.value)
                        .map((type) => type.name);

                    return (
                        <Reveal key={level.value} delay={index * 90}>
                            <article
                                className={cn(
                                    'group flex h-full flex-col rounded-blob-lg p-7 transition-elastic hover:-translate-y-1.5 hover:scale-y-[1.02]',
                                    style.card,
                                )}
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <h3 className="font-display text-3xl">
                                        {level.label}
                                    </h3>
                                    <BodyFigure
                                        index={style.figure}
                                        className="-mt-4 w-24 shrink-0 origin-bottom transition-elastic group-hover:scale-y-110"
                                    />
                                </div>
                                <p className="mt-3 text-lg font-semibold">
                                    {t(`which.${level.value}.who`)}
                                </p>
                                <p className="mt-2 opacity-85">
                                    {t(`which.${level.value}.promise`)}
                                </p>
                                {names.length > 0 && (
                                    <p className="mt-5 text-sm">
                                        <span className="font-semibold">
                                            {t('which.classes')}:
                                        </span>{' '}
                                        {names.join(' · ')}
                                    </p>
                                )}
                                <button
                                    type="button"
                                    onClick={() => onPick(level.value)}
                                    className={pillClassName(
                                        style.button,
                                        cn(
                                            'mt-6 self-start',
                                            style.button === 'outline' &&
                                                'ring-lentur-ink/25',
                                        ),
                                    )}
                                >
                                    {t('which.cta', { level: level.label })}
                                </button>
                            </article>
                        </Reveal>
                    );
                })}
            </div>
        </section>
    );
}
