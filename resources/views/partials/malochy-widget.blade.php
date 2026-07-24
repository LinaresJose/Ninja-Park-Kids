{{-- ╔══════════════════════════════════════════════════════════╗ --}}
{{-- ║  MALOCHY CHATBOT WIDGET — Ninja Park Kids              ║ --}}
{{-- ║  Modo: public | staff (pasado desde el layout)         ║ --}}
{{-- ╚══════════════════════════════════════════════════════════╝ --}}
@php
    $isStaff     = isset($modo) && $modo === 'staff';
    $chatEndpoint = $isStaff ? route('malochy.staff.chat') : route('malochy.chat');
    $staffName   = $isStaff && Auth::check() ? Auth::user()->nombre : '';
@endphp

<style>
/* ── BASE ─────────────────────────────────────────────────── */
:root {
    --mal-green-dark:  #14532d;
    --mal-green:       #16a34a;
    --mal-green-light: #22c55e;
    --mal-green-bg:    #dcfce7;
    --mal-bubble-user: #166534;
    --mal-bubble-bot:  #f0fdf4;
    --mal-text-dark:   #0f172a;
    --mal-text-muted:  #64748b;
    --mal-shadow:      0 20px 60px rgba(0,0,0,0.18);
    --mal-radius:      20px;
}

#malochy-widget { position: fixed; bottom: 28px; right: 28px; z-index: 9999; font-family: 'Segoe UI', system-ui, sans-serif; }

/* ── BURBUJA FLOTANTE ─────────────────────────────────────── */
#malochy-bubble {
    width: 72px; height: 72px;
    background: rgba(22, 163, 74, 0.18);
    border-radius: 50%; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
    position: relative;
    overflow: visible;
    padding: 0;
    animation: mal-pulse 3s ease-in-out infinite;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 20px rgba(22,163,74,0.25);
}
#malochy-bubble:hover { transform: scale(1.08); }
#malochy-bubble.open  { animation: none; transform: scale(1.05); }

@keyframes mal-pulse {
    0%,100% { box-shadow: 0 4px 20px rgba(22,163,74,0.25); }
    50%      { box-shadow: 0 4px 28px rgba(22,163,74,0.45), 0 0 0 8px rgba(22,163,74,0.08); }
}

#malochy-badge {
    position: absolute; top: -2px; right: -2px;
    background: #ef4444; color: #fff; border-radius: 50%;
    width: 22px; height: 22px; font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff; animation: mal-bounce 0.5s ease infinite alternate;
    z-index: 2;
}
@keyframes mal-bounce { from { transform: scale(1); } to { transform: scale(1.2); } }

/* ── PANEL DE CHAT ────────────────────────────────────────── */
#malochy-panel {
    position: absolute; bottom: 80px; right: 0;
    width: 370px; max-height: 560px;
    background: #fff; border-radius: var(--mal-radius);
    box-shadow: var(--mal-shadow);
    display: flex; flex-direction: column; overflow: hidden;
    transform-origin: bottom right;
    transform: scale(0.85) translateY(20px);
    opacity: 0; pointer-events: none;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease;
}
#malochy-panel.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: all; }

