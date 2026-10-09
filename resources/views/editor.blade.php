@extends('layout')
@section('title', isset($paste) ? 'Edit paste' : 'New paste')
@section('content')
    @php
        $editing = isset($paste);
        $languages = ['text'=>'Plain text','log'=>'Log','json'=>'JSON','javascript'=>'JavaScript','typescript'=>'TypeScript','php'=>'PHP','python'=>'Python','java'=>'Java','yaml'=>'YAML','bash'=>'Bash','sql'=>'SQL','html'=>'HTML','css'=>'CSS','go'=>'Go','rust'=>'Rust','xml'=>'XML','dockerfile'=>'Dockerfile'];
        $content = old('content', $editing ? $paste->content : '');
        $greetings = [
            'Your code, beautifully shared',
            'Hello, world',
            'Got logs? Drop them here',
            'Paste it. Share it. Done',
            'Less explaining, more pasting',
            'Something broken? Let\'s see it',
            'Another day, another stack trace',
            'Welcome back, developer',
            'Keep calm and paste your logs',
            'From your terminal to the world',
            'A home for your snippets',
            'Sharing code made simple',
        ];
        $greeting = $greetings[array_rand($greetings)];
    @endphp
    <section class="pt-9 sm:pt-12 pb-8">
        <div class="text-xs font-extrabold uppercase tracking-[.16em] text-hive">{{ $editing ? 'Manage your snippet' : 'Share code & logs' }}</div>
        <h1 class="my-4 text-4xl font-bold tracking-tight lg:text-5xl">{{ $editing ? 'Edit your paste' : $greeting }}<span class="text-hive">.</span></h1>
        <p class="text-zinc-400">{{ $editing ? 'Update the content and settings of this paste.' : 'Create a paste, share the link, and get back to building.' }}</p>
    </section>
    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-900 bg-red-950 p-4 text-red-200">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ $editing ? route('pastes.update', $paste) : route('pastes.store') }}">
        @csrf
        @if($editing)
            @method('PUT')
            <input type="hidden" name="management_key" value="{{ $secret }}">
        @endif
        <div class="overflow-hidden rounded-2xl border border-hive-border bg-hive-surface shadow-xl">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-hive-border px-6 py-4">
                <strong>{{ $editing ? 'Edit paste' : 'New paste' }}</strong>
                <span class="rounded-full border border-[#403329] px-3 py-1 text-xs text-zinc-400">Maximum {{ number_format(config('hivepaste.max_paste_bytes') / 1024) }} KB</span>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="title">Title (optional)</label>
                        <input class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" id="title" name="title" maxlength="120" value="{{ old('title', $editing ? $paste->title : '') }}" placeholder="My server log">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="language">Language</label>
                        <select class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" id="language" name="language">
                            @foreach($languages as $key=>$name)
                                <option value="{{ $key }}" @selected(old('language', $editing ? $paste->language : 'text')===$key)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="expires_in">Expires after update</label>
                        <select class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" id="expires_in" name="expires_in">
                            @foreach(array_filter(['1h'=>'1 hour','1d'=>'1 day','7d'=>'7 days','30d'=>'30 days','never'=>config('hivepaste.allow_never_expire') ? 'Never' : null]) as $key=>$name)
                                <option value="{{ $key }}" @selected(old('expires_in', config('hivepaste.default_expiration'))===$key)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-[18px] flex flex-col gap-2">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400">
                        Sharing
                    </label>

                    <div class="rounded-lg border border-[#35302b] bg-[#0c0c0d] px-4 py-3">
                        <p class="text-sm font-semibold text-zinc-100">
                            Unlisted - anyone with the link
                        </p>
                        <p class="mt-1 text-xs text-zinc-400">
                            A unique UUID link will be generated automatically.
                            Your paste won't appear in a public directory.
                        </p>
                    </div>

                    <input type="hidden" name="visibility" value="unlisted">
                </div>
                <div class="flex flex-col gap-2 mt-5">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="content">Paste content</label>
                    <div class="overflow-hidden rounded-xl border border-[#35302b] bg-[#0c0c0d]">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-hive-border px-4 py-3 text-xs text-zinc-400">
                            <span id="editor-info">1 line · 0 characters</span>
                            <span>Tab inserts spaces · Ctrl/⌘ + Enter to save</span>
                        </div>
                        <div id="textarea-editor" class="flex h-[clamp(320px,48vh,520px)] min-w-0 overflow-auto">
                            <div id="line-gutter" class="sticky left-0 min-w-12 select-none bg-[#151313] px-3 py-4 text-right font-mono text-sm leading-relaxed whitespace-pre text-zinc-500" aria-hidden="true">1</div>
                            <textarea class="m-0 min-h-full min-w-0 flex-1 resize-none rounded-none border-0 bg-transparent p-4 font-mono text-sm leading-relaxed whitespace-pre text-zinc-50 outline-none [tab-size:4]" id="content" name="content" aria-describedby="editor-info" autocapitalize="off" autocomplete="off" autocorrect="off" required spellcheck="false" placeholder="Paste your code, console output, or logs here...">{{ $content }}</textarea>
                        </div>
                        <div id="codemirror-editor" class="hidden min-w-0 overflow-hidden text-sm" aria-label="Code editor"></div>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button type="button" id="preview-redaction" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300">Preview IP redaction</button>
                    <span id="redaction-status" class="text-sm text-zinc-400" role="status" aria-live="polite">
                    </span>
                </div>
                <div id="redaction-preview-panel" class="mt-3 hidden rounded-xl border border-[#35302b] bg-[#0c0c0d] p-4">
                    <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
                        <strong class="text-sm">Redaction preview (not saved)</strong>
                        <button type="button" id="apply-redaction" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300">Apply to editor</button>
                    </div>
                    <pre id="redaction-preview-content" class="max-h-80 overflow-auto whitespace-pre-wrap break-words font-mono text-sm text-zinc-200">
                    </pre>
                </div>
                <div class="mt-5 flex flex-wrap items-start gap-3 rounded-xl border border-[#35302b] bg-[#101011] p-4">
                    <input type="hidden" name="redact_ips" value="0">
                    <input class="mt-1 h-4 w-4 shrink-0 accent-hive" id="redact_ips" name="redact_ips" type="checkbox" value="1" @checked(old('redact_ips', '1') == '1')>
                    <div>
                        <label class="cursor-pointer text-sm font-semibold text-zinc-100" for="redact_ips">Redact IP addresses before publishing</label>
                        <p class="text-zinc-400 text-xs mt-1">Replaces valid IPv4 and IPv6 addresses with [REDACTED IP], including private addresses. Applies when creating or saving this paste. Review your content for other sensitive information.</p>
                    </div>
                </div>
                @if(!$editing && config('hivepaste.turnstile_site_key'))
                    <div class="flex flex-col gap-2 mt-[18px]">
                        <div class="cf-turnstile" data-sitekey="{{ config('hivepaste.turnstile_site_key') }}">
                        </div>
                    </div>
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                @endif
                <p class="text-zinc-400 mt-2">Before sharing logs, remove passwords, tokens, private keys and personal information. Common credential patterns are checked automatically when enabled by the host.</p>
                <div class="mt-5 flex flex-wrap justify-end gap-3">
                    @if($editing)
                        <a class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" href="{{ route('pastes.manage', ['paste' => $paste, 'key' => $secret]) }}">Cancel</a>
                    @endif
                    <button class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300" type="submit">{{ $editing ? 'Save changes' : 'Create paste' }} →</button>
                </div>
            </div>
        </div>
    </form>
    <script defer src="{{ asset('assets/hivepaste-editor.js') }}"></script>
    <script>
    (() => {
        const input = document.getElementById('content'), gutter = document.getElementById('line-gutter'), info = document.getElementById('editor-info');
        const getContent = () => window.hivepasteEditor?.getValue() ?? input.value;
        const refresh = () => { const lines = getContent().split('\n').length; gutter.textContent = Array.from({length:lines}, (_,i)=>i+1).join('\n'); info.textContent = `${lines} lines · ${getContent().length.toLocaleString()} characters`; };
        input.addEventListener('input', refresh);
        document.addEventListener('hivepaste:editor-change', refresh);
        input.addEventListener('scroll', () => { gutter.style.transform = `translateY(-${input.scrollTop}px)`; });
        input.addEventListener('keydown', e => {
        if (e.key === 'Tab') { e.preventDefault(); const a=input.selectionStart,b=input.selectionEnd;input.setRangeText('    ',a,b,'end');refresh(); }
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault();input.form.requestSubmit(); }
        });
        const previewButton = document.getElementById('preview-redaction');
        const status = document.getElementById('redaction-status');
        const previewPanel = document.getElementById('redaction-preview-panel');
        const previewContent = document.getElementById('redaction-preview-content');
        let previewOriginal = null;
        let previewResult = null;
        previewButton.addEventListener('click', async () => {
        previewButton.disabled = true;
        status.textContent = 'Checking addresses…';
        previewPanel.classList.add('hidden');
        try {
        const response = await fetch(@json(route('pastes.redaction-preview')), {
        method: 'POST', headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value},
        body: JSON.stringify({content:getContent()}), credentials:'same-origin'
        });
        if (!response.ok) throw new Error(response.status === 422 ? 'Enter content within the paste size limit.' : 'Preview unavailable. Try again.');
        const data = await response.json();
        previewOriginal = getContent();
        previewResult = data.content;
        previewContent.textContent = data.content;
        previewPanel.classList.remove('hidden');
        status.textContent = data.changed ? 'Potential IP addresses found.' : 'No valid IP addresses detected.';
        } catch (error) { status.textContent = error.message; }
        finally { previewButton.disabled = false; }
        });
        document.getElementById('apply-redaction').addEventListener('click', () => {
        if (previewOriginal === null || getContent() !== previewOriginal) { status.textContent = 'Content changed. Preview again before applying.'; return; }
        if (window.hivepasteEditor) window.hivepasteEditor.setValue(previewResult);
        input.value = previewResult;
        refresh();
        previewPanel.classList.add('hidden');
        status.textContent = 'Redaction applied to editor.';
        });
        refresh();
    })();
    </script>
@endsection