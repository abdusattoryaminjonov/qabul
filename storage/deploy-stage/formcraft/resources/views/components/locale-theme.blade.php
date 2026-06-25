@props(['showTheme' => true])

<div class="flex items-center gap-2" @if($showTheme) x-data="{
    dark: document.documentElement.classList.contains('dark'),
    toggleTheme() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
    }
}" @endif>
    <div class="locale-switcher flex rounded-lg overflow-hidden border border-[var(--fc-border)] text-xs font-semibold">
        @foreach(['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
           class="px-2.5 py-1.5 transition {{ app()->getLocale() === $code ? 'bg-violet-600 text-white' : 'bg-[var(--fc-card)] text-[var(--fc-text-muted)] hover:bg-[var(--fc-hover)]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($showTheme)
    <button type="button" @click="toggleTheme()"
        class="p-2 rounded-lg border border-[var(--fc-border)] bg-[var(--fc-card)] text-[var(--fc-text-muted)] hover:text-violet-500 transition"
        :title="dark ? '{{ __('app.theme.light') }}' : '{{ __('app.theme.dark') }}'">
        <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg x-show="dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>
    @endif
</div>
