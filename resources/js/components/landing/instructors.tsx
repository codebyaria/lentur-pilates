import { BodyFigure } from '@/components/lentur/body-figures';
import { useTranslate } from '@/lib/i18n';
import { cn } from '@/lib/utils';
import type { SiteInstructor } from '@/types/site';
import { Reveal, SectionHeading } from './ui';

const AVATAR_BG = [
    'bg-lentur-lime',
    'bg-lentur-pink',
    'bg-lentur-blue/15',
    'bg-lentur-lime/60',
];

export function Instructors({
    instructors,
}: {
    instructors: SiteInstructor[];
}) {
    const t = useTranslate();

    return (
        <section
            id="instruktur"
            aria-labelledby="instructors-title"
            className="mx-auto max-w-6xl scroll-mt-20 px-4 py-20 sm:px-8"
        >
            <SectionHeading
                id="instructors-title"
                eyebrow={t('instructors.eyebrow')}
                title={t('instructors.title')}
                lead={t('instructors.lead')}
            />

            <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                {instructors.map((instructor, index) => (
                    <Reveal key={instructor.slug} delay={index * 80}>
                        <article className="group flex h-full flex-col rounded-blob-lg bg-white p-6 ring-1 ring-lentur-ink/5">
                            <div
                                className={cn(
                                    'flex h-40 items-end justify-center overflow-hidden rounded-blob pt-4',
                                    AVATAR_BG[index % AVATAR_BG.length],
                                )}
                            >
                                <BodyFigure
                                    index={instructor.figure}
                                    className="w-28 origin-bottom transition-elastic group-hover:scale-y-[1.12]"
                                />
                            </div>
                            <h3 className="mt-5 font-display text-3xl">
                                {instructor.name}
                            </h3>
                            <p className="mt-1 font-semibold">
                                {instructor.tagline}
                            </p>
                            <p className="mt-3 flex-1 text-lentur-ink/75">
                                {instructor.bio}
                            </p>
                            <ul className="mt-4 flex flex-wrap gap-2">
                                {instructor.specialties.map((specialty) => (
                                    <li
                                        key={specialty}
                                        className="rounded-full bg-lentur-ink/5 px-3 py-1 text-sm font-semibold"
                                    >
                                        {specialty}
                                    </li>
                                ))}
                            </ul>
                        </article>
                    </Reveal>
                ))}
            </div>
        </section>
    );
}
