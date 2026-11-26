/* Arka Mahendra — portfolio interactions (vanilla, no build step) */

const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
const lerp = (a, b, t) => a + (b - a) * t;
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
const isMobile = () => innerWidth <= 860;

/* ---------- Smooth scroll (Lenis, opsional) ---------- */
let lenis = null;
async function initLenis() {
    if (reduce) return;
    try {
        const { default: Lenis } = await import('lenis');
        lenis = new Lenis({ duration: 1.15, smoothWheel: true });
        const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
        requestAnimationFrame(raf);
    } catch (e) { /* native scroll fallback */ }
}

function scrollToId(id) {
    const el = id === 'top' ? document.body : document.getElementById(id);
    if (!el) return;
    if (lenis) lenis.scrollTo(id === 'top' ? 0 : el, { offset: 0, duration: 1.6 });
    else (id === 'top' ? scrollTo({ top: 0, behavior: 'smooth' }) : el.scrollIntoView({ behavior: 'smooth' }));
}

/* ---------- Loader ---------- */
function runLoader() {
    const loader = $('.loader');
    const count = $('[data-count]');
    document.body.classList.add('is-loading');
    return new Promise((resolve) => {
        const start = performance.now();
        const dur = reduce ? 200 : 1600;
        let fontsReady = false;
        document.fonts?.ready.then(() => (fontsReady = true));
        const tick = (now) => {
            let t = clamp((now - start) / dur);
            if (!fontsReady && t > .9) t = .9;
            const eased = 1 - Math.pow(1 - t, 3);
            const v = Math.round(eased * 100);
            count.textContent = v;
            loader.style.setProperty('--p', v);
            if (t < 1) requestAnimationFrame(tick);
            else {
                setTimeout(() => {
                    loader.classList.add('is-done');
                    document.body.classList.remove('is-loading');
                    document.documentElement.classList.add('is-ready');
                    resolve();
                }, 200);
            }
        };
        requestAnimationFrame(tick);
    });
}

/* ---------- Rotating word ---------- */
function initRotator() {
    const el = $('[data-rotator]');
    if (!el) return;
    const words = JSON.parse(el.dataset.words);
    let i = 0;
    const render = (word, cls) => {
        el.innerHTML = [...word].map((ch, k) => `<span class="ch ${cls || ''}" style="transition-delay:${k * 25}ms">${ch === ' ' ? '&nbsp;' : ch}</span>`).join('');
    };
    render(words[0]);
    if (reduce) return;
    setInterval(() => {
        $$('.ch', el).forEach((c) => c.classList.add('out'));
        setTimeout(() => {
            i = (i + 1) % words.length;
            render(words[i], 'in');
            requestAnimationFrame(() => requestAnimationFrame(() => $$('.ch', el).forEach((c) => c.classList.remove('in'))));
        }, 450);
    }, 2800);
}

