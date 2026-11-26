/* Portfolio v3 — interactions (vanilla JS) */
(() => {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Reveal */
    const io = new IntersectionObserver((es) => es.forEach((en) => {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
    }), { rootMargin: '0px 0px -8% 0px', threshold: .05 });
    $$('.reveal').forEach((el) => io.observe(el));

    /* Counters */
    const cio = new IntersectionObserver((es) => es.forEach((en) => {
        if (!en.isIntersecting) return;
        cio.unobserve(en.target);
        const el = en.target, to = +el.dataset.countTo, t0 = performance.now(), dur = reduce ? 1 : 1500;
        const step = (now) => {
            const t = Math.min(1, (now - t0) / dur);
            el.textContent = Math.round(to * (1 - Math.pow(1 - t, 4)));
            if (t < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }), { threshold: .5 });
    $$('[data-count-to]').forEach((el) => cio.observe(el));

    /* Header: hide on scroll down, active link, back-to-top */
    const hdr = $('[data-hdr]');
    const totop = $('[data-totop]');
    const links = $$('.hdr__nav a');
    const secs = links.map((a) => document.getElementById(a.hash.slice(1))).filter(Boolean);
    let lastY = scrollY;
    const onScroll = () => {
        const y = scrollY;
        hdr.classList.toggle('is-hidden', y > lastY && y > 500 && mnav.hidden);
        lastY = y;
        totop.classList.toggle('is-on', y > 900);
        let cur = secs[0]?.id;
        secs.forEach((s) => { if (s.getBoundingClientRect().top < innerHeight * .35) cur = s.id; });
        links.forEach((a) => a.classList.toggle('is-on', a.hash === '#' + cur));
    };

    /* Mobile menu */
    const burger = $('[data-burger]');
    const mnav = $('[data-mnav]');
    const setMenu = (open) => { mnav.hidden = !open; burger.setAttribute('aria-expanded', String(open)); burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu'); };
    burger.addEventListener('click', () => setMenu(mnav.hidden));
    $$('a', mnav).forEach((a) => a.addEventListener('click', () => setMenu(false)));
    addEventListener('keydown', (e) => { if (e.key === 'Escape' && !mnav.hidden) setMenu(false); });
    addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* Services accordion (one open at a time) */
    $$('[data-acc] .acc__btn').forEach((btn) => btn.addEventListener('click', () => {
        const item = btn.closest('.acc__item');
        const open = !item.classList.contains('is-open');
        $$('[data-acc] .acc__item').forEach((it) => { it.classList.remove('is-open'); $('.acc__btn', it).setAttribute('aria-expanded', 'false'); });
        if (open) { item.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true'); }
    }));

    /* FAQ: one open at a time */
    const faqs = $$('[data-faq] details');
    faqs.forEach((d) => d.addEventListener('toggle', () => { if (d.open) faqs.forEach((o) => o !== d && (o.open = false)); }));

    /* Daftar proyek: filter kategori + pencarian */
    const fbox = $('[data-filters]');
    const list = $('[data-list]');
    if (fbox && list) {
        const search = $('[data-search]');
        const empty = $('[data-empty]');
        let cat = ($('.is-on', fbox) || {}).dataset?.f || '*';
        const apply = () => {
            const q = (search?.value || '').trim().toLowerCase();
            let shown = 0;
            $$('.pcard', list).forEach((card) => {
                const ok = (cat === '*' || card.dataset.cat === cat) && (!q || card.dataset.text.includes(q));
                card.hidden = !ok;
                if (ok) { shown++; card.classList.add('is-in'); }
            });
            if (empty) empty.hidden = shown > 0;
        };
        fbox.addEventListener('click', (e) => {
            const a = e.target.closest('a[data-f]');
            if (!a) return;
            e.preventDefault();
            cat = a.dataset.f;
            $$('a', fbox).forEach((x) => x.classList.toggle('is-on', x === a));
            history.replaceState(null, '', a.href);
            apply();
        });
        search?.addEventListener('input', apply);
        /* Versi statis: kategori dibaca dari ?cat= di alamat */
        const qc = new URLSearchParams(location.search).get('cat');
        const qa = qc && $$('a[data-f]', fbox).find((x) => x.dataset.f === qc);
        if (qa) { cat = qc; $$('a', fbox).forEach((x) => x.classList.toggle('is-on', x === qa)); apply(); }
    }

    /* Testimonials */
    const track = $('[data-track]');
    if (track) {
        const step = (d) => { const c = track.firstElementChild; track.scrollBy({ left: d * ((c?.offsetWidth || 300) + 19), behavior: reduce ? 'auto' : 'smooth' }); };
        $('[data-prev]')?.addEventListener('click', () => step(-1));
        $('[data-next]')?.addEventListener('click', () => step(1));
    }

    /* Lightbox */
    const lb = $('[data-lb]');
    const lbImg = $('[data-lb-img]');
    const lbCap = $('[data-lb-cap]');
    let set = [], idx = 0, lastFocus = null;
    const show = () => { lbImg.src = set[idx].src; lbCap.textContent = set[idx].cap || ''; const many = set.length > 1; $('[data-lb-prev]').hidden = !many; $('[data-lb-next]').hidden = !many; };
    const openLb = (group, src) => {
        const els = $$(`[data-lightbox="${CSS.escape(group)}"], [data-lightbox-extra="${CSS.escape(group)}"]`);
        set = els.map((el) => ({ src: el.dataset.src, cap: el.dataset.caption }));
        idx = Math.max(0, set.findIndex((s) => s.src === src));
        lastFocus = document.activeElement;
        show();
        lb.hidden = false;
        document.body.style.overflow = 'hidden';
        $('[data-lb-close]').focus();
    };
    const closeLb = () => { lb.hidden = true; document.body.style.overflow = ''; lbImg.removeAttribute('src'); lastFocus?.focus(); };
    document.addEventListener('click', (e) => {
        const t = e.target.closest('[data-lightbox]');
        if (t) { openLb(t.dataset.lightbox, t.dataset.src); return; }
        const o = e.target.closest('[data-open]');
        if (o) { const first = $(`[data-lightbox="${CSS.escape(o.dataset.open)}"]`); if (first) openLb(o.dataset.open, first.dataset.src); }
    });
    $('[data-lb-close]').addEventListener('click', closeLb);
    $('[data-lb-prev]').addEventListener('click', () => { idx = (idx - 1 + set.length) % set.length; show(); });
    $('[data-lb-next]').addEventListener('click', () => { idx = (idx + 1) % set.length; show(); });
    lb.addEventListener('click', (e) => { if (e.target === lb) closeLb(); });
    addEventListener('keydown', (e) => {
        if (lb.hidden) return;
        if (e.key === 'Escape') closeLb();
        if (e.key === 'ArrowLeft') $('[data-lb-prev]').click();
        if (e.key === 'ArrowRight') $('[data-lb-next]').click();
    });

    /* Contact form */
    const form = $('[data-form]');
    const msg = $('[data-form-msg]');
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        let bad = null;
        $$('[required]', form).forEach((f) => {
            const ok = f.type === 'email' ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim()) : f.value.trim().length >= (f.tagName === 'TEXTAREA' ? 10 : 1);
            f.classList.toggle('is-bad', !ok);
            if (!ok && !bad) bad = f;
        });
        if (bad) { msg.className = 'cform__msg is-err'; msg.textContent = 'Please fill in the highlighted fields.'; bad.focus(); return; }
        const btn = $('button[type="submit"]', form);
        const isStatic = document.documentElement.hasAttribute('data-static');
        if (isStatic && !form.dataset.w3f) {
            /* Versi statis tanpa Web3Forms: kirim lewat WhatsApp */
            const v = (n) => (form.elements[n]?.value || '').trim();
            const lines = [
                `Hi ${form.dataset.to || ''}! I'm ${v('name')} (${v('email')}${v('phone') ? ', ' + v('phone') : ''}).`,
                `Interested in: ${v('topics[]')}`,
                v('budget') ? `Budget: ${v('budget')}` : '',
                v('deadline') ? `Deadline: ${v('deadline')}` : '',
                '',
                v('message'),
            ];
            const text = lines.filter((x, i) => x !== '' || i === 4).join('\n');
            const wa = form.dataset.wa || '';
            const url = wa.startsWith('mailto:')
                ? `${wa}?subject=${encodeURIComponent('Project inquiry')}&body=${encodeURIComponent(text)}`
                : `${wa}?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank', 'noopener');
            msg.className = 'cform__msg is-ok';
            msg.textContent = wa.startsWith('mailto:') ? 'Opening your email app… just press send there.' : 'Opening WhatsApp… just press send there.';
            return;
        }
        btn.disabled = true;
        msg.className = 'cform__msg'; msg.textContent = 'Sending…';
        try {
            let res, data;
            if (isStatic) {
                /* Versi statis + Web3Forms: pesan diteruskan ke email */
                const fd = new FormData(form);
                if (fd.get('website')) { form.reset(); msg.className = 'cform__msg is-ok'; msg.textContent = 'Thanks! Message sent.'; return; }
                const body = new FormData();
                for (const [k, val] of fd) if (k !== 'website') body.append(k === 'topics[]' ? 'topics' : k, val);
                body.append('access_key', form.dataset.w3f);
                body.append('subject', 'New project inquiry from ' + (fd.get('name') || 'portfolio'));
                res = await fetch('https://api.web3forms.com/submit', { method: 'POST', body, headers: { Accept: 'application/json' } });
                data = await res.json().catch(() => ({}));
                data.ok = data.success;
            } else {
                res = await fetch(form.action, { method: 'POST', body: new FormData(form) });
                data = await res.json().catch(() => ({}));
            }
            if (!res.ok || !data.ok) throw new Error(data.error || 'Could not send right now. Please try WhatsApp.');
            msg.className = 'cform__msg is-ok'; msg.textContent = data.message || 'Thanks! Message sent.';
            form.reset();
        } catch (err) {
            msg.className = 'cform__msg is-err'; msg.textContent = err.message;
        } finally { btn.disabled = false; }
    });
    form?.addEventListener('input', (e) => e.target.classList.remove('is-bad'));

    /* Footer quick message → WhatsApp (or email) */
    const qf = $('.ftr__wa');
    qf?.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = qf.q.value.trim() || 'Hi! I would like to talk about a project.';
        const url = qf.dataset.wa ? `${qf.dataset.wa}?text=${encodeURIComponent(text)}` : `mailto:${qf.dataset.mail}?body=${encodeURIComponent(text)}`;
        window.open(url, '_blank', 'noopener');
    });
})();
