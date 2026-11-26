/* Portfolio v2 — interactions (vanilla JS) */
(() => {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Intro */
    requestAnimationFrame(() => setTimeout(() => document.documentElement.classList.add('is-ready'), 60));

    /* Reveal on scroll */
    const io = new IntersectionObserver((entries) => {
        entries.forEach((en) => {
            if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
        });
    }, { rootMargin: '0px 0px -10% 0px', threshold: .05 });
    $$('.reveal').forEach((el) => io.observe(el));

    /* Counters */
    const cio = new IntersectionObserver((entries) => {
        entries.forEach((en) => {
            if (!en.isIntersecting) return;
            cio.unobserve(en.target);
            const el = en.target, to = +el.dataset.countTo, t0 = performance.now(), dur = reduce ? 1 : 1600;
            const step = (now) => {
                const t = clamp((now - t0) / dur);
                el.textContent = Math.round(to * (1 - Math.pow(1 - t, 4)));
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        });
    }, { threshold: .5 });
    $$('[data-count-to]').forEach((el) => cio.observe(el));

    /* Nav: active link, hide on scroll down, mobile menu */
    const nav = $('[data-nav]');
    const links = $$('.nav__group a, .mmenu a');
    const ids = ['top', 'about', 'services', 'experience', 'portfolio', 'contact'];
    let lastY = scrollY;
    const onScroll = () => {
        const y = scrollY;
        nav.classList.toggle('is-hidden', y > lastY && y > 400 && !nav.classList.contains('menu-open'));
        lastY = y;

        let current = 'top';
        ids.forEach((id) => {
            const el = document.getElementById(id);
            if (el && el.getBoundingClientRect().top < innerHeight * .4) current = id;
        });
        links.forEach((a) => a.classList.toggle('is-active', a.getAttribute('href') === '#' + current));

        // Timeline progress line
        const tl = $('[data-timeline]');
        if (tl) {
            const r = tl.getBoundingClientRect();
            tl.style.setProperty('--tp', clamp((innerHeight * .6 - r.top) / r.height).toFixed(3));
        }
    };
    addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    const burger = $('[data-burger]');
    const mm = $('[data-mmenu]');
    const setMenu = (open) => {
        mm.hidden = !open;
        burger.setAttribute('aria-expanded', String(open));
        nav.classList.toggle('menu-open', open);
    };
    burger.addEventListener('click', () => setMenu(mm.hidden));
    $$('a', mm).forEach((a) => a.addEventListener('click', () => setMenu(false)));
    addEventListener('keydown', (e) => e.key === 'Escape' && !mm.hidden && setMenu(false));

    /* Horizontal rails (services + portfolio) */
    const railStep = (track, dir) => {
        const card = [...track.children].find((c) => !c.hidden);
        const gap = parseFloat(getComputedStyle(track).columnGap) || 16;
        track.scrollBy({ left: dir * ((card?.offsetWidth || 300) + gap), behavior: reduce ? 'auto' : 'smooth' });
    };
    const wireRail = (track, prev, next) => {
        if (!track) return;
        prev?.addEventListener('click', () => railStep(track, -1));
        next?.addEventListener('click', () => railStep(track, 1));
        const sync = () => {
            const max = track.scrollWidth - track.clientWidth - 2;
            if (prev) prev.disabled = track.scrollLeft <= 2;
            if (next) next.disabled = track.scrollLeft >= max;
        };
        track.addEventListener('scroll', sync, { passive: true });
        addEventListener('resize', sync);
        sync();
        return sync;
    };
    wireRail($('[data-rail]'), $('[data-rail-prev]'), $('[data-rail-next]'));
    const pfSync = wireRail($('[data-pf-track]'), $('[data-pf-prev]'), $('[data-pf-next]'));

    /* Portfolio filters */
    $$('[data-filter]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const f = btn.dataset.filter;
            $$('[data-filter]').forEach((b) => { b.classList.toggle('is-on', b === btn); b.setAttribute('aria-selected', String(b === btn)); });
            $$('.pf__card').forEach((card) => (card.hidden = f !== 'all' && card.dataset.cat !== f));
            $('[data-pf-track]').scrollTo({ left: 0 });
            pfSync?.();
        });
    });

    /* Testimonials carousel: kartu aktif di tengah */
    const tst = $('[data-tst]');
    if (tst) {
        const track = $('[data-tst-track]', tst);
        const cards = $$('[data-tst-card]', tst);
        let i = Math.min(1, cards.length - 1);
        let timer;
        const go = (n) => {
            i = (n + cards.length) % cards.length;
            const c = cards[i];
            const offset = c.offsetLeft - (tst.clientWidth - c.offsetWidth) / 2;
            track.style.transform = `translateX(${-offset}px)`;
            cards.forEach((x, k) => x.classList.toggle('is-active', k === i));
        };
        const auto = () => { clearInterval(timer); if (!reduce) timer = setInterval(() => go(i + 1), 6000); };
        $('[data-tst-prev]').addEventListener('click', () => { go(i - 1); auto(); });
        $('[data-tst-next]').addEventListener('click', () => { go(i + 1); auto(); });
        addEventListener('resize', () => go(i));
        // Swipe
        let sx = null;
        tst.addEventListener('pointerdown', (e) => (sx = e.clientX));
        tst.addEventListener('pointerup', (e) => {
            if (sx === null) return;
            const dx = e.clientX - sx;
            if (Math.abs(dx) > 40) { go(i + (dx < 0 ? 1 : -1)); auto(); }
            sx = null;
        });
        go(i);
        auto();
    }

    /* CTA → WhatsApp (atau email) dengan pesan terisi */
    const wf = $('[data-wa-form]');
    wf?.addEventListener('submit', (e) => {
        e.preventDefault();
        const idea = $('input', wf).value.trim();
        const base = wf.dataset.wa;
        const text = 'Halo Dzakaria, saya tertarik bekerja sama.' + (idea ? '\n\nProyek saya: ' + idea : '');
        const url = base.startsWith('mailto:')
            ? base + '?subject=' + encodeURIComponent('Project inquiry') + '&body=' + encodeURIComponent(text)
            : base + '?text=' + encodeURIComponent(text);
        window.open(url, '_blank', 'noopener');
    });

    /* Lightbox */
    const lb = $('[data-lb]');
    if (lb) {
        const img = $('[data-lb-img]', lb);
        const cap = $('[data-lb-cap]', lb);
        let last = null;
        const close = () => { lb.classList.remove('is-open'); setTimeout(() => (lb.hidden = true), 300); last?.focus(); };
        document.addEventListener('click', (e) => {
            const t = e.target.closest('[data-lightbox]');
            if (!t) return;
            last = t;
            img.src = t.dataset.src;
            img.alt = t.dataset.caption || '';
            cap.textContent = t.dataset.caption || '';
            lb.hidden = false;
            requestAnimationFrame(() => lb.classList.add('is-open'));
            $('[data-lb-close]', lb).focus();
        });
        $('[data-lb-close]', lb).addEventListener('click', close);
        lb.addEventListener('click', (e) => e.target === lb && close());
        addEventListener('keydown', (e) => e.key === 'Escape' && !lb.hidden && close());
    }

    /* Hero parallax ringan mengikuti kursor */
    const visual = $('.hero__visual');
    if (visual && !reduce && matchMedia('(pointer: fine)').matches) {
        addEventListener('pointermove', (e) => {
            const x = e.clientX / innerWidth - .5, y = e.clientY / innerHeight - .5;
            $$('.hero__badge', visual).forEach((b, k) => (b.style.translate = `${x * (12 + k * 8)}px ${y * (12 + k * 8)}px`));
            const prod = $('.hero__products, .hero__person, .hero__photo', visual);
            if (prod) prod.style.translate = `${x * -8}px ${y * -6}px`;
        }, { passive: true });
    }
})();
