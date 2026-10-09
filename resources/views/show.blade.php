@extends('layout')
@section('title', $paste->title ?: 'Paste '.$paste->slug)
@section('content')
    <section class="pt-9 sm:pt-12 pb-8">
        <div class="text-xs font-extrabold uppercase tracking-[.16em] text-hive">Shared paste</div>
        <h1 class="my-4 text-4xl font-bold tracking-tight lg:text-5xl">{{ $paste->title ?: 'Untitled paste' }}
            <span class="text-hive">.</span>
        </h1>
        <p class="text-zinc-400">{{ strtoupper($paste->language) }} · {{ $paste->created_at->diffForHumans() }} · {{ $paste->expires_at?->diffForHumans() ?? 'Never expires' }} · {{ ucfirst($paste->visibility) }}</p>
    </section>
    @if(session('status'))
        <div class="mb-5 rounded-lg border border-green-900 bg-green-950 p-4 text-green-200">{{ session('status') }}</div>
    @endif
    <div class="overflow-hidden rounded-2xl border border-hive-border bg-hive-surface shadow-xl">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-hive-border px-6 py-4">
            <strong>⬡ {{ $paste->slug }}</strong>
            <div class="flex flex-wrap gap-2">
                <a class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" href="{{ route('pastes.raw', $paste) }}">Raw</a>
                <a class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" href="{{ route('pastes.download', $paste) }}">Download</a>
                <button class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" type="button" id="copy-content">Copy content</button>
                <button class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" type="button" id="copy-link">Copy link</button>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-hive-border px-4 py-3 text-xs text-zinc-400">
            <span id="view-info">
            </span>
            <span>Read-only · {{ strtoupper($paste->language) }}</span>
        </div>
        <div class="flex max-h-[80vh] overflow-auto bg-[#0c0c0d]">
            <div id="view-gutter" class="sticky left-0 min-w-12 select-none bg-[#151313] px-3 py-4 text-right font-mono text-sm leading-relaxed whitespace-pre text-zinc-500" aria-hidden="true">
            </div>
            <pre class="m-0 max-h-none min-w-0 flex-1 overflow-auto bg-[#0c0c0d] p-4 font-mono text-sm leading-relaxed whitespace-pre">
                <code id="highlighted">
                </code>
            </pre>
        </div>
    </div>
    @if(session('management_key'))
        <div class="overflow-hidden rounded-2xl border border-hive-border bg-hive-surface shadow-xl mt-5">
            <div class="p-4 sm:p-6">
                <strong>Save your private management link</strong>
                <p class="text-zinc-400">This link lets anyone holding it edit or delete the paste. It is shown only once. Never share it alongside the public paste URL.</p>
                <input class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" id="management-link" readonly value="{{ route('pastes.manage', ['paste' => $paste, 'key' => session('management_key')]) }}" aria-label="Private management link">
                <div class="mt-5 flex flex-wrap justify-end gap-3">
                    <button type="button" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300" onclick="navigator.clipboard.writeText(document.getElementById('management-link').value)">Copy management link</button>
                </div>
            </div>
        </div>
    @endif
    <div class="mt-5 flex flex-wrap justify-between gap-3">
        <a href="{{ route('home') }}" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300">+ New paste</a>
    </div>
    <details class="overflow-hidden rounded-2xl border border-hive-border bg-hive-surface shadow-xl mt-5">
        <summary class="flex flex-wrap items-center justify-between gap-4 border-b border-hive-border px-6 py-4 cursor-pointer">Report this paste
        </summary>
        <div class="p-4 sm:p-6">
            <p class="text-zinc-400">Reports are stored for the host administrator to review. Submitting a report does not automatically remove a paste.</p>
            @if($errors->any())
                <div class="mb-5 rounded-lg border border-red-900 bg-red-950 p-4 text-red-200">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('pastes.report', $paste) }}">
                @csrf
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="report-reason">Reason</label>
                    <select class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" name="reason" id="report-reason" required>
                        <option value="spam">Spam</option>
                        <option value="malware">Malware or phishing</option>
                        <option value="personal_information">Personal information</option>
                        <option value="illegal_content">Illegal content</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2 mt-3">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="report-details">Details (optional)</label>
                    <textarea class="w-full min-h-24 resize-y rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-sm text-zinc-50 outline-none focus:border-hive" name="details" id="report-details" maxlength="1000" rows="3">
                    </textarea>
                </div>
                <div class="hidden" aria-hidden="true">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="website">Leave empty</label>
                    <input class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>
                @if(config('hivepaste.turnstile_site_key'))
                    <div class="mt-[14px] cf-turnstile" data-sitekey="{{ config('hivepaste.turnstile_site_key') }}">
                    </div>
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                @endif
                <div class="mt-5 flex flex-wrap justify-end gap-3">
                    <button class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" type="submit">Submit report</button>
                </div>
            </form>
        </div>
    </details>
    <script defer src="{{ asset('assets/hivepaste-viewer.js') }}"></script>
    <script type="application/json" id="paste-data">@json(['content' => $paste->content, 'language' => $paste->language], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
    <script>
    (() => {
        const {content, language} = JSON.parse(document.getElementById('paste-data').textContent);
        const code = document.getElementById('highlighted');
        const gutter = document.getElementById('view-gutter');
        const lines = content.split('\n').length;
        gutter.textContent = Array.from({length:lines},(_,i)=>i+1).join('\n');
        document.getElementById('view-info').textContent = `${lines} lines · ${content.length.toLocaleString()} characters`;
        // Escape all user input before adding highlighting markup.
        const escape = s => s.replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const tokens = /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\b(?:true|false|null|const|let|var|function|return|class|public|private|if|else|for|while|import|export|def|async|await|package|func|type|struct)\b|\b\d+(?:\.\d+)?\b)/g;
        if (['text','log'].includes(language)) { code.textContent = content; }
        else {
            code.innerHTML = content.split(tokens).map((part, index) => {
            if (index % 2 === 0) return escape(part);
            const kind = /^['"]/.test(part) ? 'string' : /^\d/.test(part) ? 'number' : 'keyword';
            return `<span class="${kind === 'string' ? 'text-green-300' : kind === 'number' ? 'text-orange-300' : 'text-purple-300'}">${escape(part)}</span>`;
            }).join('');
        }
        async function copy(text, button) { try { await navigator.clipboard.writeText(text); const old=button.textContent;button.textContent='Copied!';setTimeout(()=>button.textContent=old,1600); } catch { alert('Clipboard access unavailable.'); } }
        document.getElementById('copy-content').addEventListener('click',e=>copy(content,e.currentTarget));
        document.getElementById('copy-link').addEventListener('click',e=>copy(location.href,e.currentTarget));
    })();
    </script>
@endsection