/* ---------- Nav: active link, indicator, theme, hide on scroll ---------- */
function initNav() {
    const nav = $('[data-nav]');
    const menu = $('.nav__menu');
    const ind = $('.nav__indicator');
    const links = $$('[data-link]');
    const cursor = $('.cursor');

    const moveIndicator = (a) => {
        if (!a) { menu.style.setProperty('--o', 0); return; }
        menu.style.setProperty('--x', a.offsetLeft + 'px');
        menu.style.setProperty('--w', a.offsetWidth + 'px');
        menu.style.setProperty('--o', 1);
    };

    // Section yang ada di bawah nav menentukan tema & link aktif
    const themed = $$('[data-theme]');
    const sections = links.map((a) => document.getElementById(a.dataset.link)).filter(Boolean);
    let lastY = scrollY;

    const update = () => {
        const probe = 40;
        let theme = 'dark';
        for (const s of themed) {
            const r = s.getBoundingClientRect();
            if (r.top <= probe && r.bottom > probe) { theme = s.dataset.theme; }
        }
        // Hero yang sudah mengecil: area di belakang nav adalah poster (terang)
        const frame = $('[data-frame]');
        if (frame && !isMobile()) {
            const p = parseFloat(getComputedStyle($('[data-hero]')).getPropertyValue('--p')) || 0;
            const r = frame.getBoundingClientRect();
            if (p > .25 && r.bottom > probe) theme = 'poster';
        }
        nav.classList.toggle('on-light', theme === 'light' || theme === 'poster');

        const mid = innerHeight * .4;
        let active = null;
        sections.forEach((s, k) => {
            const r = s.getBoundingClientRect();
            if (r.top <= mid && r.bottom > mid) active = links[k];
        });
        links.forEach((a) => a.classList.toggle('is-active', a === active));
        moveIndicator(active);

        const y = scrollY;
        nav.classList.toggle('is-hidden', y > lastY && y > innerHeight * 1.2 && !nav.classList.contains('menu-open'));
        lastY = y;

        // Cursor follows section theme too
        if (cursor) {
            const el = document.elementFromPoint(cursorPos.x, cursorPos.y);
            const sec = el?.closest('[data-theme]');
            cursor.classList.toggle('on-light', sec?.dataset.theme === 'light');
        }
    };
    addEventListener('scroll', update, { passive: true });
    addEventListener('resize', update);
    update();

    // Anchor clicks
    $$('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href').slice(1);
            if (!id) return;
            e.preventDefault();
            closeMenu();
            scrollToId(id);
        });
    });

    // Mobile menu
    const burger = $('[data-burger]');
    const mm = $('[data-mmenu]');
    function closeMenu() {
        if (!mm.classList.contains('is-open')) return;
        mm.classList.remove('is-open');
        nav.classList.remove('menu-open');
        burger.setAttribute('aria-expanded', 'false');
        lenis?.start();
        setTimeout(() => { if (!mm.classList.contains('is-open')) mm.hidden = true; }, 800);
    }
    burger.addEventListener('click', () => {
        if (mm.classList.contains('is-open')) return closeMenu();
        mm.hidden = false;
        requestAnimationFrame(() => mm.classList.add('is-open'));
        nav.classList.add('menu-open');
        burger.setAttribute('aria-expanded', 'true');
        lenis?.stop();
    });
    addEventListener('keydown', (e) => e.key === 'Escape' && closeMenu());
}

/* ---------- Clock ---------- */
function initClock() {
    const els = $$('[data-clock]');
    const fmt = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' });
    const tick = () => els.forEach((el) => (el.textContent = fmt.format(new Date())));
    tick();
    setInterval(tick, 15000);
}

/* ---------- Reveal on view ---------- */
function initReveal() {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((en) => {
            if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
        });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.05 });
    $$('.reveal, .reveal-words, [data-case]').forEach((el) => io.observe(el));

    // Counters
    const cio = new IntersectionObserver((entries) => {
        entries.forEach((en) => {
            if (!en.isIntersecting) return;
            cio.unobserve(en.target);
            const el = en.target;
            const to = +el.dataset.countTo;
            const t0 = performance.now();
            const dur = reduce ? 1 : 1800;
            const step = (now) => {
                const t = clamp((now - t0) / dur);
                el.textContent = Math.round(to * (1 - Math.pow(1 - t, 4)));
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        });
    }, { threshold: .6 });
    $$('[data-count-to]').forEach((el) => cio.observe(el));

    // stagger bars in orbit chart
    $$('.ob__bars span').forEach((s, k) => s.style.setProperty('--n', k));
}

