import { useSyncExternalStore } from 'react';

/**
 * Gerak Lentur: elemen memanjang lalu memantul.
 *
 * Dua jalur, nilai yang sama:
 * - CSS: `ELASTIC_EASING` + `ELASTIC_DURATION` (juga tersedia sebagai token
 *   `--ease-elastic` / `--duration-elastic` dan utilitas `transition-elastic`).
 * - JS: `animateSpring` untuk interaksi langsung (tarik, usap, scroll),
 *   karena pegas fisik bisa dilanjutkan dari posisi dan kecepatan berapa pun.
 *
 * Selalu hormati prefers-reduced-motion: tanpa pantulan, cukup fade.
 */

export const ELASTIC_EASING = 'cubic-bezier(0.34, 1.56, 0.64, 1)';
export const ELASTIC_DURATION = 700;
export const FADE_EASING = 'ease-out';
export const FADE_DURATION = 200;

export type SpringConfig = {
    /** Kekakuan: makin tinggi makin cepat kembali. */
    stiffness: number;
    /** Redaman: makin rendah makin banyak memantul. */
    damping: number;
    mass: number;
};

export const springs = {
    /** Ciri khas Lentur: ~700 ms, satu-dua pantulan yang terasa riang. */
    elastic: { stiffness: 180, damping: 12, mass: 1 },
    /** Untuk elemen besar (ilustrasi tubuh): lebih kalem, tetap memantul. */
    gentle: { stiffness: 120, damping: 11, mass: 1.2 },
    /** Untuk umpan balik kecil (tombol, chip). */
    snappy: { stiffness: 320, damping: 16, mass: 0.8 },
} satisfies Record<string, SpringConfig>;

/* ------------------------------------------------------------------ */
/* prefers-reduced-motion                                             */
/* ------------------------------------------------------------------ */

const REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

export function prefersReducedMotion(): boolean {
    if (typeof window === 'undefined' || !window.matchMedia) {
        return false;
    }

    return window.matchMedia(REDUCED_MOTION_QUERY).matches;
}

function subscribeReducedMotion(onChange: () => void): () => void {
    if (typeof window === 'undefined' || !window.matchMedia) {
        return () => {};
    }

    const query = window.matchMedia(REDUCED_MOTION_QUERY);
    query.addEventListener('change', onChange);

    return () => query.removeEventListener('change', onChange);
}

/** Hook reaktif: ikut berubah kalau pengguna mengganti pengaturan OS. */
export function useReducedMotion(): boolean {
    return useSyncExternalStore(
        subscribeReducedMotion,
        prefersReducedMotion,
        () => false,
    );
}

/**
 * String `transition` CSS untuk properti tertentu.
 * Dengan reduced motion, transform tidak dianimasikan, hanya opacity (fade).
 */
export function elasticTransition(
    properties: string[] = ['transform'],
    reduced = prefersReducedMotion(),
): string {
    if (reduced) {
        return `opacity ${FADE_DURATION}ms ${FADE_EASING}`;
    }

    return properties
        .map(
            (property) => `${property} ${ELASTIC_DURATION}ms ${ELASTIC_EASING}`,
        )
        .join(', ');
}

/* ------------------------------------------------------------------ */
/* Pegas fisik                                                        */
/* ------------------------------------------------------------------ */

export type SpringAnimation = {
    stop: () => void;
};

type AnimateSpringOptions = {
    from: number;
    to: number;
    /** Kecepatan awal (unit/detik), mis. dari kecepatan lepas jari. */
    velocity?: number;
    config?: SpringConfig;
    onUpdate: (value: number, velocity: number) => void;
    onComplete?: () => void;
};

const REST_DELTA = 0.01;
const REST_SPEED = 0.05;
const MAX_STEP = 1 / 60;

/**
 * Menganimasikan satu nilai menuju `to` dengan pegas teredam
 * (integrasi semi-implisit Euler per frame).
 *
 * Dengan reduced motion, nilai langsung melompat ke tujuan, tanpa pantulan;
 * pemanggil cukup menampilkan fade bila perlu.
 */
export function animateSpring({
    from,
    to,
    velocity = 0,
    config = springs.elastic,
    onUpdate,
    onComplete,
}: AnimateSpringOptions): SpringAnimation {
    if (prefersReducedMotion() || typeof window === 'undefined') {
        onUpdate(to, 0);
        onComplete?.();

        return { stop: () => {} };
    }

    let position = from;
    let speed = velocity;
    let last = performance.now();
    let frame = 0;
    let stopped = false;

    const tick = (now: number) => {
        if (stopped) {
            return;
        }

        // Pecah selisih waktu besar (tab tidak aktif) jadi langkah kecil agar stabil.
        let remaining = Math.min((now - last) / 1000, 0.1);
        last = now;

        while (remaining > 0) {
            const dt = Math.min(remaining, MAX_STEP);
            const springForce = -config.stiffness * (position - to);
            const dampingForce = -config.damping * speed;
            speed += ((springForce + dampingForce) / config.mass) * dt;
            position += speed * dt;
            remaining -= dt;
        }

        const settled =
            Math.abs(speed) < REST_SPEED &&
            Math.abs(position - to) < REST_DELTA;

        if (settled) {
            onUpdate(to, 0);
            onComplete?.();

            return;
        }

        onUpdate(position, speed);
        frame = requestAnimationFrame(tick);
    };

    frame = requestAnimationFrame(tick);

    return {
        stop: () => {
            stopped = true;
            cancelAnimationFrame(frame);
        },
    };
}

/**
 * Hambatan karet: makin jauh ditarik, makin berat.
 * Hasil tidak pernah melebihi `limit`.
 */
export function rubberband(distance: number, limit: number): number {
    const sign = Math.sign(distance);
    const magnitude = Math.abs(distance);

    return sign * ((magnitude * limit) / (magnitude + limit));
}

export function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}
