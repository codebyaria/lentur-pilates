import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { BringAFriend } from '@/components/landing/bring-a-friend';
import { Hero } from '@/components/landing/hero';
import { Instructors } from '@/components/landing/instructors';
import { NotHere } from '@/components/landing/not-here';
import { Pricing } from '@/components/landing/pricing';
import { Schedule } from '@/components/landing/schedule';
import { SiteFooter } from '@/components/landing/site-footer';
import { SiteHeader } from '@/components/landing/site-header';
import { WhichOne } from '@/components/landing/which-one';
import { useLocale, useTranslate } from '@/lib/i18n';
import { prefersReducedMotion } from '@/lib/motion';
import type { LandingProps, Level } from '@/types/site';

export default function Landing({
    days,
    schedule,
    levels,
    classTypes,
    packages,
    cancellationHours,
    instructors,
    studio,
    urls,
}: LandingProps) {
    const t = useTranslate();
    const locale = useLocale();

    const firstDayWith = (level: Level | null) =>
        days.find((day) =>
            schedule.some(
                (session) =>
                    session.day === day.key &&
                    (level === null || session.level === level),
            ),
        )?.key ??
        days[0]?.key ??
        '';

    const [level, setLevel] = useState<Level | null>(null);
    const [day, setDay] = useState(() => firstDayWith(null));

    const introPackage = packages.find((item) => item.isIntro);
    const nextIntroSession = schedule.find(
        (session) => session.isIntro && !session.isFull,
    );
    const introUrl = nextIntroSession?.bookingUrl ?? urls.booking;

    const changeLevel = (next: Level | null) => {
        setLevel(next);

        if (
            next !== null &&
            !schedule.some(
                (session) => session.day === day && session.level === next,
            )
        ) {
            setDay(firstDayWith(next));
        }
    };

    const pickLevel = (next: Level) => {
        changeLevel(next);
        document.getElementById('jadwal')?.scrollIntoView({
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    };

    return (
        <>
            <Head title={t('meta.title')}>
                <meta
                    head-key="description"
                    name="description"
                    content={t('meta.description', {
                        hours: cancellationHours,
                    })}
                />
                <link
                    head-key="alternate-current"
                    rel="alternate"
                    hrefLang={locale}
                    href={urls.home}
                />
                <link
                    head-key="alternate-other"
                    rel="alternate"
                    hrefLang={locale === 'en' ? 'id' : 'en'}
                    href={urls.alternateHome}
                />
            </Head>

            <div className="min-h-screen bg-lentur-cream font-sans text-lentur-ink antialiased">
                <SiteHeader urls={urls} introUrl={introUrl} />
                <main>
                    <Hero
                        introUrl={introUrl}
                        introPrice={introPackage?.priceLabel ?? null}
                    />
                    <WhichOne
                        levels={levels}
                        classTypes={classTypes}
                        onPick={pickLevel}
                    />
                    <Schedule
                        days={days}
                        sessions={schedule}
                        levels={levels}
                        day={day}
                        level={level}
                        onDayChange={setDay}
                        onLevelChange={changeLevel}
                    />
                    <Pricing
                        packages={packages}
                        cancellationHours={cancellationHours}
                    />
                    <NotHere />
                    <Instructors instructors={instructors} />
                    <BringAFriend
                        pairPackage={packages.find((item) => item.isShareable)}
                        bookingUrl={urls.booking}
                        shareUrl={urls.home}
                    />
                </main>
                <SiteFooter studio={studio} urls={urls} />
            </div>
        </>
    );
}
