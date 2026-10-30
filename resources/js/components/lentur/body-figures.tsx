import { useEffect, useRef } from 'react';
import { animateSpring, clamp, springs, useReducedMotion } from '@/lib/motion';
import type { SpringAnimation } from '@/lib/motion';
import { cn } from '@/lib/utils';

const INK = '#1F2A44';
const LIME = '#C8F04B';
const PINK = '#FF9EC4';
const BLUE = '#2F5BE8';
const CREAM = '#FFF8EE';

type Hair = 'none' | 'short' | 'bun' | 'curly';
type Pose = 'stand' | 'wheelchair';

type Figure = {
    label: string;
    x: number;
    pose: Pose;
    bodyW: number;
    bodyH: number;
    headR: number;
    legH: number;
    /** Sudut lengan dari posisi menggantung (derajat), kiri dan kanan. */
    arms: [number, number];
    armL: number;
    body: string;
    skin: string;
    hair: Hair;
    /** Seberapa responsif tubuh ini terhadap regangan (0.5–1.3). */
    give: number;
};

/** Kepala bulat + badan pil, berbagai ukuran: semua bentuk tubuh diundang. */
const FIGURES: Figure[] = [
    {
        label: 'Tinggi ramping, meraih ke atas',
        x: 70,
        pose: 'stand',
        bodyW: 36,
        bodyH: 92,
        headR: 19,
        legH: 74,
        arms: [168, 168],
        armL: 64,
        body: BLUE,
        skin: '#8D5A3B',
        hair: 'short',
        give: 1.1,
    },
    {
        label: 'Berbadan lebar, merentangkan tangan',
        x: 200,
        pose: 'stand',
        bodyW: 84,
        bodyH: 96,
        headR: 22,
        legH: 52,
        arms: [100, 100],
        armL: 56,
        body: PINK,
        skin: '#F1C7A1',
        hair: 'bun',
        give: 0.8,
    },
    {
        label: 'Mungil, melambai',
        x: 318,
        pose: 'stand',
        bodyW: 34,
        bodyH: 56,
        headR: 17,
        legH: 40,
        arms: [20, 150],
        armL: 40,
        body: LIME,
        skin: '#C68A5E',
        hair: 'curly',
        give: 1.3,
    },
    {
        label: 'Pengguna kursi roda, tangan di roda',
        x: 438,
        pose: 'wheelchair',
        bodyW: 48,
        bodyH: 72,
        headR: 20,
        legH: 40,
        arms: [26, 26],
        armL: 46,
        body: PINK,
        skin: '#5B3A29',
        hair: 'short',
        give: 0.7,
    },
    {
        label: 'Tinggi besar, santai',
        x: 560,
        pose: 'stand',
        bodyW: 70,
        bodyH: 118,
        headR: 23,
        legH: 66,
        arms: [14, 14],
        armL: 70,
        body: INK,
        skin: '#E3A47A',
        hair: 'none',
        give: 0.9,
    },
    {
        label: 'Sedang, satu tangan ke atas',
        x: 668,
        pose: 'stand',
        bodyW: 46,
        bodyH: 80,
        headR: 19,
        legH: 62,
        arms: [176, 10],
        armL: 58,
        body: LIME,
        skin: '#A86B45',
        hair: 'bun',
        give: 1,
    },
];

const VIEW_W = 740;
const VIEW_H = 340;
const FLOOR_Y = 320;
const LIMB_W = 16;
/** Regangan maksimum dari scroll (sy = 1 + 0.16). */
const MAX_SCROLL_STRETCH = 0.16;

type BodyFiguresProps = {
    className?: string;
    /** Regang sedikit mengikuti kecepatan scroll, lalu memantul. */
    stretchOnScroll?: boolean;
    /** Regangan manual (0–1). Bila diisi, regangan scroll diabaikan. */
    stretch?: number;
};

