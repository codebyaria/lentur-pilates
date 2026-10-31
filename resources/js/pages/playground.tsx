import { Head } from '@inertiajs/react';
import { useState } from 'react';
import type { ReactNode } from 'react';
import { BodyFigures } from '@/components/lentur/body-figures';
import { ElasticWord } from '@/components/lentur/elastic-word';
import {
    ELASTIC_DURATION,
    ELASTIC_EASING,
    useReducedMotion,
} from '@/lib/motion';

const SWATCHES = [
    {
        name: 'Putih hangat',
        hex: '#FFF8EE',
        className: 'bg-lentur-cream text-lentur-ink ring-1 ring-lentur-ink/10',
        note: 'Dasar',
    },
    {
        name: 'Tinta biru',
        hex: '#1F2A44',
        className: 'bg-lentur-ink text-lentur-lime',
        note: 'Teks',
    },
    {
        name: 'Limau',
        hex: '#C8F04B',
        className: 'bg-lentur-lime text-lentur-ink',
        note: 'Teks tinta di atasnya',
    },
    {
        name: 'Merah muda',
        hex: '#FF9EC4',
        className: 'bg-lentur-pink text-lentur-ink',
        note: 'Teks tinta di atasnya',
    },
    {
        name: 'Biru',
        hex: '#2F5BE8',
        className: 'bg-lentur-blue text-white',
        note: 'Tombol utama · satu-satunya dasar teks putih',
    },
];

const LEVELS = ['Baru Kenal', 'Mulai Akrab', 'Sudah Lengket'];

