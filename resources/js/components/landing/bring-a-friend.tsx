import { useState } from 'react';
import { BodyFigure } from '@/components/lentur/body-figures';
import { useTranslate } from '@/lib/i18n';
import type { SitePackage } from '@/types/site';
import { pillClassName } from './ui';

export function BringAFriend({
    pairPackage,
    bookingUrl,
    shareUrl,
}: {
    pairPackage: SitePackage | undefined;
    bookingUrl: string;
    shareUrl: string;
}) {
    const t = useTranslate();
    const [copied, setCopied] = useState(false);

    const copyLink = async () => {
        try {
            await navigator.clipboard.writeText(shareUrl);
            setCopied(true);
            window.setTimeout(() => setCopied(false), 3000);
        } catch {
            window.prompt(t('friend.copy'), shareUrl);
        }
    };

    return (
        <section aria-labelledby="friend-title" className="px-4 py-10 sm:px-8">
            {/* Putih hanya di atas biru. */}
            <div className="mx-auto grid max-w-6xl items-center gap-8 overflow-hidden rounded-blob-lg bg-lentur-blue p-8 text-white sm:p-12 lg:grid-cols-[1fr_auto]">
                <div>
                    <p className="inline-flex rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                        {t('friend.eyebrow')}
                    </p>
                    <h2
                        id="friend-title"
                        className="mt-4 font-display text-4xl leading-[1.05] sm:text-6xl"
                    >
                        {t('friend.title')}
                    </h2>
                    {pairPackage && (
                        <p className="mt-4 max-w-xl text-lg text-white/90">
                            {t('friend.body', {
                                name: pairPackage.name,
                                count: pairPackage.classCount,
                                price: pairPackage.priceLabel,
                            })}
                        </p>
                    )}
                    <div className="mt-8 flex flex-wrap items-center gap-3">
                        <a href={bookingUrl} className={pillClassName('lime')}>
                            {t('friend.cta')}
                        </a>
                        <button
                            type="button"
                            onClick={copyLink}
                            className="inline-flex min-h-11 items-center rounded-full px-5 font-semibold text-white ring-2 ring-white/40 transition-elastic hover:ring-white"
                        >
                            {t('friend.copy')}
                        </button>
                        <span role="status" className="text-sm text-white/90">
                            {copied ? t('friend.copied') : ''}
                        </span>
                    </div>
                </div>
                <div
                    aria-hidden="true"
                    className="flex items-end justify-center gap-2"
                >
                    <BodyFigure index={5} className="w-32 sm:w-44" />
                    <BodyFigure index={3} className="w-32 sm:w-44" />
                </div>
            </div>
        </section>
    );
}
