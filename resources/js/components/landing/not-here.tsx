import { useTranslate } from '@/lib/i18n';
import { Reveal, SectionHeading } from './ui';

const ITEMS = [
    'photos',
    'counting',
    'shouting',
    'mirrors',
    'contracts',
    'fees',
] as const;

export function NotHere() {
    const t = useTranslate();

    return (
        <section
            aria-labelledby="not-here-title"
            className="bg-lentur-lime py-20"
        >
            <div className="mx-auto max-w-6xl px-4 sm:px-8">
                <SectionHeading
                    id="not-here-title"
                    eyebrow={t('notHere.eyebrow')}
                    title={t('notHere.title')}
                />
                <ul className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {ITEMS.map((item, index) => (
                        <Reveal key={item} delay={index * 60}>
                            <li className="h-full list-none rounded-blob bg-lentur-cream p-6">
                                <p className="flex items-start gap-3">
                                    <span
                                        aria-hidden="true"
                                        className="mt-0.5 inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-lentur-pink text-sm font-bold"
                                    >
                                        ✕
                                    </span>
                                    <span className="text-lg font-bold line-through decoration-lentur-pink decoration-[3px]">
                                        {t(`notHere.${item}.title`)}
                                    </span>
                                </p>
                                <p className="mt-3 text-lentur-ink/80">
                                    {t(`notHere.${item}.body`)}
                                </p>
                            </li>
                        </Reveal>
                    ))}
                </ul>
            </div>
        </section>
    );
}