export default function Playground() {
    const reducedMotion = useReducedMotion();
    const [manualStretch, setManualStretch] = useState<number | null>(null);
    const [bounced, setBounced] = useState(false);

    return (
        <>
            <Head title="Playground" />
            <main className="min-h-screen bg-lentur-cream font-sans text-lentur-ink">
                <div className="mx-auto max-w-5xl px-4 py-10 sm:px-8 sm:py-16">
                    <header className="mb-12 flex flex-wrap items-center justify-between gap-4">
                        <p className="font-display text-2xl">
                            Lentur · playground
                        </p>
                        <span
                            className={
                                reducedMotion
                                    ? 'rounded-full bg-lentur-pink px-4 py-1.5 text-sm font-semibold'
                                    : 'rounded-full bg-lentur-lime px-4 py-1.5 text-sm font-semibold'
                            }
                        >
                            {reducedMotion
                                ? 'Gerak dikurangi: cukup fade'
                                : 'Gerak penuh: pegas elastis'}
                        </span>
                    </header>

                    {/* ElasticWord */}
                    <section className="mb-16 rounded-blob-lg bg-white p-6 sm:p-12">
                        <Label>ElasticWord</Label>
                        <h1 className="my-8 text-center">
                            <ElasticWord
                                word="lentur"
                                className="text-[clamp(5rem,20vw,13rem)] text-lentur-blue"
                            />
                        </h1>
                        <p className="text-center text-lg">
                            Tarik, usap, atau klik katanya. Lepas, lalu lihat
                            dia pulang sendiri.
                        </p>
                        <p className="mt-2 text-center text-sm text-lentur-ink/70">
                            Bisa juga lewat keyboard: Tab ke kata, lalu Enter.
                        </p>
                        <div className="mt-10 grid gap-6 sm:grid-cols-2">
                            <div className="rounded-blob bg-lentur-lime p-6 text-center">
                                <ElasticWord
                                    word="Gerak dulu."
                                    className="text-3xl sm:text-5xl"
                                />
                            </div>
                            <div className="rounded-blob bg-lentur-pink p-6 text-center">
                                <ElasticWord
                                    word="Sempurna belakangan."
                                    className="text-2xl sm:text-4xl"
                                />
                            </div>
                        </div>
                    </section>

                    {/* BodyFigures */}
                    <section className="mb-16 rounded-blob-lg bg-lentur-lime/40 p-6 sm:p-12">
                        <Label>BodyFigures</Label>
                        <p className="mt-4 max-w-xl text-lg">
                            Scroll halaman, mereka ikut meregang lalu memantul.
                            Arahkan kursor ke salah satu untuk menyapa.
                        </p>
                        <div className="my-8">
                            <BodyFigures stretch={manualStretch ?? undefined} />
                        </div>
                        <div className="flex flex-wrap items-center gap-4">
                            <label className="flex flex-1 items-center gap-3 text-sm font-semibold">
                                Regangan manual
                                <input
                                    type="range"
                                    min={0}
                                    max={1}
                                    step={0.01}
                                    value={manualStretch ?? 0}
                                    onChange={(event) =>
                                        setManualStretch(
                                            Number(event.target.value),
                                        )
                                    }
                                    className="w-full max-w-xs accent-lentur-blue"
                                />
                            </label>
                            <button
                                type="button"
                                onClick={() => setManualStretch(null)}
                                className="rounded-full bg-white px-5 py-2.5 text-sm font-semibold ring-1 ring-lentur-ink/15 transition-elastic hover:scale-105"
                            >
                                Kembali ke mode scroll
                            </button>
                        </div>
                    </section>

                    {/* Tokens */}
                    <section className="mb-16">
                        <Label>Warna</Label>
                        <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                            {SWATCHES.map((swatch) => (
                                <div
                                    key={swatch.hex}
                                    className={`flex min-h-36 flex-col justify-between rounded-blob-sm p-5 ${swatch.className}`}
                                >
                                    <span className="font-display text-xl">
                                        {swatch.name}
                                    </span>
                                    <span className="text-sm">
                                        {swatch.hex}
                                        <br />
                                        {swatch.note}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </section>

                    <section className="mb-16 grid gap-6 lg:grid-cols-2">
                        <div className="rounded-blob bg-white p-6 sm:p-8">
                            <Label>Tipografi</Label>
                            <p className="mt-6 font-display text-5xl leading-tight">
                                Gerak dulu. Sempurna belakangan.
                            </p>
                            <p className="mt-2 text-sm text-lentur-ink/70">
                                Bricolage Grotesque 800 · judul saja
                            </p>
                            <p className="mt-6 text-lg">
                                Kelas ini sudah ramai. Jam berikutnya masih
                                lega.
                            </p>
                            <p className="mt-2 text-sm text-lentur-ink/70">
                                Figtree · body, tombol, form
                            </p>
                        </div>
                        <div className="rounded-blob bg-white p-6 sm:p-8">
                            <Label>Chip, tombol & gerak</Label>
                            <div className="mt-6 flex flex-wrap gap-2">
                                {LEVELS.map((level, index) => (
                                    <span
                                        key={level}
                                        className={[
                                            'rounded-full px-4 py-1.5 text-sm font-semibold',
                                            index === 0 && 'bg-lentur-lime',
                                            index === 1 && 'bg-lentur-pink',
                                            index === 2 &&
                                                'bg-lentur-blue text-white',
                                        ]
                                            .filter(Boolean)
                                            .join(' ')}
                                    >
                                        {level}
                                    </span>
                                ))}
                            </div>
                            <div className="mt-6 flex flex-wrap items-center gap-4">
                                <button
                                    type="button"
                                    onClick={() =>
                                        setBounced((value) => !value)
                                    }
                                    className="rounded-full bg-lentur-blue px-6 py-3 font-semibold text-white transition-elastic hover:scale-105 active:scale-95"
                                >
                                    Pesan kelas
                                </button>
                                <span
                                    aria-hidden="true"
                                    className="inline-block h-12 w-12 rounded-full bg-lentur-pink transition-elastic"
                                    style={{
                                        transform: bounced
                                            ? 'translateX(72px) scaleX(1.4)'
                                            : 'none',
                                        opacity:
                                            reducedMotion && bounced ? 0.4 : 1,
                                    }}
                                />
                            </div>
                            <p className="mt-6 font-mono text-xs text-lentur-ink/70">
                                {ELASTIC_EASING} · {ELASTIC_DURATION} ms
                            </p>
                        </div>
                    </section>

                    {/* Ruang scroll untuk mencoba regangan BodyFigures */}
                    <section className="rounded-blob-lg bg-lentur-blue p-8 text-white sm:p-12">
                        <p className="font-display text-3xl sm:text-4xl">
                            Scroll naik-turun di sini, lalu lihat lagi
                            tubuh-tubuh di atas.
                        </p>
                        <div className="h-[60vh]" />
                        <p className="text-lg">
                            Sampai ketemu di kelas. Bawa air minum, tinggalkan
                            rasa sungkan.
                        </p>
                    </section>
                </div>
            </main>
        </>
    );
}

function Label({ children }: { children: ReactNode }) {
    return (
        <span className="inline-block rounded-full bg-lentur-ink px-3 py-1 text-xs font-semibold tracking-wide text-lentur-lime uppercase">
            {children}
        </span>
    );
}
