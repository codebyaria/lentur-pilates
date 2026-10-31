import { useTranslate } from '@/lib/i18n';

export function BookingHeader({
    homeUrl,
    alternateUrl,
}: {
    homeUrl: string;
    alternateUrl?: string;
}) {
    const t = useTranslate();

    return (
        <header className="border-b border-lentur-ink/5">
            <div className="mx-auto flex max-w-6xl items-center gap-4 px-4 py-3 sm:px-8">
                <a
                    href={homeUrl}
                    className="font-display text-2xl leading-none"
                    aria-label="Lentur Pilates"
                >
                    lentur<span className="text-lentur-blue">.</span>
                </a>
                {alternateUrl && (
                    <a
                        href={alternateUrl}
                        hrefLang={t('nav.switchLanguageShort').toLowerCase()}
                        aria-label={t('nav.switchLanguage')}
                        className="ml-auto inline-flex min-h-11 min-w-11 items-center justify-center rounded-full text-sm font-semibold ring-2 ring-lentur-ink/10 hover:ring-lentur-ink/30"
                    >
                        {t('nav.switchLanguageShort')}
                    </a>
                )}
            </div>
        </header>
    );
}