export function BodyFigures({
    className,
    stretchOnScroll = true,
    stretch,
}: BodyFiguresProps) {
    const reducedMotion = useReducedMotion();
    const figureRefs = useRef<(SVGGElement | null)[]>([]);
    const scrollStretch = useRef(0);
    const hoverStretch = useRef<number[]>(FIGURES.map(() => 0));
    const scrollSpring = useRef<SpringAnimation | null>(null);
    const hoverSprings = useRef<(SpringAnimation | null)[]>([]);
    const controlled = stretch !== undefined;

    const render = () => {
        const base = controlled
            ? clamp(stretch, 0, 1) * 0.35
            : scrollStretch.current;

        figureRefs.current.forEach((group, index) => {
            if (!group) {
                return;
            }

            const figure = FIGURES[index];
            const amount = base * figure.give + hoverStretch.current[index];
            const scaleY = Math.max(0.7, 1 + amount);
            // Volume terjaga: makin tinggi, makin ramping.
            const scaleX = 1 / Math.sqrt(scaleY);

            group.setAttribute(
                'transform',
                `translate(${figure.x} ${FLOOR_Y}) scale(${scaleX.toFixed(4)} ${scaleY.toFixed(4)})`,
            );
        });
    };

    useEffect(() => {
        render();
    });

    useEffect(() => {
        if (!stretchOnScroll || controlled || reducedMotion) {
            return;
        }

        let lastY = window.scrollY;
        let lastTime = performance.now();

        const onScroll = () => {
            const now = performance.now();
            const elapsed = Math.max(now - lastTime, 8) / 1000;
            const speed = Math.abs(window.scrollY - lastY) / elapsed;
            lastY = window.scrollY;
            lastTime = now;

            const target = clamp(speed / 4000, 0, 1) * MAX_SCROLL_STRETCH;

            if (target <= scrollStretch.current) {
                return;
            }

            // Meregang mengikuti scroll, lalu pegas menariknya kembali (dan memantul).
            scrollSpring.current?.stop();
            scrollStretch.current = target;
            render();
            scrollSpring.current = animateSpring({
                from: target,
                to: 0,
                config: springs.gentle,
                onUpdate: (value) => {
                    scrollStretch.current = value;
                    render();
                },
            });
        };

        window.addEventListener('scroll', onScroll, { passive: true });

        return () => {
            window.removeEventListener('scroll', onScroll);
            scrollSpring.current?.stop();
            scrollStretch.current = 0;
        };
    }, [stretchOnScroll, controlled, reducedMotion]);

    useEffect(() => {
        const springsAtMount = hoverSprings.current;

        return () => springsAtMount.forEach((spring) => spring?.stop());
    }, []);

    const boing = (index: number) => {
        if (reducedMotion) {
            return;
        }

        hoverSprings.current[index]?.stop();
        hoverSprings.current[index] = animateSpring({
            from: hoverStretch.current[index],
            to: 0,
            velocity: 1.4,
            config: springs.elastic,
            onUpdate: (value) => {
                hoverStretch.current[index] = value;
                render();
            },
        });
    };

    return (
        <svg
            viewBox={`0 0 ${VIEW_W} ${VIEW_H}`}
            role="img"
            aria-label="Enam orang dengan bentuk tubuh berbeda sedang bergerak bersama"
            className={cn('h-auto w-full overflow-visible', className)}
        >
            <rect
                x={20}
                y={FLOOR_Y - 2}
                width={VIEW_W - 40}
                height={14}
                rx={7}
                fill={INK}
                opacity={0.08}
            />
            {FIGURES.map((figure, index) => (
                <g
                    key={figure.label}
                    ref={(element) => {
                        figureRefs.current[index] = element;
                    }}
                    transform={`translate(${figure.x} ${FLOOR_Y})`}
                    onPointerEnter={() => boing(index)}
                    className="cursor-pointer"
                >
                    <title>{figure.label}</title>
                    {figure.pose === 'wheelchair' ? (
                        <WheelchairFigure figure={figure} />
                    ) : (
                        <StandingFigure figure={figure} />
                    )}
                </g>
            ))}
        </svg>
    );
}

function Pill({
    x,
    y,
    width,
    height,
    fill,
    rotate,
    originX = 0,
    originY = 0,
}: {
    x: number;
    y: number;
    width: number;
    height: number;
    fill: string;
    rotate?: number;
    originX?: number;
    originY?: number;
}) {
    return (
        <rect
            x={x}
            y={y}
            width={width}
            height={height}
            rx={Math.min(width, height) / 2}
            fill={fill}
            transform={
                rotate ? `rotate(${rotate} ${originX} ${originY})` : undefined
            }
        />
    );
}

/** Lengan sebagai pil yang berputar di bahu; 0° = menggantung. */
function Arm({
    shoulderX,
    shoulderY,
    angle,
    length,
    fill,
}: {
    shoulderX: number;
    shoulderY: number;
    angle: number;
    length: number;
    fill: string;
}) {
    return (
        <Pill
            x={shoulderX - LIMB_W / 2}
            y={shoulderY - LIMB_W / 2}
            width={LIMB_W}
            height={length}
            fill={fill}
            rotate={angle}
            originX={shoulderX}
            originY={shoulderY}
        />
    );
}

