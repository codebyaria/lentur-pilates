import { useTranslate } from '@/lib/i18n';
import type { SiteUrls } from '@/types/site';
import { PillLink } from './ui';

export function SiteHeader({
    urls,
    introUrl,
}: {
    urls: SiteUrls;
    introUrl: string;
}) {
    const t = useTranslate();

    return (
        <header className="sticky top-0 z-30 border-b border-lentur-ink/5 bg-lentur-cream/85 backdrop-blur">
            <a
                href="#jadwal"
                className="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-4 focus:rounded-full focus:bg-lentur-blue focus:px-4 focus:py-2 focus:text-white"
            >
                {t('nav.skip')}
            </a>
            <div className="mx-auto flex max-w-6xl items-center gap-4 px-4 py-3 sm:px-8">
                <a
                    href={urls.home}
                    className="font-display text-2xl leading-none"
                    aria-label="Lentur Pilates"
                >
                    lentur<span className="text-lentur-blue">.</span>
                </a>
                <nav
                    aria-label="Utama"
                    className="ml-6 hidden items-center gap-6 text-sm font-semibold md:flex"
                >
                    <a href="#jadwal" className="hover:text-lentur-blue">
                        {t('nav.schedule')}
                    </a>
                    <a href="#harga" className="hover:text-lentur-blue">
                        {t('nav.pricing')}
                    </a>
                    <a href="#instruktur" className="hover:text-lentur-blue">
                        {t('nav.instructors')}
                    </a>
                </nav>
                <div className="ml-auto flex items-center gap-2">
                    <a
                        href={urls.alternateHome}
                        hrefLang={t('nav.switchLanguageShort').toLowerCase()}
                        aria-label={t('nav.switchLanguage')}
                        className="inline-flex min-h-11 min-w-11 items-center justify-center rounded-full text-sm font-semibold ring-2 ring-lentur-ink/10 hover:ring-lentur-ink/30"
                    >
                        {t('nav.switchLanguageShort')}
                    </a>
                    <PillLink href={introUrl} className="px-5 py-2.5 text-sm">
                        {t('nav.book')}
                    </PillLink>
                </div>
            </div>
        </header>
    );
}