/* ---------- Scroll-linked scenes ---------- */
function initScrollScenes() {
    const frame = $('[data-frame]');
    const hero = $('[data-hero]');
    const poster = $('.frame__sticky');
    const cases = $$('[data-case]');
    const fill = $('[data-fill]');
    const fillWords = fill ? $$('span', fill) : [];
    const proc = $('[data-process]');
    const track = $('[data-track]');
    const bar = $('[data-progress]');
    const steps = $$('.step');
    const arc = $('.contact__arc span');
    const contact = $('#contact');

    const update = () => {
        const vh = innerHeight;
        const mobile = isMobile();

        // 1. Hero → monitor screen
        if (frame && !mobile) {
            const r = frame.getBoundingClientRect();
            const total = frame.offsetHeight - vh;
            const raw = clamp(-r.top / total);
            const p = reduce ? (raw > .5 ? 1 : 0) : easeInOut(clamp(raw / .8));
            hero.style.setProperty('--p', p.toFixed(4));
            const topGap = Math.max(vh * .3, 250);
            hero.style.setProperty('--ty', topGap + 'px');
            const scale = clamp((vh * .84 - topGap) / vh, .45, .7);
            hero.style.setProperty('--sc', (1 - scale).toFixed(4));
            poster.style.setProperty('--po', clamp((p - .45) / .4).toFixed(3));
            poster.style.setProperty('--so', clamp((p - .6) / .35).toFixed(3));
            const stand = $('.frame__stand');
            stand.style.top = (topGap + vh * scale - 2) + 'px';
            stand.style.height = (vh - topGap - vh * scale + 4) + 'px';
            stand.style.width = (innerWidth * scale * .34) + 'px';
            window.__heroProgress = p;
        }

        // 2. Stacked cases: kartu sebelumnya mengecil & meredup
        if (!mobile) {
            cases.forEach((c, i) => {
                const next = cases[i + 1];
                const inner = c.firstElementChild;
                if (!next) { inner.style.setProperty('--s', 1); inner.style.setProperty('--b', 1); return; }
                const nr = next.getBoundingClientRect();
                const cr = c.getBoundingClientRect();
                const t = clamp(1 - (nr.top - cr.top) / (vh * .9));
                inner.style.setProperty('--s', (1 - t * .06).toFixed(4));
                inner.style.setProperty('--b', (1 - t * .45).toFixed(3));
            });
        }

        // 3. About statement fills word by word
        if (fill) {
            const r = fill.getBoundingClientRect();
            const t = clamp((vh * .85 - r.top) / (r.height + vh * .35));
            const n = Math.floor(t * fillWords.length * 1.05);
            fillWords.forEach((w, k) => w.classList.toggle('on', k < n));
        }

        // 4. Process horizontal scroll
        if (proc && !mobile) {
            const r = proc.getBoundingClientRect();
            const total = proc.offsetHeight - vh;
            const t = clamp(-r.top / total);
            const dist = track.scrollWidth - innerWidth;
            track.style.setProperty('--tx', (-dist * t).toFixed(1) + 'px');
            bar.parentElement.style.setProperty('--pp', t.toFixed(4));
            bar.style.setProperty('--pp', t.toFixed(4));
            const idx = Math.min(steps.length - 1, Math.floor(t * steps.length * 1.05));
            steps.forEach((s, k) => s.classList.toggle('is-active', k === idx && t > .05));
        }

        // 5. Contact arc rotates slowly with scroll
        if (arc && contact) {
            const r = contact.getBoundingClientRect();
            const t = clamp((vh - r.top) / (vh + r.height));
            arc.style.setProperty('--ar', ((t - .5) * 50).toFixed(2) + 'deg');
        }
    };

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => { update(); ticking = false; });
    };
    addEventListener('scroll', onScroll, { passive: true });
    addEventListener('resize', onScroll);
    update();
}
const easeInOut = (t) => (t < .5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2);

/* ---------- Services accordion ---------- */
function initServices() {
    const rows = $$('.svc__row');
    rows.forEach((row) => {
        row.addEventListener('click', () => {
            const open = row.getAttribute('aria-expanded') === 'true';
            rows.forEach((r) => r.setAttribute('aria-expanded', 'false'));
            row.setAttribute('aria-expanded', open ? 'false' : 'true');
        });
    });
}

