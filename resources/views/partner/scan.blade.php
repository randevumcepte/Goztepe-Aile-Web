<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Göztepe Ayrıcalık · İşletme</title>
    <style>
        :root { --red:#D5102E; --deep:#6E0718; --gold:#F7B500; --ink:#1A1C1E; --soft:#5B6066; --bg:#F4F5F7; --green:#12805C; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; background:var(--bg); color:var(--ink); }
        header { background:linear-gradient(135deg,var(--red),var(--deep)); color:#fff; padding:18px 16px 22px; }
        header h1 { margin:0; font-size:20px; } header p { margin:4px 0 0; opacity:.85; font-size:13px; }
        main { padding:16px; max-width:520px; margin:0 auto; }
        .card { background:#fff; border-radius:16px; padding:16px; border:1px solid #E7E9EC; margin-bottom:14px; }
        label { display:block; font-weight:700; font-size:13px; margin:10px 0 6px; }
        input { width:100%; padding:14px; font-size:18px; border:1px solid #D0D4D9; border-radius:12px; letter-spacing:1px; }
        button { width:100%; padding:15px; border:0; border-radius:12px; font-size:16px; font-weight:800; cursor:pointer; margin-top:14px; }
        .btn { background:var(--red); color:#fff; } .btn-gold { background:var(--gold); color:var(--ink); } .btn-ghost { background:#fff; color:var(--soft); border:1px solid #D0D4D9; }
        .err { background:#FCE7EC; color:#9B0B22; padding:12px; border-radius:12px; font-size:14px; margin-bottom:10px; }
        #reader { border-radius:14px; overflow:hidden; }
        .res { display:none; } .res.show { display:block; }
        .ok { border:3px solid var(--green); } .no { border:3px solid var(--red); }
        .big { font-size:26px; font-weight:900; margin:4px 0; }
        .disc { display:inline-block; background:var(--gold); color:var(--ink); font-weight:900; font-size:20px; padding:8px 14px; border-radius:999px; margin:8px 0; }
        .stamps { font-size:26px; letter-spacing:4px; margin-top:6px; }
        .muted { color:var(--soft); font-size:13px; }
        .row { display:flex; gap:10px; } .row > div { flex:1; text-align:center; background:var(--bg); border-radius:12px; padding:10px; }
        .row b { display:block; font-size:22px; }
    </style>
</head>
<body>
<header>
    <h1>⚽ Göztepe Ayrıcalık</h1>
    <p>{{ $partner ? $partner->name.' · Üye kartı okutma' : 'Anlaşmalı işletme girişi' }}</p>
</header>
<main>
@if (! $partner)
    <form class="card" method="post" action="/isletme/giris">
        @csrf
        @if ($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
        <label for="code">İşletme kodu</label>
        <input id="code" name="code" value="{{ old('code') }}" autocapitalize="characters" autocomplete="off" required>
        <label for="pin">PIN</label>
        <input id="pin" name="pin" type="password" inputmode="numeric" autocomplete="off" required>
        <button class="btn" type="submit">Giriş yap</button>
        <p class="muted">Kod ve PIN'i Göztepe Tribünleri yönetimi verir. Bu sayfayı telefonunuzun ana ekranına ekleyebilirsiniz.</p>
    </form>
@else
    <div class="card">
        <div class="row">
            <div><b id="today">{{ $stats['today'] }}</b><span class="muted">bugün</span></div>
            <div><b>{{ $stats['visits'] }}</b><span class="muted">bu ay</span></div>
            <div><b>{{ $stats['members'] }}</b><span class="muted">farklı üye</span></div>
        </div>
    </div>

    <div class="card" id="scanCard">
        <div id="reader"></div>
        <p class="muted" style="text-align:center">Üyenin uygulamasındaki <b>Taraftar Kartı</b> QR kodunu kameraya gösterin.</p>
        <details>
            <summary class="muted">Kamera açılmıyor mu? Kodu elle girin</summary>
            <input id="manual" placeholder="GT1.…" autocomplete="off">
            <button class="btn-ghost" type="button" onclick="send(document.getElementById('manual').value)">Doğrula</button>
        </details>
    </div>

    <div class="card res" id="result"></div>

    <form method="post" action="/isletme/cikis">@csrf<button class="btn-ghost" type="submit">Çıkış</button></form>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const csrf = document.querySelector('meta[name=csrf-token]').content;
        const result = document.getElementById('result');
        let busy = false, scanner;

        function esc(s) { return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

        async function post(url, body) {
            const r = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf}, body:JSON.stringify(body) });
            if (r.status === 401 || r.status === 419) { location.reload(); return null; }
            return r.json();
        }

        async function send(qr) {
            if (busy || !qr) return;
            busy = true;
            try { scanner && scanner.pause(true); } catch (e) {}
            const d = await post('/isletme/okut', { qr });
            if (d) show(d);
        }

        function show(d) {
            result.className = 'card res show ' + (d.ok ? 'ok' : 'no');
            if (!d.ok) {
                result.innerHTML = `<div class="big">✗ İndirim yok</div>${d.name ? `<p>${esc(d.name)}</p>` : ''}<p>${esc(d.reason)}</p>
                    <button class="btn" onclick="next()">Yeni okutma</button>`;
                return;
            }
            document.getElementById('today').textContent = d.today;
            let stamps = '';
            if (d.stamp_goal) {
                const filled = Math.min(d.stamps, d.stamp_goal);
                stamps = `<div class="stamps">${'🟡'.repeat(filled)}${'⚪'.repeat(d.stamp_goal - filled)}</div>
                    <p class="muted">Damga ${filled}/${d.stamp_goal} · Hediye: ${esc(d.reward)}${d.stamp_given ? ' · bu ziyarette +1 damga' : ''}</p>
                    ${d.stamp_note ? `<p class="muted">${esc(d.stamp_note)}</p>` : ''}`;
            }
            result.innerHTML = `<div class="big">✓ Aktif üye</div><p><b>${esc(d.name)}</b> · ${esc(d.tier)}${d.member_no ? ' · No ' + esc(d.member_no) : ''}</p>
                ${d.discount ? `<div class="disc">${esc(d.discount)}</div><p class="muted">İndirimi hesaba uygulayın.</p>` : ''}
                ${stamps}
                ${d.reward_ready ? `<button class="btn-gold" onclick="reward('${esc(d.reward_ticket)}')">🎁 Hediyeyi teslim et: ${esc(d.reward)}</button>` : ''}
                <button class="btn" onclick="next()">Yeni okutma</button>`;
        }

        async function reward(ticket) {
            const d = await post('/isletme/hediye', { ticket });
            if (!d) return;
            result.className = 'card res show ' + (d.ok ? 'ok' : 'no');
            result.innerHTML = `<div class="big">${d.ok ? '🎁 Teslim edildi' : '✗'}</div><p>${esc(d.ok ? d.message : d.reason)}</p>
                <button class="btn" onclick="next()">Yeni okutma</button>`;
        }

        function next() {
            result.className = 'card res';
            document.getElementById('manual').value = '';
            busy = false;
            try { scanner && scanner.resume(); } catch (e) {}
        }

        try {
            scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: { width: 240, height: 240 }, rememberLastUsedCamera: true }, false);
            scanner.render(text => send(text), () => {});
        } catch (e) { console.warn(e); }
    </script>
@endif
</main>
</body>
</html>
