@extends('layout')
@section('title', 'Manage paste')
@section('content')
    <section class="pt-9 sm:pt-12 pb-8">
        <div class="text-xs font-extrabold uppercase tracking-[.16em] text-hive">Private management</div>
        <h1 class="my-4 text-4xl font-bold tracking-tight lg:text-5xl">Manage paste
            <span class="text-hive">.</span>
        </h1>
        <p class="text-zinc-400">Keep this page's URL secret. Anyone with the management link can edit or delete this paste.</p>
    </section>
    @if(session('status'))
        <div class="mb-5 rounded-lg border border-green-900 bg-green-950 p-4 text-green-200">{{ session('status') }}</div>
    @endif
    <div class="overflow-hidden rounded-2xl border border-hive-border bg-hive-surface shadow-xl">
        <div class="p-4 sm:p-6">
            <p>
                <strong>{{ $paste->title ?: 'Untitled paste' }}</strong> · {{ $paste->slug }}</p>
            <div class="flex flex-col gap-2">
                <label class="text-xs font-extrabold uppercase tracking-wider text-zinc-400" for="management-link">Your private management link</label>
                <input class="w-full rounded-lg border border-[#35302b] bg-[#0c0c0d] px-3 py-3 text-zinc-50 outline-none focus:border-hive" id="management-link" readonly value="{{ route('pastes.manage', ['paste' => $paste, 'key' => $secret]) }}">
            </div>
            <div class="mt-5 flex flex-wrap justify-between gap-3">
                <a class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" href="{{ route('pastes.show', $paste) }}">View public paste</a>
                <button class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300" type="button" onclick="navigator.clipboard.writeText(document.getElementById('management-link').value)">Copy management link</button>
                <a class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300" href="{{ route('pastes.edit', ['paste' => $paste, 'key' => $secret]) }}">Edit paste</a>
                <form method="POST" action="{{ route('pastes.destroy', $paste) }}" onsubmit="return confirm('Permanently delete this paste?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="management_key" value="{{ $secret }}">
                    <button class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-hive bg-hive px-5 py-3 text-sm font-bold text-[#101010] hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-300 border-[#39302a] bg-[#1c1917] text-zinc-300 border-red-900 text-rose-300" type="submit">Delete paste</button>
                </form>
            </div>
        </div>
    </div>
@endsection