/* ── HEADER ───────────────────────────────────────────────── */
.mal-header {
    background: linear-gradient(135deg, var(--mal-green-dark) 0%, var(--mal-green) 100%);
    padding: 16px 18px; display: flex; align-items: center; gap: 12px;
    flex-shrink: 0;
}
.mal-avatar-wrap {
    display: flex; align-items: center;
    justify-content: center; flex-shrink: 0;
}
.mal-header-info { flex: 1; }
.mal-header-name  { color: #fff; font-weight: 700; font-size: 15px; letter-spacing: 0.3px; }
.mal-header-status { display: flex; align-items: center; gap: 5px; margin-top: 2px; }
.mal-header-dot    { width: 7px; height: 7px; border-radius: 50%; background: #86efac; flex-shrink: 0; animation: mal-blink 2s ease infinite; }
@keyframes mal-blink { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
.mal-header-status span { color: rgba(255,255,255,0.85); font-size: 12px; }
.mal-mode-badge {
    background: rgba(255,255,255,0.2); color: #fff; font-size: 10px;
    font-weight: 700; padding: 3px 8px; border-radius: 20px; white-space: nowrap;
}
.mal-close-btn {
    background: rgba(255,255,255,0.15); border: none; color: #fff;
    border-radius: 50%; width: 30px; height: 30px; cursor: pointer;
    font-size: 16px; display: flex; align-items: center; justify-content: center;
    transition: background 0.2s; flex-shrink: 0;
}
.mal-close-btn:hover { background: rgba(255,255,255,0.3); }

/* ── MENSAJES ─────────────────────────────────────────────── */
#malochy-messages {
    flex: 1; overflow-y: auto; padding: 16px 14px;
    display: flex; flex-direction: column; gap: 10px;
    background: #fafafa;
    scrollbar-width: thin; scrollbar-color: #d1fae5 transparent;
}
#malochy-messages::-webkit-scrollbar { width: 4px; }
#malochy-messages::-webkit-scrollbar-track { background: transparent; }
#malochy-messages::-webkit-scrollbar-thumb { background: #bbf7d0; border-radius: 4px; }

.mal-msg { display: flex; gap: 8px; animation: mal-fadeup 0.3s ease; }
@keyframes mal-fadeup { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: none; } }

.mal-msg.bot  { align-items: flex-end; }
.mal-msg.user { align-items: flex-end; flex-direction: row-reverse; }

.mal-msg-icon {
    width: 32px; flex-shrink: 0;
    display: flex; align-items: flex-end; justify-content: center;
}
.mal-msg-icon img { width: 32px; height: auto; object-fit: contain; display: block; }

.mal-bubble {
    max-width: 82%; padding: 10px 14px; border-radius: 16px;
    font-size: 13.5px; line-height: 1.55; word-break: break-word;
}
.mal-msg.bot  .mal-bubble { background: #fff; color: var(--mal-text-dark); border-bottom-left-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
.mal-msg.user .mal-bubble { background: var(--mal-bubble-user); color: #fff; border-bottom-right-radius: 4px; }

.mal-bubble strong { font-weight: 700; }
.mal-bubble a { color: var(--mal-green); text-decoration: underline; }
.mal-msg.user .mal-bubble a { color: #a7f3d0; }

.mal-link-btn {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 8px; padding: 8px 14px;
    background: linear-gradient(135deg, var(--mal-green-dark), var(--mal-green));
    color: #fff !important; text-decoration: none !important;
    border-radius: 10px; font-size: 13px; font-weight: 600;
    transition: opacity 0.2s, transform 0.2s;
}
.mal-link-btn:hover { opacity: 0.9; transform: translateY(-1px); }

.mal-typing {
    display: flex; align-items: center; gap: 5px;
    padding: 10px 14px; background: #fff; border-radius: 16px;
    border-bottom-left-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
.mal-typing span { width: 7px; height: 7px; border-radius: 50%; background: var(--mal-green); display: inline-block; animation: mal-type 1.2s ease infinite; }
.mal-typing span:nth-child(2) { animation-delay: 0.2s; }
.mal-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes mal-type { 0%,80%,100%{transform:scale(0.6);opacity:0.4;} 40%{transform:scale(1);opacity:1;} }

/* ── QUICK REPLIES ────────────────────────────────────────── */
#malochy-quick {
    padding: 8px 14px; display: flex; flex-wrap: wrap; gap: 6px;
    border-top: 1px solid #f0fdf4; background: #fafafa; flex-shrink: 0;
}
.mal-quick-btn {
    padding: 6px 13px; background: var(--mal-green-bg);
    color: var(--mal-green-dark); border: 1.5px solid #bbf7d0;
    border-radius: 20px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.mal-quick-btn:hover { background: var(--mal-green); color: #fff; border-color: var(--mal-green); transform: translateY(-1px); }

/* ── INPUT ────────────────────────────────────────────────── */
.mal-input-area {
    padding: 12px 14px; display: flex; gap: 8px; align-items: center;
    border-top: 1px solid #e2e8f0; background: #fff; flex-shrink: 0;
}
#malochy-input {
    flex: 1; border: 1.5px solid #e2e8f0; border-radius: 12px;
    padding: 9px 14px; font-size: 13.5px; outline: none;
    transition: border-color 0.2s;
}
#malochy-input:focus { border-color: var(--mal-green); }
#malochy-send {
    width: 38px; height: 38px; border-radius: 50%; border: none;
    background: linear-gradient(135deg, var(--mal-green-dark), var(--mal-green));
    color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: transform 0.2s, opacity 0.2s; flex-shrink: 0;
}
#malochy-send:hover { transform: scale(1.1); }
#malochy-send:active { transform: scale(0.95); }
#malochy-send svg { width: 16px; height: 16px; }

/* ── RESPONSIVE ───────────────────────────────────────────── */
@media (max-width: 480px) {
    #malochy-widget { bottom: 16px; right: 16px; }
    #malochy-panel  { width: calc(100vw - 32px); right: -16px; max-height: 480px; }
}
</style>

<div id="malochy-widget">

    {{-- BURBUJA ──────────────────────────────────────────── --}}
    <button id="malochy-bubble" onclick="malochyToggle()" aria-label="Abrir chat con Malochy" title="Malochy — Asistente Ninja">
        <img src="{{ asset('img/avatar-caricatura.png') }}" alt="Malochy" style="width:95px;height:110px;object-fit:contain;display:block;position:relative;z-index:1;">
        <span id="malochy-badge">1</span>
    </button>

    {{-- PANEL ────────────────────────────────────────────── --}}
    <div id="malochy-panel" role="dialog" aria-label="Chat con Malochy">

        {{-- Header --}}
        <div class="mal-header">
            <div class="mal-avatar-wrap">
                <img src="{{ asset('img/avatar-caricatura.png') }}" alt="Malochy" style="width:46px;height:auto;object-fit:contain;display:block;">
            </div>
            <div class="mal-header-info">
                <div class="mal-header-name">Malochy 🥷</div>
                <div class="mal-header-status">
                    <div class="mal-header-dot"></div>
                    <span>En línea · Asistente Ninja</span>
                </div>
            </div>
            @if($isStaff)
                <div class="mal-mode-badge">🔐 STAFF</div>
            @endif
            <button class="mal-close-btn" onclick="malochyToggle()" aria-label="Cerrar chat">✕</button>
        </div>

        {{-- Mensajes --}}
        <div id="malochy-messages"></div>

        {{-- Quick replies --}}
        <div id="malochy-quick"></div>

        {{-- Input --}}
        <div class="mal-input-area">
            <input type="text" id="malochy-input" placeholder="Escribe aquí..." autocomplete="off"
                   onkeydown="if(event.key==='Enter') malochySend()">
            <button id="malochy-send" onclick="malochySend()" aria-label="Enviar mensaje">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    /* ── CONFIG ───────────────────────────────────────────── */
    const MODO           = '{{ $isStaff ? "staff" : "public" }}';
    const ENDPOINT       = '{{ $chatEndpoint }}';
    const VERIFY_EP      = '{{ route("malochy.verificar") }}';
    const CSRF           = '{{ csrf_token() }}';
    const STAFF_NAME     = '{{ $staffName }}';
    const AVATAR_URL     = '{{ asset("img/avatar-caricatura.png") }}';

    /* ── STATE ────────────────────────────────────────────── */
    let isOpen           = false;
    let awaitingCedula   = MODO === 'public';

    /* ── DOM ──────────────────────────────────────────────── */
    const panel   = document.getElementById('malochy-panel');
    const bubble  = document.getElementById('malochy-bubble');
    const badge   = document.getElementById('malochy-badge');
    const msgs    = document.getElementById('malochy-messages');
    const quick   = document.getElementById('malochy-quick');
    const input   = document.getElementById('malochy-input');

    /* ── TOGGLE ───────────────────────────────────────────── */
    window.malochyToggle = function () {
        isOpen = !isOpen;
        panel.classList.toggle('open', isOpen);
        bubble.classList.toggle('open', isOpen);
        if (isOpen) {
            badge.style.display = 'none';
            if (msgs.children.length === 0) malochyInit();
            setTimeout(() => input.focus(), 350);
        }
    };

    /* ── INIT: Mensaje de bienvenida ──────────────────────── */
    function malochyInit() {
        if (MODO === 'staff') {
            const name = STAFF_NAME ? `, **${STAFF_NAME}**` : '';
            appendBot(
                `¡Hola${name}! Soy **Malochy** en modo Analítico 🔐\n\n¿Qué datos necesitas hoy?`,
                ['Afluencia hoy', 'Afluencia esta semana', 'Horas pico', 'Buscar cliente']
            );
        } else {
            appendBot(
                '¡Hola! Soy **Malochy**, el asistente ninja de Ninja Park Kids 🥷\n\nPara poder ayudarte mejor, ¿cuál es tu número de **cédula de identidad**?'
            );
        }
    }

    /* ── ENVIAR MENSAJE ───────────────────────────────────── */
    window.malochySend = function () {
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        clearQuick();
        appendUser(text);

        if (awaitingCedula) {
            // Extraemos la cédula del mensaje
            const match = text.match(/\d{6,10}/);
            if (match) {
                awaitingCedula = false;
                postBot(VERIFY_EP, { cedula: match[0] });
            } else {
                appendBot('Por favor ingresa solo el número de tu cédula (ej: 12345678).');
            }
            return;
        }

        postBot(ENDPOINT, { message: text });
    };

    /* ── FETCH ────────────────────────────────────────────── */
    function postBot(url, body) {
        showTyping();
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            removeTyping();
            appendBot(data.message || '...', data.quickReplies || []);
            if (data.link) appendLink(data.link.url, data.link.label);
        })
        .catch(() => {
            removeTyping();
            appendBot('⚠️ Hubo un error de conexión. Intenta de nuevo en un momento.');
        });
    }

    /* ── RENDER ───────────────────────────────────────────── */
    function appendUser(text) {
        const div = document.createElement('div');
        div.className = 'mal-msg user';
        div.innerHTML = `<div class="mal-bubble">${escHtml(text)}</div>`;
        msgs.appendChild(div);
        scrollBot();
    }

    function appendBot(text, quickReplies = []) {
        const div = document.createElement('div');
        div.className = 'mal-msg bot';
        div.innerHTML = `
            <div class="mal-msg-icon">
                <img src="{{ asset('img/avatar-caricatura.png') }}" alt="Malochy">
            </div>
            <div class="mal-bubble">${formatMd(text)}</div>`;
        msgs.appendChild(div);
        setQuick(quickReplies);
        scrollBot();
    }

    function appendLink(url, label) {
        const last = msgs.querySelector('.mal-msg.bot:last-child .mal-bubble');
        if (!last) return;
        const a = document.createElement('a');
        a.href = url; a.target = '_blank'; a.rel = 'noopener noreferrer';
        a.className = 'mal-link-btn';
        a.innerHTML = `${escHtml(label)} <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>`;
        last.appendChild(document.createElement('br'));
        last.appendChild(a);
        scrollBot();
    }

    function showTyping() {
        const div = document.createElement('div');
        div.className = 'mal-msg bot'; div.id = 'mal-typing-row';
        div.innerHTML = '<div class="mal-msg-icon"><img src="' + AVATAR_URL + '" alt="Malochy"></div><div class="mal-typing"><span></span><span></span><span></span></div>';
        msgs.appendChild(div);
        scrollBot();
    }

    function removeTyping() {
        const t = document.getElementById('mal-typing-row');
        if (t) t.remove();
    }

    const DEFAULT_PUBLIC_REPLIES = ['Ver Tarifas', 'Ver Horarios', 'Promociones', 'Reservar', 'Redes Sociales'];
    const DEFAULT_STAFF_REPLIES = ['Afluencia hoy', 'Afluencia esta semana', 'Horas pico', 'Buscar cliente'];

    function setQuick(replies) {
        if (awaitingCedula) {
            quick.innerHTML = '';
            return;
        }

        if (!replies || replies.length === 0) {
            replies = MODO === 'staff' ? DEFAULT_STAFF_REPLIES : DEFAULT_PUBLIC_REPLIES;
        }

        quick.innerHTML = '';
        replies.forEach(r => {
            const btn = document.createElement('button');
            btn.className = 'mal-quick-btn';
            btn.textContent = r;
            btn.onclick = () => { input.value = r; malochySend(); };
            quick.appendChild(btn);
        });
    }

    function clearQuick() { quick.innerHTML = ''; }

    function scrollBot() { setTimeout(() => msgs.scrollTop = msgs.scrollHeight, 50); }

    /* ── UTILS ────────────────────────────────────────────── */
    function escHtml(t) {
        return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function formatMd(text) {
        return escHtml(text)
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
    }

})();
</script>