/* ---------- Cursor, magnetic buttons, tilt ---------- */
const cursorPos = { x: -100, y: -100 };
function initPointer() {
    if (!fine || reduce) return;
    document.documentElement.classList.add('has-cursor');
    const cur = $('.cursor');
    const label = $('.cursor__label');
    const ring = { x: -100, y: -100 };
    addEventListener('pointermove', (e) => { cursorPos.x = e.clientX; cursorPos.y = e.clientY; }, { passive: true });
    const loop = () => {
        ring.x = lerp(ring.x, cursorPos.x, .18);
        ring.y = lerp(ring.y, cursorPos.y, .18);
        cur.style.setProperty('--dx', cursorPos.x + 'px');
        cur.style.setProperty('--dy', cursorPos.y + 'px');
        cur.style.setProperty('--rx', ring.x + 'px');
        cur.style.setProperty('--ry', ring.y + 'px');
        requestAnimationFrame(loop);
    };
    loop();

    document.addEventListener('pointerover', (e) => {
        const lab = e.target.closest('[data-cursor]');
        const hov = e.target.closest('a, button, label, input, textarea');
        cur.classList.toggle('is-label', !!lab);
        label.textContent = lab ? lab.dataset.cursor : '';
        cur.classList.toggle('is-hover', !lab && !!hov);
    });
    document.addEventListener('pointerleave', () => { cursorPos.x = cursorPos.y = -100; });

    // Magnetic
    $$('[data-magnetic]').forEach((el) => {
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const x = e.clientX - r.left - r.width / 2;
            const y = e.clientY - r.top - r.height / 2;
            el.style.transform = `translate(${x * .22}px, ${y * .3}px)`;
        });
        el.addEventListener('pointerleave', () => {
            el.style.transition = 'transform .7s cubic-bezier(.22,1,.36,1)';
            el.style.transform = '';
            setTimeout(() => (el.style.transition = ''), 700);
        });
    });

    // Tilt: mock layers move in depth, ID card rotates
    $$('[data-tilt]').forEach((el) => {
        const layers = el.classList.contains('idcard') ? [el] : $$('.mock > *', el);
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - .5;
            const y = (e.clientY - r.top) / r.height - .5;
            if (el.classList.contains('idcard')) {
                el.style.transform = `perspective(900px) rotateY(${x * 14}deg) rotateX(${-y * 14}deg)`;
                el.style.setProperty('--sx', (50 + x * 120) + '%');
                return;
            }
            layers.forEach((l, k) => {
                const d = (k + 1) * 6;
                l.style.translate = `${x * d}px ${y * d}px`;
            });
        });
        el.addEventListener('pointerleave', () => {
            el.style.transform = '';
            layers.forEach((l) => (l.style.translate = ''));
        });
    });
}

/* ---------- Copy email ---------- */
function initCopy() {
    $$('[data-copy]').forEach((b) => {
        b.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(b.dataset.copy);
                b.classList.add('is-done');
                b.firstElementChild.textContent = 'Copied';
                setTimeout(() => { b.classList.remove('is-done'); b.firstElementChild.textContent = 'Copy'; }, 1800);
            } catch (e) { /* clipboard blocked */ }
        });
    });
}

