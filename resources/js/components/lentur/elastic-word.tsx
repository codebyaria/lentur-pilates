import { useEffect, useRef } from 'react';
import type { KeyboardEvent, PointerEvent } from 'react';
import {
    FADE_DURATION,
    FADE_EASING,
    animateSpring,
    clamp,
    rubberband,
    springs,
    useReducedMotion,
} from '@/lib/motion';
import type { SpringAnimation } from '@/lib/motion';
import { cn } from '@/lib/utils';

type ElasticWordProps = {
    word?: string;
    className?: string;
};

type Offset = { x: number; y: number };

const PULL_LIMIT_X = 180;
const PULL_LIMIT_Y = 130;
const CLICK_THRESHOLD = 4;
/** Kecepatan awal "petikan" saat diklik atau ditekan Enter/Spasi. */
const PLUCK_VELOCITY = -900;

/**
 * Kata yang bisa ditarik kursor/diusap, lalu memantul kembali.
 * Huruf terdekat dari titik tarik bergerak paling jauh, tetangganya ikut
 * sedikit, sehingga kata terlihat memanjang seperti karet.
 */
export function ElasticWord({ word = 'lentur', className }: ElasticWordProps) {
    const reducedMotion = useReducedMotion();
    const containerRef = useRef<HTMLSpanElement>(null);
    const letterRefs = useRef<(HTMLSpanElement | null)[]>([]);
    const offset = useRef<Offset>({ x: 0, y: 0 });
    const grabIndex = useRef(0);
    const drag = useRef<{
        startX: number;
        startY: number;
        lastX: number;
        lastY: number;
        lastTime: number;
        velocityX: number;
        velocityY: number;
        moved: boolean;
    } | null>(null);
    const springX = useRef<SpringAnimation | null>(null);
    const springY = useRef<SpringAnimation | null>(null);

    const letters = Array.from(word);

    const render = () => {
        const { x, y } = offset.current;
        const spread = Math.max(1.2, letters.length / 3);

        letterRefs.current.forEach((letter, index) => {
            if (!letter) {
                return;
            }

            const distance = index - grabIndex.current;
            const weight = Math.exp(
                -(distance * distance) / (2 * spread * spread),
            );
            const pullX = x * weight;
            const pullY = y * weight;
            const scaleX =
                1 + Math.abs(pullX) * 0.004 - Math.abs(pullY) * 0.0015;
            const scaleY =
                1 + Math.abs(pullY) * 0.005 - Math.abs(pullX) * 0.0012;
            const skew = clamp(-pullX * 0.06, -14, 14);

            // Huruf memanjang ke arah tarikan, menjauhi tetangganya, supaya tidak saling menimpa.
            // Titik pangkal berganti saat tarikan ~0 (skala ~1), jadi tidak ada loncatan.
            letter.style.transformOrigin = `${pullX >= 0 ? '0%' : '100%'} ${pullY <= 0 ? '100%' : '0%'}`;
            letter.style.transform =
                `translate3d(${pullX.toFixed(2)}px, ${pullY.toFixed(2)}px, 0) ` +
                `skewX(${skew.toFixed(2)}deg) ` +
                `scale(${scaleX.toFixed(3)}, ${scaleY.toFixed(3)})`;
        });
    };

    const stopSprings = () => {
        springX.current?.stop();
        springY.current?.stop();
    };

    const release = (velocityX: number, velocityY: number) => {
        stopSprings();

        springX.current = animateSpring({
            from: offset.current.x,
            to: 0,
            velocity: velocityX,
            config: springs.elastic,
            onUpdate: (value) => {
                offset.current.x = value;
                render();
            },
        });
        springY.current = animateSpring({
            from: offset.current.y,
            to: 0,
            velocity: velocityY,
            config: springs.elastic,
            onUpdate: (value) => {
                offset.current.y = value;
                render();
            },
        });
    };

    const fadePulse = () => {
        const container = containerRef.current;

        if (!container) {
            return;
        }

        container.style.transition = 'none';
        container.style.opacity = '0.45';
        requestAnimationFrame(() => {
            container.style.transition = `opacity ${FADE_DURATION * 2}ms ${FADE_EASING}`;
            container.style.opacity = '1';
        });
    };

    const pluck = () => {
        if (reducedMotion) {
            fadePulse();

            return;
        }

        grabIndex.current = (letters.length - 1) / 2;
        release(0, PLUCK_VELOCITY);
    };

    const nearestLetter = (clientX: number): number => {
        let nearest = 0;
        let best = Infinity;

        letterRefs.current.forEach((letter, index) => {
            if (!letter) {
                return;
            }

            const rect = letter.getBoundingClientRect();
            const distance = Math.abs(rect.left + rect.width / 2 - clientX);

            if (distance < best) {
                best = distance;
                nearest = index;
            }
        });

        return nearest;
    };

    const onPointerDown = (event: PointerEvent<HTMLSpanElement>) => {
        if (event.button !== 0) {
            return;
        }

        stopSprings();
        event.currentTarget.setPointerCapture(event.pointerId);

        // Ditangkap di tengah pantulan: lanjutkan dari posisi dan huruf yang sama
        // supaya tidak meloncat.
        const atRest =
            Math.abs(offset.current.x) < 1 && Math.abs(offset.current.y) < 1;

        if (reducedMotion || atRest) {
            offset.current = { x: 0, y: 0 };
            grabIndex.current = nearestLetter(event.clientX);
        }

        drag.current = {
            startX: event.clientX - offset.current.x,
            startY: event.clientY - offset.current.y,
            lastX: event.clientX,
            lastY: event.clientY,
            lastTime: event.timeStamp,
            velocityX: 0,
            velocityY: 0,
            moved: false,
        };
    };

    const onPointerMove = (event: PointerEvent<HTMLSpanElement>) => {
        const state = drag.current;

        if (!state) {
            return;
        }

        const dx = event.clientX - state.startX;
        const dy = event.clientY - state.startY;

        if (Math.hypot(dx, dy) > CLICK_THRESHOLD) {
            state.moved = true;
        }

        const elapsed = Math.max(event.timeStamp - state.lastTime, 1) / 1000;
        state.velocityX = (event.clientX - state.lastX) / elapsed;
        state.velocityY = (event.clientY - state.lastY) / elapsed;
        state.lastX = event.clientX;
        state.lastY = event.clientY;
        state.lastTime = event.timeStamp;

        if (reducedMotion) {
            return;
        }

        offset.current = {
            x: rubberband(dx, PULL_LIMIT_X),
            y: rubberband(dy, PULL_LIMIT_Y),
        };
        render();
    };

    const onPointerUp = () => {
        const state = drag.current;
        drag.current = null;

        if (!state) {
            return;
        }

        if (!state.moved) {
            pluck();

            return;
        }

        if (reducedMotion) {
            fadePulse();

            return;
        }

        // Kecepatan lepas ikut diredam hambatan karet supaya pantulan tetap wajar.
        release(
            clamp(state.velocityX * 0.25, -1200, 1200),
            clamp(state.velocityY * 0.25, -1200, 1200),
        );
    };

    const onKeyDown = (event: KeyboardEvent<HTMLSpanElement>) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            pluck();
        }
    };

    useEffect(() => {
        if (reducedMotion) {
            stopSprings();
            offset.current = { x: 0, y: 0 };
            render();
        }

        return stopSprings;
    }, [reducedMotion]);

    return (
        <span
            ref={containerRef}
            tabIndex={0}
            title="Tarik aku!"
            onPointerDown={onPointerDown}
            onPointerMove={onPointerMove}
            onPointerUp={onPointerUp}
            onPointerCancel={onPointerUp}
            onKeyDown={onKeyDown}
            className={cn(
                'inline-flex cursor-grab touch-none font-display leading-none outline-none select-none focus-visible:rounded-blob-sm focus-visible:ring-4 focus-visible:ring-lentur-blue/40 active:cursor-grabbing',
                className,
            )}
        >
            <span className="sr-only">{word}</span>
            {letters.map((letter, index) => (
                <span
                    key={index}
                    ref={(element) => {
                        letterRefs.current[index] = element;
                    }}
                    aria-hidden="true"
                    className="inline-block origin-center will-change-transform"
                >
                    {letter === ' ' ? ' ' : letter}
                </span>
            ))}
        </span>
    );
}
