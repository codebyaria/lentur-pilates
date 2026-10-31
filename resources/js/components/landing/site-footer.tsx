import { useTranslate } from '@/lib/i18n';
import type { SiteUrls } from '@/types/site';

export function SiteFooter({
    studio,
    urls,
}: {
    studio: { name: string; address: string };
    urls: SiteUrls;
}) {
    const t = useTranslate();

    return (
        <footer className="mx-auto max-w-6xl px-4 pt-10 pb-12 sm:px-8">
            <div className="grid gap-8 border-t border-lentur-ink/10 pt-10 sm:grid-cols-3">
                <div>
                    <p className="font-display text-3xl leading-none">
                        lentur<span className="text-lentur-blue">.</span>
                    </p>
                    <p className="mt-3 text-lentur-ink/70">
                        {t('hero.tagline')}
                    </p>
                </div>
                <div>
                    <p className="font-semibold">{t('footer.visit')}</p>
                    <p className="mt-2 text-lentur-ink/75">
                        {studio.name}
                        <br />
                        {studio.address}
                        <br />
                        {t('footer.hours')}
                    </p>
                </div>
                <div>
                    <p className="font-semibold">{t('footer.language')}</p>
                    <a
                        href={urls.alternateHome}
                        className="mt-2 inline-flex min-h-11 items-center font-semibold text-lentur-blue underline underline-offset-4"
                    >
                        {t('nav.switchLanguage')}
                    </a>
                </div>
            </div>
            <p className="mt-10 text-sm text-lentur-ink/60">
                {t('footer.demo')}
            </p>
        </footer>
    );
}
