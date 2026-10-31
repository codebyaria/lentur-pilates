import { useEffect, useRef, useState } from 'react';
import type { ComponentProps, ReactNode } from 'react';
import { useReducedMotion } from '@/lib/motion';
import { cn } from '@/lib/utils';

type PillVariant = 'primary' | 'lime' | 'outline' | 'pink';

/** Putih HANYA di atas biru; limau dan merah muda selalu tinta biru. */
const PILL_VARIANTS: Record<PillVariant, string> = {
    primary: 'bg-lentur-blue text-white hover:bg-[#2449c4]',
    lime: 'bg-lentur-lime text-lentur-ink hover:bg-[#bde33a]',
    pink: 'bg-lentur-pink text-lentur-ink hover:bg-[#ff8dba]',
    outline:
        'bg-transparent text-lentur-ink ring-2 ring-lentur-ink/15 hover:ring-lentur-ink/40',
};

export function pillClassName(
    variant: PillVariant = 'primary',
    className?: string,
) {
    return cn(
        'inline-flex min-h-11 items-center justify-center gap-2 rounded-full px-6 py-3 text-base font-semibold transition-elastic hover:scale-[1.04] focus-visible:ring-4 focus-visible:ring-lentur-blue/40 focus-visible:outline-none active:scale-[0.97]',
        PILL_VARIANTS[variant],
        className,
    );
}

export function PillLink({
    variant = 'primary',
    className,
    ...props
}: ComponentProps<'a'> & { variant?: PillVariant }) {
    return <a className={pillClassName(variant, className)} {...props} />;
}

export function Eyebrow({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <p
            className={cn(
                'inline-flex rounded-full bg-lentur-ink/5 px-3 py-1 text-sm font-semibold text-lentur-ink/80',
                className,
            )}
        >
            {children}
        </p>
    );
}

export function SectionHeading({
    eyebrow,
    title,
    lead,
    id,
    className,
}: {
    eyebrow: string;
    title: string;
    lead?: string;
    id?: string;
    className?: string;
}) {
    return (
        <div className={cn('max-w-2xl', className)}>
            <Eyebrow>{eyebrow}</Eyebrow>
            <h2
                id={id}
                className="mt-4 font-display text-4xl leading-[1.05] text-balance sm:text-5xl"
            >
                {title}
            </h2>
            {lead && <p className="mt-4 text-lg text-lentur-ink/75">{lead}</p>}
        </div>
    );
}

/**
 * Muncul sekali saat masuk layar: sedikit memanjang lalu memantul ke tempatnya.
 * Dengan reduced motion: cukup fade.
 */
export function Reveal({
    children,
    className,
    delay = 0,
}: {
    children: ReactNode;
    className?: string;
    delay?: number;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const [visible, setVisible] = useState(false);
    const reducedMotion = useReducedMotion();

    useEffect(() => {
        const element = ref.current;

        if (!element || typeof IntersectionObserver === 'undefined') {
            setVisible(true);

            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setVisible(true);
                    observer.disconnect();
                }
            },
            { rootMargin: '0px 0px -10% 0px' },
        );

        observer.observe(element);

        return () => observer.disconnect();
    }, []);

    return (
        <div
            ref={ref}
            className={cn('transition-elastic', className)}
            style={{
                transitionDelay: `${delay}ms`,
                opacity: visible ? 1 : 0,
                transform:
                    visible || reducedMotion
                        ? 'none'
                        : 'translateY(24px) scaleY(1.06)',
                transformOrigin: 'bottom',
            }}
        >
            {children}
        </div>
    );
}