function Head({ cy, figure }: { cy: number; figure: Figure }) {
    const { headR: r, skin, hair } = figure;

    return (
        <g>
            {hair === 'bun' && (
                <circle cx={0} cy={cy - r - 4} r={r * 0.45} fill={INK} />
            )}
            {hair === 'curly' &&
                [-0.7, -0.25, 0.25, 0.7].map((offset) => (
                    <circle
                        key={offset}
                        cx={offset * r}
                        cy={cy - r * 0.75}
                        r={r * 0.42}
                        fill={INK}
                    />
                ))}
            <circle cx={0} cy={cy} r={r} fill={skin} />
            {(hair === 'short' || hair === 'bun') && (
                <path
                    d={`M ${-r} ${cy} A ${r} ${r} 0 0 1 ${r} ${cy} Q ${r * 0.2} ${cy - r * 0.45} ${-r} ${cy} Z`}
                    fill={INK}
                />
            )}
            {/* Senyum kecil: riang, bukan menuntut. */}
            <path
                d={`M ${-r * 0.32} ${cy + r * 0.28} Q 0 ${cy + r * 0.55} ${r * 0.32} ${cy + r * 0.28}`}
                stroke={INK}
                strokeWidth={2.5}
                strokeLinecap="round"
                fill="none"
            />
        </g>
    );
}

function StandingFigure({ figure }: { figure: Figure }) {
    const { bodyW, bodyH, legH, headR, arms, armL, body } = figure;
    const bodyTop = -(legH + bodyH) + 10;
    const shoulderY = bodyTop + LIMB_W;
    const shoulderX = bodyW / 2 - LIMB_W / 3;
    const legX = Math.max(bodyW / 4, LIMB_W / 2 + 2);

    return (
        <>
            <Pill
                x={-legX - LIMB_W / 2}
                y={-legH}
                width={LIMB_W}
                height={legH}
                fill={INK}
            />
            <Pill
                x={legX - LIMB_W / 2}
                y={-legH}
                width={LIMB_W}
                height={legH}
                fill={INK}
            />
            <Arm
                shoulderX={-shoulderX}
                shoulderY={shoulderY}
                angle={arms[0]}
                length={armL}
                fill={body}
            />
            <Arm
                shoulderX={shoulderX}
                shoulderY={shoulderY}
                angle={-arms[1]}
                length={armL}
                fill={body}
            />
            <Pill
                x={-bodyW / 2}
                y={bodyTop}
                width={bodyW}
                height={bodyH}
                fill={body}
            />
            <Head cy={bodyTop - headR - 4} figure={figure} />
        </>
    );
}

function WheelchairFigure({ figure }: { figure: Figure }) {
    const { bodyW, bodyH, headR, arms, armL, body, legH } = figure;
    const wheelR = 40;
    const seatY = -wheelR - 12;
    const bodyTop = seatY - bodyH + 8;
    const shoulderY = bodyTop + LIMB_W;

    return (
        <>
            {/* Kaki: paha ke depan, betis turun ke pijakan. */}
            <Pill
                x={0}
                y={seatY - LIMB_W}
                width={legH + 10}
                height={LIMB_W}
                fill={INK}
            />
            <Pill
                x={legH - 6}
                y={seatY - LIMB_W}
                width={LIMB_W}
                height={legH + 8}
                fill={INK}
            />
            <Pill
                x={legH - 10}
                y={seatY + legH - 10}
                width={28}
                height={10}
                fill={BLUE}
            />
            {/* Sandaran dan dudukan. */}
            <Pill
                x={-bodyW / 2 - 12}
                y={bodyTop - 4}
                width={10}
                height={-bodyTop + seatY + 14}
                fill={BLUE}
            />
            <Pill
                x={-bodyW / 2 - 12}
                y={seatY - 4}
                width={bodyW + legH}
                height={10}
                fill={BLUE}
            />
            <Pill
                x={-bodyW / 2}
                y={bodyTop}
                width={bodyW}
                height={bodyH}
                fill={body}
            />
            <Arm
                shoulderX={bodyW / 2 - LIMB_W / 3}
                shoulderY={shoulderY}
                angle={-arms[1]}
                length={armL}
                fill={body}
            />
            {/* Roda besar di depan badan. */}
            <circle
                cx={-4}
                cy={-wheelR}
                r={wheelR - 4}
                fill="none"
                stroke={BLUE}
                strokeWidth={8}
            />
            <circle
                cx={-4}
                cy={-wheelR}
                r={wheelR - 14}
                fill="none"
                stroke={BLUE}
                strokeWidth={2}
                opacity={0.5}
            />
            <circle
                cx={-4}
                cy={-wheelR}
                r={6}
                fill={CREAM}
                stroke={BLUE}
                strokeWidth={4}
            />
            <Arm
                shoulderX={-(bodyW / 2 - LIMB_W / 3)}
                shoulderY={shoulderY}
                angle={arms[0]}
                length={armL}
                fill={body}
            />
            <Head cy={bodyTop - headR - 4} figure={figure} />
        </>
    );
}
