<div
    x-data="{
        theme: 'light',

        init() {
            this.syncTheme()

            document.addEventListener('livewire:navigated', () => this.syncTheme())
        },

        syncTheme() {
            const savedTheme = localStorage.getItem('theme')

            this.theme = savedTheme === 'dark' ? 'dark' : 'light'
            localStorage.setItem('theme', this.theme)
            document.documentElement.classList.toggle('dark', this.theme === 'dark')
        },

        setTheme(selectedTheme) {
            this.theme = selectedTheme
            localStorage.setItem('theme', selectedTheme)
            document.documentElement.classList.toggle('dark', selectedTheme === 'dark')
            $dispatch('theme-changed', selectedTheme)

            if (typeof close === 'function') {
                close()
            }
        },
    }"
    class="fi-theme-switcher"
    role="group"
    aria-label="Pilih tema admin"
>
    <button
        aria-label="Gunakan tema putih"
        type="button"
        x-on:click="setTheme('light')"
        x-tooltip="{
            content: 'Putih',
            theme: $store.theme,
        }"
        x-bind:aria-pressed="theme === 'light' ? 'true' : 'false'"
        x-bind:class="{ 'fi-active': theme === 'light' }"
        class="fi-theme-switcher-btn"
    >
        {{
            \Filament\Support\generate_icon_html(
                \Filament\Support\Icons\Heroicon::Sun,
                alias: \Filament\View\PanelsIconAlias::THEME_SWITCHER_LIGHT_BUTTON,
            )
        }}

        <span class="sr-only">Putih</span>
    </button>

    <button
        aria-label="Gunakan tema hitam"
        type="button"
        x-on:click="setTheme('dark')"
        x-tooltip="{
            content: 'Hitam',
            theme: $store.theme,
        }"
        x-bind:aria-pressed="theme === 'dark' ? 'true' : 'false'"
        x-bind:class="{ 'fi-active': theme === 'dark' }"
        class="fi-theme-switcher-btn"
    >
        {{
            \Filament\Support\generate_icon_html(
                \Filament\Support\Icons\Heroicon::Moon,
                alias: \Filament\View\PanelsIconAlias::THEME_SWITCHER_DARK_BUTTON,
            )
        }}

        <span class="sr-only">Hitam</span>
    </button>
</div>
