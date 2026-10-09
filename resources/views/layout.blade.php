<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="dark">
        <meta name="referrer" content="no-referrer">
        @php
            $isPublicLanding = request()->routeIs('home');
            $isSharedPaste = request()->routeIs('pastes.show') && isset($paste);
            $pageTitle = $isSharedPaste ? (($paste->title ?: 'Shared paste').' - HivePaste') : ($isPublicLanding ? 'HivePaste - Share code and logs instantly' : 'HivePaste');
            $pageDescription = $isSharedPaste
                ? 'View a shared '.strtoupper($paste->language).' snippet on HivePaste. Share code and logs with a simple link.'
                : 'Share code snippets and logs with unique links, syntax highlighting, expiration options and optional IP redaction. Powered by HivePaste.';
            $pageUrl = $isSharedPaste ? route('pastes.show', $paste) : ($isPublicLanding ? route('home') : url()->current());
        @endphp
        <meta name="description" content="{{ $pageDescription }}">
        <meta name="robots" content="{{ $isPublicLanding ? 'index,follow,max-image-preview:large' : 'noindex,nofollow,noarchive' }}">
        <link rel="canonical" href="{{ $pageUrl }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="HivePaste">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ $pageUrl }}">
        <meta property="og:image" content="{{ asset('assets/hivepaste-social.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="HivePaste - Share code and logs">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $pageDescription }}">
        <meta name="twitter:image" content="{{ asset('assets/hivepaste-social.png') }}">
        <meta name="theme-color" content="#090909">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <title>{{ $pageTitle }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/hivepaste.css') }}">
    </head>
    <body>
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[100] focus:rounded-lg focus:bg-hive focus:px-4 focus:py-2 focus:text-black">Skip to content</a>
        <header class="sticky top-0 z-50 border-b border-white/5 bg-[#0a0a0b]/95 backdrop-blur-xl">
            <div class="mx-auto flex h-[76px] w-full max-w-screen-2xl items-center justify-between gap-5 px-4 sm:px-8">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center gap-2 text-lg font-bold tracking-tight sm:gap-3 sm:text-xl">
                    <img
                        src="{{ asset('HiveIcon.png') }}"
                        alt=""
                        aria-hidden="true"
                        class="size-9 shrink-0 object-contain sm:size-10"
                        width="40"
                        height="40"
                    >

                    <span>
                        Hive<span class="text-hive">Paste</span>
                    </span>
                </a>
                <nav aria-label="Main navigation" class="flex min-w-0 items-center gap-2 text-xs font-semibold sm:gap-7 sm:text-sm">
                    <a href="{{ route('home') }}" class="hidden text-zinc-400 transition-colors hover:text-white min-[420px]:inline-flex">New paste</a>
                    <a href="https://hivepanel.dev" target="_blank" rel="noopener noreferrer" class="hidden text-zinc-400 transition-colors hover:text-white sm:inline-flex">HivePanel</a>
                    <a href="https://hivepanel.dev" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-hive px-3 py-2.5 font-bold text-black transition-colors hover:bg-orange-400 sm:px-4">Hive ecosystem ↗</a>
                </nav>
            </div>
        </header>
        <main id="main-content" class="mx-auto w-full max-w-screen-2xl px-3 sm:px-8">
            @yield('content')
        </main>
        <footer class="mx-auto mt-14 flex w-full max-w-screen-2xl flex-col justify-between gap-3 border-t border-white/10 px-4 py-8 text-xs text-zinc-500 sm:flex-row sm:px-8">
            <span>© {{ date('Y') }} HivePaste · A Hive ecosystem service</span>
            <span class="flex flex-wrap gap-x-4 gap-y-2"><a class="hover:text-hive" href="{{ route('privacy') }}">Privacy</a><a class="hover:text-hive" href="{{ route('terms') }}">Terms</a><a class="hover:text-hive" href="{{ route('abuse') }}">Report abuse</a></span>
        </footer>
    </body>
</html>