/* ---------- Contact form ---------- */
function initForm() {
    const form = $('[data-form]');
    if (!form) return;
    const status = $('.form__status', form);
    const label = $('.btn__label', form);
    const rules = {
        name: (v) => v.trim().length >= 2 || 'Please tell me your name.',
        email: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) || 'That email does not look right.',
        message: (v) => v.trim().length >= 10 || 'A sentence or two about the project, please.',
    };
    const check = (input) => {
        const res = rules[input.name]?.(input.value);
        const field = input.closest('.field');
        const err = $('.field__err', field);
        const bad = res !== true && res !== undefined;
        field.classList.toggle('has-error', bad);
        err.textContent = bad ? res : '';
        input.setAttribute('aria-invalid', bad ? 'true' : 'false');
        return !bad;
    };
    $$('.field input, .field textarea', form).forEach((i) => i.addEventListener('blur', () => i.value && check(i)));

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const inputs = $$('.field input, .field textarea', form);
        const ok = inputs.map(check).every(Boolean);
        if (!ok) { inputs.find((i) => i.getAttribute('aria-invalid') === 'true')?.focus(); return; }

        form.classList.add('is-sending');
        label.textContent = 'Sending…';
        status.className = 'form__status';
        status.textContent = '';
        try {
            const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } });
            const data = await res.json();
            if (!res.ok || !data.ok) throw new Error(data.error || 'Something went wrong.');
            status.classList.add('ok');
            status.textContent = data.message || 'Thanks! I will reply within one working day.';
            form.reset();
            $('input[name="topics[]"]', form).checked = true;
        } catch (err) {
            status.classList.add('err');
            status.textContent = err.message || 'Could not send. Please email me directly.';
        } finally {
            form.classList.remove('is-sending');
            label.textContent = 'Send message';
        }
    });
}

/* ---------- 3D hero ---------- */
async function initHero3D() {
    const canvas = $('[data-gl]');
    if (!canvas) return;
    try {
        const { createHero } = await import('./hero3d.js');
        await createHero(canvas, { reduce, getProgress: () => window.__heroProgress || 0 });
        document.documentElement.classList.add('has-gl');
    } catch (e) {
        console.warn('3D hero unavailable, using CSS fallback.', e);
    }
}

/* ---------- Lightbox (gambar proyek & arsip) ---------- */
function initLightbox() {
    const lb = $('[data-lb]');
    if (!lb) return;
    const img = $('[data-lb-img]', lb);
    const cap = $('[data-lb-cap]', lb);
    let list = [];
    let idx = 0;
    let lastFocus = null;

    const show = (i) => {
        idx = (i + list.length) % list.length;
        const it = list[idx];
        img.src = it.src;
        img.alt = it.caption || '';
        cap.textContent = (it.caption ? it.caption + ' — ' : '') + (idx + 1) + ' / ' + list.length;
    };
    const open = (group, src) => {
        list = [...$$(`[data-lightbox="${group}"]`), ...$$(`[data-lightbox-extra="${group}"]`)]
            .map((el) => ({ src: el.dataset.src, caption: el.dataset.caption || '' }));
        lastFocus = document.activeElement;
        lb.hidden = false;
        requestAnimationFrame(() => lb.classList.add('is-open'));
        show(Math.max(0, list.findIndex((x) => x.src === src)));
        lenis?.stop();
        $('[data-lb-close]', lb).focus();
    };
    const close = () => {
        lb.classList.remove('is-open');
        lenis?.start();
        setTimeout(() => { lb.hidden = true; img.src = ''; }, 400);
        lastFocus?.focus();
    };
    document.addEventListener('click', (e) => {
        const t = e.target.closest('[data-lightbox]');
        if (t) open(t.dataset.lightbox, t.dataset.src);
    });
    $('[data-lb-close]', lb).addEventListener('click', close);
    $('[data-lb-prev]', lb).addEventListener('click', () => show(idx - 1));
    $('[data-lb-next]', lb).addEventListener('click', () => show(idx + 1));
    lb.addEventListener('click', (e) => { if (e.target === lb) close(); });
    addEventListener('keydown', (e) => {
        if (lb.hidden) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(idx - 1);
        if (e.key === 'ArrowRight') show(idx + 1);
    });
}

/* ---------- Boot ---------- */
initHero3D();
initLenis();
initClock();
initRotator();
initServices();
initCopy();
initForm();
initLightbox();
runLoader().then(() => {
    initNav();
    initReveal();
    initScrollScenes();
    initPointer();
});
