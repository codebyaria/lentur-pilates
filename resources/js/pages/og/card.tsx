import { BodyFigures } from '@/components/lentur/body-figures';
import { useTranslate } from '@/lib/i18n';

/**
 * Gambar pratinjau tautan (Open Graph), 1200×630. Hanya dirender lokal lalu
 * di-screenshot ke public/images/og/ (lihat DEPLOY.md); tidak tersedia di produksi.
 */
export default function OgCard() {
    const t = useTranslate();

    return (
        <main
            className="relative flex h-[630px] w-[1200px] flex-col overflow-hidden bg-lentur-cream px-20 pt-16 font-sans text-lentur-ink antialiased"
            data-og-card
        >
            <span
                aria-hidden="true"
                className="absolute -top-24 -right-24 size-80 rounded-full bg-lentur-lime"
            />
            <span
                aria-hidden="true"
                className="absolute top-40 -right-10 size-28 rounded-full bg-lentur-pink"
            />

            <p className="relative inline-flex self-start rounded-full bg-lentur-ink/5 px-4 py-1.5 text-xl font-semibold text-lentur-ink/80">
                {t('hero.eyebrow')}
            </p>
            <p className="relative mt-4 font-display text-[200px] leading-[0.85] tracking-[-0.04em] text-lentur-blue">
                lentur
            </p>
            <p className="relative mt-4 font-display text-5xl">
                {t('hero.tagline')}
            </p>

            <div className="absolute right-10 bottom-0 w-[620px]">
                <BodyFigures stretchOnScroll={false} label="" />
            </div>
            <p className="absolute bottom-10 left-20 text-xl font-semibold text-lentur-ink/60">
                lentur.arizain.my.id
            </p>
        </main>
    );
}
