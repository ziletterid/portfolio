/* Dashboard portfolio — teks, proyek, dan gambar */
(() => {
    const app = document.querySelector('[data-app]');
    if (!app) return;
    const CSRF = app.dataset.csrf;
    const view = document.querySelector('[data-view]');
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const kb = (n) => (n > 1048576 ? (n / 1048576).toFixed(1) + ' MB' : Math.round(n / 1024) + ' KB');
    const h = (tag, cls, html) => { const n = document.createElement(tag); if (cls) n.className = cls; if (html != null) n.innerHTML = html; return n; };
    const getPath = (o, p) => p.split('.').reduce((a, k) => (a == null ? undefined : a[k]), o);
    const setPath = (o, p, v) => { const ks = p.split('.'); const last = ks.pop(); let r = o; ks.forEach((k) => { if (typeof r[k] !== 'object' || r[k] == null) r[k] = {}; r = r[k]; }); r[last] = v; };

    let state = null;
    let route = '';
    let dirty = false;
    let uid = 0;
    let imageMounts = [];

    /* ---------- Toast ---------- */
    const toastEl = $('[data-toast]');
    let toastT;
    const toast = (msg, type = 'ok') => {
        toastEl.textContent = msg;
        toastEl.className = 'toast is-on toast--' + type;
        clearTimeout(toastT);
        toastT = setTimeout(() => toastEl.classList.remove('is-on'), type === 'err' ? 6000 : 2600);
    };

    /* ---------- API ---------- */
    const api = async (action, body = {}) => {
        const res = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF': CSRF },
            body: JSON.stringify({ action, ...body }),
            credentials: 'same-origin',
        });
        const data = await res.json().catch(() => ({ ok: false, error: 'Respon server tidak valid.' }));
        if (res.status === 401) location.reload();
        if (!res.ok || data.ok === false) throw new Error(data.error || 'Terjadi kesalahan.');
        return data;
    };

    const upload = (fields, files, bar) => new Promise((resolve, reject) => {
        const fd = new FormData();
        Object.entries(fields).forEach(([k, v]) => fd.append(k, v));
        if (fields.action === 'profile') fd.append('file', files[0]);
        else files.forEach((f) => fd.append('files[]', f));
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'api.php');
        xhr.setRequestHeader('X-CSRF', CSRF);
        xhr.upload.onprogress = (e) => { if (bar && e.lengthComputable) bar.style.width = (e.loaded / e.total * 100) + '%'; };
        xhr.onload = () => {
            let data = {};
            try { data = JSON.parse(xhr.responseText); } catch (e) { data = { ok: false, error: 'Upload gagal (respon tidak valid). Mungkin file terlalu besar untuk server.' }; }
            if (xhr.status === 401) location.reload();
            if (xhr.status >= 400 || data.ok === false) reject(Object.assign(new Error(data.error || 'Upload gagal.'), { data }));
            else resolve(data);
        };
        xhr.onerror = () => reject(new Error('Koneksi terputus.'));
        xhr.send(fd);
    });

    /** Perbarui data. Operasi gambar hanya menggambar ulang bagian gambar, supaya isian teks yang belum disimpan tidak hilang. */
    const setState = (s, { images = false } = {}) => {
        if (!s || !s.projects) return;
        state = s;
        updateBadges();
        if (images) imageMounts.forEach((m) => document.contains(m.el) && m.draw());
        else render();
    };

    /* ================================================================ */
    /* Gambar: dropzone + grid                                          */
    /* ================================================================ */
    const makeDrop = (onFiles, { multiple = true, label } = {}) => {
        const node = $('#tpl-dropzone').content.firstElementChild.cloneNode(true);
        const input = $('input', node);
        input.multiple = multiple;
        if (label) $('.drop__text', node).innerHTML = label;
        const go = (list) => {
            const files = [...list].filter((f) => f.type.startsWith('image/') || /\.svg$/i.test(f.name));
            if (!files.length) return toast('Pilih file gambar.', 'err');
            onFiles(files, $('.drop__bar i', node), node);
        };
        input.addEventListener('change', () => { go(input.files); input.value = ''; });
        ['dragenter', 'dragover'].forEach((ev) => node.addEventListener(ev, (e) => {
            if (![...e.dataTransfer.types].includes('Files')) return;
            e.preventDefault(); node.classList.add('is-over');
        }));
        ['dragleave', 'drop'].forEach((ev) => node.addEventListener(ev, () => node.classList.remove('is-over')));
        node.addEventListener('drop', (e) => { if (e.dataTransfer.files.length) { e.preventDefault(); go(e.dataTransfer.files); } });
        return node;
    };

    const doUpload = async (fields, files, bar, node) => {
        node.classList.add('is-busy');
        bar.style.width = '0%';
        try {
            const data = await upload(fields, files, bar);
            if (data.errors?.length) toast(`${data.added} gambar masuk. Gagal: ${data.errors.join('; ')}`, 'err');
            else toast(files.length > 1 ? `${files.length} gambar berhasil diupload.` : 'Gambar berhasil diupload.');
            setState(data, { images: true });
        } catch (e) {
            toast(e.message, 'err');
        } finally {
            node.classList.remove('is-busy');
            bar.style.width = '0%';
        }
    };

    const grid = (target, files, { captions = false } = {}) => {
        const g = h('ol', 'thumbs');
        g.dataset.target = target;
        if (!files.length) {
            g.innerHTML = `<li class="thumbs__empty">${captions ? 'Galeri masih kosong.' : 'Belum ada gambar. Website memakai mockup cadangan sampai kamu upload gambar.'}</li>`;
            return g;
        }
        files.forEach((f, i) => {
            const li = h('li', 'thumb');
            li.draggable = true;
            li.dataset.name = f.name;
            li.innerHTML = `
                <div class="thumb__img"><img src="${esc(f.url)}" alt="" loading="lazy">
                    ${i === 0 && !captions ? '<span class="thumb__badge">Cover</span>' : `<span class="thumb__num">${i + 1}</span>`}
                </div>
                ${captions ? `<input class="thumb__cap" value="${esc(f.caption)}" aria-label="Keterangan gambar ${i + 1}" maxlength="60">` : ''}
                <div class="thumb__meta">${f.w ? f.w + '×' + f.h + ' · ' : ''}${kb(f.size)}</div>
                <div class="thumb__tools">
                    <button type="button" data-act="left" title="Geser ke kiri" aria-label="Geser ke kiri" ${i === 0 ? 'disabled' : ''}>←</button>
                    <button type="button" data-act="right" title="Geser ke kanan" aria-label="Geser ke kanan" ${i === files.length - 1 ? 'disabled' : ''}>→</button>
                    ${!captions && i > 0 ? '<button type="button" data-act="cover" title="Jadikan cover" aria-label="Jadikan cover">★</button>' : ''}
                    <a href="${esc(f.url)}" target="_blank" rel="noopener" title="Lihat ukuran penuh" aria-label="Lihat ukuran penuh">⤢</a>
                    <button type="button" data-act="delete" class="danger" title="Hapus" aria-label="Hapus gambar">✕</button>
                </div>`;
            g.appendChild(li);
        });
        wireGrid(g);
        return g;
    };

    const currentOrder = (g) => $$('.thumb', g).map((li) => li.dataset.name);
    const imgOp = async (action, body, msg) => {
        try { setState(await api(action, body), { images: true }); toast(msg); } catch (err) { toast(err.message, 'err'); imageMounts.forEach((m) => document.contains(m.el) && m.draw()); }
    };

    const wireGrid = (g) => {
        const target = g.dataset.target;
        let dragging = null;
        g.addEventListener('dragstart', (e) => {
            dragging = e.target.closest('.thumb');
            if (!dragging) return;
            dragging.classList.add('is-drag');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', dragging.dataset.name);
        });
        g.addEventListener('dragover', (e) => {
            if (!dragging) return;
            e.preventDefault();
            const over = e.target.closest('.thumb');
            if (!over || over === dragging) return;
            const r = over.getBoundingClientRect();
            over.parentNode.insertBefore(dragging, (e.clientX - r.left) > r.width / 2 ? over.nextSibling : over);
        });
        g.addEventListener('dragend', () => {
            if (!dragging) return;
            dragging.classList.remove('is-drag');
            dragging = null;
            imgOp('reorder', { target, order: currentOrder(g) }, 'Urutan disimpan.');
        });
        g.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-act]');
            if (!btn) return;
            const name = btn.closest('.thumb').dataset.name;
            const order = currentOrder(g);
            const i = order.indexOf(name);
            const act = btn.dataset.act;
            if (act === 'left' || act === 'right') {
                const j = act === 'left' ? i - 1 : i + 1;
                [order[i], order[j]] = [order[j], order[i]];
                imgOp('reorder', { target, order }, 'Urutan disimpan.');
            } else if (act === 'cover') {
                imgOp('cover', { target, name }, 'Cover diganti.');
            } else if (act === 'delete') {
                if (!confirm('Hapus gambar ini dari website?\n\nFile dipindahkan ke storage/trash/ dan masih bisa dikembalikan manual.')) return;
                imgOp('delete', { target, name }, 'Gambar dihapus.');
            }
        });
        g.addEventListener('change', (e) => {
            const input = e.target.closest('.thumb__cap');
            if (!input) return;
            imgOp('caption', { target, name: input.closest('.thumb').dataset.name, caption: input.value }, 'Keterangan disimpan.');
        });
        g.addEventListener('keydown', (e) => { if (e.target.matches('.thumb__cap') && e.key === 'Enter') e.target.blur(); });
    };

    /** Pasang area gambar yang bisa digambar ulang sendiri. */
    const mountImages = (el, draw) => {
        const m = { el, draw: () => { el.innerHTML = ''; draw(el); } };
        imageMounts.push(m);
        m.draw();
        return el;
    };

    /* ================================================================ */
    /* Isian formulir                                                   */
    /* ================================================================ */
    const sizeCls = (f) => 'field' + (f.half ? ' field--half' : '') + (f.third ? ' field--third' : '') + (f.quarter ? ' field--quarter' : '');

    /** Buat satu isian. Mengembalikan { el, get, f }. */
    const buildField = (f, value) => {
        const id = 'f' + (++uid);
        const wrap = h('div', sizeCls(f) + ' field--' + f.type);
        const label = `<label class="field__label" for="${id}">${esc(f.label)}</label>`;
        const hint = f.hint ? `<small class="field__hint">${esc(f.hint)}</small>` : '';
        let get;

        if (f.type === 'repeater') return buildRepeater(f, value);

        if (f.type === 'textarea' || f.type === 'lines') {
            const val = f.type === 'lines' ? (value || []).join('\n') : (value ?? '');
            const rows = f.type === 'lines' ? Math.min(8, Math.max(3, (value || []).length + 1)) : Math.min(8, Math.max(3, Math.ceil(String(val).length / 70) + 1));
            wrap.innerHTML = `${label}<textarea id="${id}" rows="${rows}" ${f.type === 'textarea' && f.max ? `maxlength="${f.max}"` : ''}>${esc(val)}</textarea>${hint}`;
            const ta = $('textarea', wrap);
            get = () => (f.type === 'lines' ? ta.value.split('\n').map((s) => s.trim()).filter(Boolean) : ta.value.trim());
            if (f.type === 'textarea' && f.max) counter(wrap, ta, f.max);
        } else if (f.type === 'select') {
            wrap.innerHTML = `${label}<select id="${id}">${Object.entries(f.options).map(([k, v]) => `<option value="${esc(k)}" ${k === value ? 'selected' : ''}>${esc(v)}</option>`).join('')}</select>${hint}`;
            const sel = $('select', wrap);
            get = () => sel.value;
        } else if (f.type === 'color') {
            const v = /^#[0-9a-f]{6}$/i.test(value || '') ? value : (f.default || '#000000');
            wrap.innerHTML = `${label}<div class="color"><input type="color" value="${esc(v)}" aria-label="${esc(f.label)}"><input id="${id}" type="text" value="${esc(v)}" maxlength="7" spellcheck="false"></div>${hint}`;
            const [pick, txt] = $$('input', wrap);
            pick.addEventListener('input', () => { txt.value = pick.value; });
            txt.addEventListener('input', () => { if (/^#[0-9a-f]{6}$/i.test(txt.value)) pick.value = txt.value; });
            get = () => (/^#[0-9a-f]{6}$/i.test(txt.value) ? txt.value.toLowerCase() : pick.value);
        } else {
            const type = { email: 'email', url: 'url', number: 'number', wa: 'tel' }[f.type] || 'text';
            const val = f.type === 'tags' ? (value || []).join(', ') : (value ?? '');
            const list = f.list ? `<datalist id="${id}-l">${f.list.map((o) => `<option value="${esc(o)}">`).join('')}</datalist>` : '';
            wrap.innerHTML = `${label}<input id="${id}" type="${type}" value="${esc(val)}" ${f.max && f.type !== 'tags' && f.type !== 'number' ? `maxlength="${f.max}"` : ''} ${f.list ? `list="${id}-l"` : ''} ${type === 'number' ? 'min="0" inputmode="numeric"' : ''}>${list}${hint}`;
            const inp = $('input', wrap);
            get = () => {
                if (f.type === 'tags') return inp.value.split(',').map((s) => s.trim()).filter(Boolean);
                if (f.type === 'number') return Number(inp.value) || 0;
                return inp.value.trim();
            };
            if (f.type === 'text' && f.max >= 40) counter(wrap, inp, f.max);
        }
        return { el: wrap, get, f };
    };

    const counter = (wrap, input, max) => {
        const c = h('span', 'field__count');
        const upd = () => { c.textContent = `${input.value.length}/${max}`; c.classList.toggle('is-near', input.value.length > max * 0.9); };
        input.addEventListener('input', upd);
        upd();
        $('.field__label', wrap).after(c);
    };

    /** Daftar yang bisa ditambah / dihapus / diurutkan. */
    const buildRepeater = (f, value) => {
        const wrap = h('div', 'field field--repeater');
        wrap.innerHTML = `<div class="rep__head"><span class="field__label">${esc(f.label)}</span><span class="rep__count"></span></div><ol class="rep"></ol>`;
        const list = $('.rep', wrap);
        const items = [];
        const add = h('button', 'btn btn--ghost btn--sm rep__add', `+ Tambah ${esc((f.item || 'item').toLowerCase())}`);
        add.type = 'button';
        wrap.appendChild(add);

        const sync = () => {
            items.forEach((it, i) => {
                $('.rep__no', it.el).textContent = `${f.item || 'Item'} ${i + 1}`;
                $('[data-up]', it.el).disabled = i === 0;
                $('[data-down]', it.el).disabled = i === items.length - 1;
            });
            add.disabled = items.length >= (f.max || 20);
            $('.rep__count', wrap).textContent = `${items.length} / ${f.max || 20}`;
        };
        const addItem = (v = {}, focus = false) => {
            const li = h('li', 'rep__item');
            li.innerHTML = `<div class="rep__bar"><b class="rep__no"></b><div class="rep__tools">
                <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
                <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
                <button type="button" data-del class="danger" title="Hapus" aria-label="Hapus">✕</button></div></div><div class="fields"></div>`;
            const sub = f.fields.map((sf) => buildField(sf, getPath(v, sf.key)));
            sub.forEach((s) => $('.fields', li).appendChild(s.el));
            const it = { el: li, sub };
            items.push(it);
            list.appendChild(li);
            $('[data-del]', li).addEventListener('click', () => {
                const filled = sub.some((s) => { const x = s.get(); return Array.isArray(x) ? x.length : x; });
                if (filled && !confirm(`Hapus ${(f.item || 'item').toLowerCase()} ini?`)) return;
                items.splice(items.indexOf(it), 1); li.remove(); sync(); markDirty();
            });
            const move = (d) => {
                const i = items.indexOf(it); const j = i + d;
                if (j < 0 || j >= items.length) return;
                [items[i], items[j]] = [items[j], items[i]];
                if (d < 0) list.insertBefore(li, items[j + 1].el); else list.insertBefore(items[i].el, li);
                sync(); markDirty();
            };
            $('[data-up]', li).addEventListener('click', () => move(-1));
            $('[data-down]', li).addEventListener('click', () => move(1));
            sync();
            if (focus) $('input, textarea', li)?.focus();
        };
        (Array.isArray(value) ? value : []).forEach((v) => addItem(v));
        add.addEventListener('click', () => { addItem({}, true); markDirty(); });
        sync();
        const get = () => items.map((it) => { const o = {}; it.sub.forEach((s) => setPath(o, s.f.key, s.get())); return o; });
        return { el: wrap, get, f };
    };

    /* ---------- Simpan bar & perubahan belum disimpan ---------- */
    const markDirty = () => {
        if (dirty) return;
        dirty = true;
        $$('.savebar').forEach((b) => b.classList.add('is-dirty'));
    };
    const clearDirty = () => { dirty = false; $$('.savebar').forEach((b) => b.classList.remove('is-dirty')); };
    window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

    const saveBar = (onSave, extra = '') => {
        const bar = h('div', 'savebar', `<span class="savebar__msg"><i></i><span class="savebar__clean">Semua perubahan tersimpan</span><span class="savebar__dirty">Ada perubahan yang belum disimpan</span></span><div class="savebar__btns">${extra}<button type="button" class="btn btn--accent" data-save>Simpan perubahan</button></div>`);
        const btn = $('[data-save]', bar);
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            btn.textContent = 'Menyimpan…';
            try { await onSave(); } finally { btn.disabled = false; btn.textContent = 'Simpan perubahan'; }
        });
        return bar;
    };

    const watch = (el) => {
        el.addEventListener('input', markDirty);
        el.addEventListener('change', markDirty);
        el.addEventListener('keydown', (e) => { if ((e.metaKey || e.ctrlKey) && e.key === 's') { e.preventDefault(); $('[data-save]')?.click(); } });
    };

    /* ================================================================ */
    /* Halaman                                                          */
    /* ================================================================ */
    const head = (title, desc, actions = '') => h('header', 'vhead', `<div><h1>${esc(title)}</h1>${desc ? `<p>${desc}</p>` : ''}</div>${actions ? `<div class="vhead__end">${actions}</div>` : ''}`);
    const card = (title, desc) => h('section', 'card', title ? `<header class="card__head"><h2>${esc(title)}</h2>${desc ? `<p>${desc}</p>` : ''}</header>` : '');

    /** Panel teks umum dari schema. */
    const viewPanel = (id, before) => {
        const p = state.panels[id];
        view.appendChild(head(p.title, esc(p.desc)));
        if (before) before();
        const box = card('', '');
        const fields = h('div', 'fields');
        const built = p.fields.map((f) => buildField(f, p.values[f.path]));
        built.forEach((b) => fields.appendChild(b.el));
        box.appendChild(fields);
        view.appendChild(box);
        watch(box);
        const bar = saveBar(async () => {
            const values = {};
            built.forEach((b) => { values[b.f.path] = b.get(); });
            try {
                const s = await api('content_save', { panel: id, values });
                clearDirty(); setState(s); toast('Tersimpan. Refresh website untuk melihat hasilnya.');
            } catch (e) { toast(e.message, 'err'); }
        }, '<button type="button" class="btn btn--ghost" data-reset>Reset ke bawaan</button>');
        $('[data-reset]', bar).addEventListener('click', async () => {
            if (!confirm(`Kembalikan semua isian "${p.title}" ke teks bawaan dari data/content.php?`)) return;
            try { const s = await api('content_reset', { panel: id }); clearDirty(); setState(s); toast('Dikembalikan ke bawaan.'); } catch (e) { toast(e.message, 'err'); }
        });
        view.appendChild(bar);
    };

    const viewProfilePhotos = () => {
        const box = card('Foto profil', '');
        const wrap = h('div', 'profile');
        box.appendChild(wrap);
        view.appendChild(box);
        mountImages(wrap, (el) => {
            const slot = (kind, title, desc, file) => {
                const s = h('div', 'pslot', `
                    <div class="pslot__preview ${kind === 'cutout' ? 'pslot__preview--check' : ''}">${file ? `<img src="${esc(file.url)}" alt="">` : '<span>Belum ada</span>'}</div>
                    <div class="pslot__body"><h3>${title}</h3><p>${desc}</p><div class="pslot__actions"></div></div>`);
                $('.pslot__actions', s).appendChild(makeDrop((files, bar, node) => doUpload({ action: 'profile', kind }, [files[0]], bar, node), {
                    multiple: false, label: `<b>${file ? 'Ganti foto' : 'Upload foto'}</b> (seret atau klik)`,
                }));
                if (file) {
                    const del = h('button', 'btn btn--ghost btn--sm', 'Hapus foto');
                    del.type = 'button';
                    del.addEventListener('click', () => { if (confirm('Hapus foto ini?')) imgOp('profile_delete', { kind }, 'Foto dihapus.'); });
                    $('.pslot__actions', s).appendChild(del);
                }
                el.appendChild(s);
            };
            slot('photo', 'Foto profil', 'JPG/PNG/WebP, sebaiknya persegi minimal 800×800 px. Tampil di kartu About.', state.profile);
            slot('cutout', 'Foto potongan (opsional)', 'PNG <b>transparan tanpa latar</b> (hapus background di remove.bg). Dipakai di hero versi oranye.', state.cutout);
        });
    };

    /* ---------- Beranda ---------- */
    const viewHome = () => {
        const per = state.panels.profil.values;
        const name = per['person.first'] || '';
        const imgCount = state.projects.reduce((n, p) => n + p.files.length, 0);
        view.appendChild(head(`Halo, ${name}`, 'Semua isi website bisa diubah dari sini. Perubahan langsung tampil setelah website di-refresh.', '<a class="btn btn--ghost" href="../" target="_blank" rel="noopener">Buka website ↗</a>'));

        const stats = h('div', 'stats');
        [
            ['Proyek', state.projects.length, '#proyek'],
            ['Gambar proyek', imgCount, '#proyek'],
            ['Galeri arsip', state.gallery.length, '#galeri'],
            ['Pesan masuk', state.messages.length, '#pesan'],
        ].forEach(([l, v, href]) => { stats.appendChild(h('a', 'stat', `<b>${v}</b><span>${l}</span>`)).href = href; });
        view.appendChild(stats);

        const todo = [];
        if (!per['person.email'] || per['person.email'] === 'email@kamu.com') todo.push(['Ganti email contoh dengan email aslimu', '#profil']);
        if (!per['person.whatsapp'] || per['person.whatsapp'] === '6281200000000') todo.push(['Isi nomor WhatsApp aslimu agar tombol WhatsApp berfungsi', '#profil']);
        if ((per['person.phone'] || '').includes('0000')) todo.push(['Ganti nomor HP contoh', '#profil']);
        if (!state.profile) todo.push(['Upload foto profil', '#profil']);
        const noImg = state.projects.filter((p) => !p.files.length);
        if (noImg.length) todo.push([`Upload gambar untuk ${noImg.length} proyek (masih memakai mockup): ${noImg.map((p) => p.data.title).join(', ')}`, '#proyek']);
        if (!state.gallery.length) todo.push(['Isi galeri arsip dengan logo, label, poster, dll.', '#galeri']);
        if ((per.social || []).some((s) => !s.href || s.href === '#')) todo.push(['Lengkapi link media sosial (masih "#")', '#profil']);
        if ((state.panels.testimoni.values.testimonials || []).some((t) => /Pak Wahyu|Bu Sri|Rina Kartika/.test(t.name))) todo.push(['Ganti testimoni contoh dengan testimoni asli (atau hapus)', '#testimoni']);

        const box = card('Yang perlu dilengkapi', todo.length ? 'Data contoh yang masih tampil di website.' : '');
        box.appendChild(todo.length
            ? h('ul', 'todo', todo.map(([t, href]) => `<li><a href="${href}"><i></i><span>${esc(t)}</span><b>Buka →</b></a></li>`).join(''))
            : h('p', 'done', 'Semua sudah lengkap. Portfolio siap dibagikan.'));
        view.appendChild(box);

        const quick = card('Pintasan', '');
        quick.appendChild(h('div', 'quick', `
            <a href="#proyek/baru"><b>+ Tambah proyek</b><span>Proyek baru dengan judul, cerita, dan gambar</span></a>
            <a href="#galeri"><b>Upload ke galeri</b><span>Logo, label, poster, feed IG</span></a>
            <a href="#hero"><b>Ubah judul hero</b><span>Teks besar di layar pembuka</span></a>`));
        view.appendChild(quick);
    };

    /* ---------- Proyek ---------- */
    const cover = (p) => (p.files[0] ? `<img src="${esc(p.files[0].url)}" alt="" loading="lazy">` : `<span class="pthumb__mock">${esc((state.projectFields.find((f) => f.key === 'mock')?.options || {})[p.data.mock] || 'Mockup')}</span>`);

    const viewProjects = () => {
        view.appendChild(head('Proyek', 'Studi kasus besar di bagian <b>Work</b>. Urutan di sini sama dengan urutan di website.'));

        const add = card('Tambah proyek baru', '');
        add.appendChild(h('form', 'addrow', `<input type="text" name="title" maxlength="80" placeholder="Judul proyek, mis. Kopi Senja Rebrand" required aria-label="Judul proyek baru"><button class="btn btn--accent" type="submit">+ Tambah proyek</button>`));
        $('form', add).addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = e.target.title.value.trim();
            if (!title) return;
            try {
                const s = await api('project_add', { title });
                state = s; updateBadges();
                toast('Proyek dibuat. Lengkapi isinya lalu upload gambar.');
                location.hash = '#proyek/' + s.slug;
            } catch (err) { toast(err.message, 'err'); }
        });
        view.appendChild(add);
        if (route === 'proyek/baru') setTimeout(() => $('input', add).focus(), 50);

        const list = h('ol', 'plist');
        state.projects.forEach((p, i) => {
            const li = h('li', 'prow', `
                <a class="prow__main" href="#proyek/${esc(p.slug)}">
                    <span class="pthumb">${cover(p)}</span>
                    <span class="prow__no">${String(i + 1).padStart(2, '0')}</span>
                    <span class="prow__text"><b>${esc(p.data.title)}</b><small>${esc(p.data.client)} · ${esc(p.data.category)} · ${esc(p.data.year)}</small></span>
                    <span class="pill ${p.files.length ? '' : 'pill--warn'}">${p.files.length ? p.files.length + ' gambar' : 'Belum ada gambar'}</span>
                </a>
                <div class="prow__tools">
                    <button type="button" data-move="-1" title="Naikkan" aria-label="Naikkan" ${i === 0 ? 'disabled' : ''}>↑</button>
                    <button type="button" data-move="1" title="Turunkan" aria-label="Turunkan" ${i === state.projects.length - 1 ? 'disabled' : ''}>↓</button>
                    <a href="#proyek/${esc(p.slug)}" class="btn btn--ghost btn--sm">Edit</a>
                    <button type="button" data-del class="danger" title="Hapus proyek" aria-label="Hapus proyek">✕</button>
                </div>`);
            $$('[data-move]', li).forEach((b) => b.addEventListener('click', async () => {
                try { setState(await api('project_move', { slug: p.slug, dir: Number(b.dataset.move) })); toast('Urutan disimpan.'); } catch (e) { toast(e.message, 'err'); }
            }));
            $('[data-del]', li).addEventListener('click', async () => {
                if (!confirm(`Hapus proyek "${p.data.title}"?\n\nGambarnya dipindahkan ke storage/trash/ dan masih bisa dikembalikan manual.`)) return;
                try { setState(await api('project_delete', { slug: p.slug })); toast('Proyek dihapus.'); } catch (e) { toast(e.message, 'err'); }
            });
            list.appendChild(li);
        });
        if (!state.projects.length) list.appendChild(h('li', 'thumbs__empty', 'Belum ada proyek. Tambahkan yang pertama di atas.'));
        const box = card('Semua proyek', '');
        box.appendChild(list);
        view.appendChild(box);
    };

    const viewProject = (slug) => {
        const p = state.projects.find((x) => x.slug === slug);
        if (!p) { location.hash = '#proyek'; return; }
        const n = state.projects.indexOf(p) + 1;
        view.appendChild(h('a', 'back', '← Semua proyek')).href = '#proyek';
        view.appendChild(head(p.data.title, `Proyek ${String(n).padStart(2, '0')} · folder gambar <code>assets/img/work/${esc(p.slug)}/</code>`, `<a class="btn btn--ghost" href="../#work" target="_blank" rel="noopener">Lihat di website ↗</a><a class="btn btn--ghost" href="../v3/project.php?p=${encodeURIComponent(p.slug)}" target="_blank" rel="noopener">Halaman detail /v3/ ↗</a>`));

        // Gambar
        const imgs = card('Gambar', 'Gambar pertama jadi gambar besar (cover), gambar ke-2 dan ke-3 tampil di sebelahnya, sisanya bisa dilihat saat diklik.');
        const area = h('div', 'imgarea');
        imgs.appendChild(area);
        mountImages(area, (el) => {
            const cur = state.projects.find((x) => x.slug === slug);
            if (!cur) return;
            el.appendChild(makeDrop((files, bar, node) => doUpload({ action: 'upload', target: cur.target }, files, bar, node)));
            el.appendChild(grid(cur.target, cur.files));
        });

        // Teks
        const txt = card('Teks proyek', '');
        const fields = h('div', 'fields');
        const mockBox = h('details', 'mockbox', '<summary>Mockup cadangan <small>tampil selama proyek belum punya gambar</small></summary><div class="fields"></div>');
        if (!p.files.length) mockBox.open = true;
        const built = state.projectFields.map((f) => {
            const b = buildField(f, getPath(p.data, f.key));
            (f.group === 'mock' ? $('.fields', mockBox) : fields).appendChild(b.el);
            return b;
        });
        txt.appendChild(fields);
        txt.appendChild(mockBox);
        const mockSel = built.find((b) => b.f.key === 'mock');
        const toggle = () => built.forEach((b) => { if (b.f.show) b.el.hidden = mockSel.get() !== b.f.show[1]; });
        $('select', mockSel.el).addEventListener('change', toggle);
        toggle();
        watch(txt);

        view.appendChild(imgs);
        view.appendChild(txt);
        view.appendChild(saveBar(async () => {
            const data = {};
            built.forEach((b) => setPath(data, b.f.key, b.get()));
            try {
                const s = await api('project_save', { slug, data });
                clearDirty(); setState(s); toast('Proyek tersimpan.');
            } catch (e) { toast(e.message, 'err'); }
        }));
    };

    /* ---------- Galeri ---------- */
    const viewGallery = () => {
        view.appendChild(head('Galeri arsip', 'Logo, label, poster, feed IG, dll. Keterangan dipakai sebagai judul gambar di website.'));
        const box = card('', '');
        box.appendChild(h('div', 'capfield', `
            <label for="gal-cap">Keterangan untuk gambar yang akan diupload <span>(opsional, mis. "Logo Kopi Senja")</span></label>
            <input id="gal-cap" type="text" maxlength="60" placeholder="Kosongkan untuk memakai nama file">`));
        const area = h('div', 'imgarea');
        box.appendChild(area);
        view.appendChild(box);
        mountImages(area, (el) => {
            el.appendChild(makeDrop((files, bar, node) => {
                const caption = $('#gal-cap').value.trim();
                doUpload({ action: 'upload', target: 'gallery', caption }, files, bar, node).then(() => { $('#gal-cap').value = ''; });
            }));
            el.appendChild(grid('gallery', state.gallery, { captions: true }));
        });
    };

    /* ---------- Pesan ---------- */
    const viewMessages = () => {
        view.appendChild(head('Pesan masuk', 'Pesan dari form kontak di website. Tersimpan di <code>storage/messages.jsonl</code>.'));
        const box = card('', '');
        if (!state.messages.length) box.appendChild(h('p', 'muted', 'Belum ada pesan. Saat pengunjung mengisi form kontak, pesannya muncul di sini.'));
        state.messages.forEach((m) => {
            const d = new Date(m.at);
            const mail = /^[^\s@]+@[^\s@]+$/.test(m.email || '') ? m.email : '';
            box.appendChild(h('article', 'msg', `
                <header><b>${esc(m.name)}</b>${mail ? `<a href="mailto:${esc(mail)}">${esc(mail)}</a>` : `<span>${esc(m.email)}</span>`}<time>${isNaN(d) ? '' : d.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</time></header>
                ${(m.topics || []).length || m.budget ? `<p class="msg__tags">${(m.topics || []).map((t) => `<span>${esc(t)}</span>`).join('')}${m.budget ? `<span class="is-budget">${esc(m.budget)}</span>` : ''}${m.deadline ? `<span>${esc(m.deadline)}</span>` : ''}</p>` : ''}
                ${m.phone ? `<p class="msg__tags"><span>☎ ${esc(m.phone)}</span></p>` : ''}
                <p class="msg__body">${esc(m.message)}</p>`));
        });
        view.appendChild(box);
    };

    /* ================================================================ */
    /* Router                                                           */
    /* ================================================================ */
    const updateBadges = () => {
        const b = $('[data-msg-count]');
        if (!b || !state) return;
        b.textContent = state.messages.length;
        b.hidden = !state.messages.length;
    };

    const render = () => {
        if (!state) return;
        imageMounts = [];
        const y = window.scrollY;
        const keep = view.dataset.route === route;
        view.innerHTML = '';
        view.dataset.route = route;
        const [top, sub] = route.split('/');
        $$('[data-route]').forEach((a) => a.classList.toggle('is-on', a.dataset.route === top));
        if (top === 'beranda') viewHome();
        else if (top === 'proyek' && sub && sub !== 'baru') viewProject(sub);
        else if (top === 'proyek') viewProjects();
        else if (top === 'galeri') viewGallery();
        else if (top === 'pesan') viewMessages();
        else if (top === 'profil') viewPanel('profil', viewProfilePhotos);
        else if (state.panels[top]) viewPanel(top);
        else { location.hash = '#beranda'; return; }
        window.scrollTo(0, keep ? y : 0);
    };

    const go = () => {
        const next = decodeURIComponent(location.hash.slice(1)) || 'beranda';
        if (next === route) return;
        if (dirty && !confirm('Ada perubahan yang belum disimpan. Tinggalkan halaman ini?')) {
            history.replaceState(null, '', '#' + route);
            return;
        }
        clearDirty();
        route = next;
        render();
    };
    window.addEventListener('hashchange', go);

    fetch('api.php?action=state', { credentials: 'same-origin' })
        .then((r) => { if (r.status === 401) location.reload(); return r.json(); })
        .then((s) => { state = s; updateBadges(); go(); })
        .catch(() => toast('Gagal memuat data.', 'err'));
})();
