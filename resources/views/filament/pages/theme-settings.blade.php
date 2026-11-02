<x-filament-panels::page>
    <form wire:submit="save" class="fi-form">
        <div
            class="nc-theme-accordion"
            role="radiogroup"
            aria-label="{{ __('admin.pages.themes.active') }}"
        >
            @foreach ($this->themes as $slug => $theme)
                @php($isSelected = $slug === $this->activeTheme)
                @php($isLive = $slug === $this->liveTheme)

                <div class="nc-theme-item {{ $isSelected ? 'is-open' : '' }} {{ $isLive ? 'is-live' : '' }}">
                    <label class="nc-theme-header">
                        <input
                            type="radio"
                            class="nc-theme-radio"
                            name="theme_picker"
                            value="{{ $slug }}"
                            wire:model.live="activeTheme"
                            {{ $isSelected ? 'checked' : '' }}
                        />

                        <span class="nc-theme-name">{{ $theme->name() }}</span>

                        @if ($isLive)
                            <span class="nc-theme-badge">{{ __('admin.pages.themes.active') }}</span>
                        @endif

                        <span class="nc-theme-version">v{{ $theme->version() }}</span>

                        <svg
                            class="nc-theme-chevron"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.06l3.71-3.83a.75.75 0 1 1 1.08 1.04l-4.25 4.39a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </label>

                    <div class="nc-theme-body" @unless ($isSelected) hidden @endunless>
                        <p class="nc-theme-desc">{{ $theme->description() }}</p>

                        <dl class="nc-theme-meta-grid">
                            <div>
                                <dt>{{ __('admin.pages.themes.slug') }}</dt>
                                <dd><code>{{ $slug }}</code></dd>
                            </div>
                            <div>
                                <dt>{{ __('admin.pages.themes.locales') }}</dt>
                                <dd>{{ implode(' / ', array_map(strtoupper(...), $theme->locales())) }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('admin.pages.themes.version') }}</dt>
                                <dd>{{ $theme->version() }}</dd>
                            </div>
                            @if ($theme->author())
                                <div>
                                    <dt>{{ __('admin.pages.themes.author') }}</dt>
                                    <dd>{{ $theme->author() }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="fi-form-actions">
            <x-filament::button type="submit" color="primary">
                {{ __('admin.pages.themes.activate') }}
            </x-filament::button>
        </div>

        <details class="nc-theme-how">
            <summary>{{ __('admin.pages.themes.how') }}</summary>
            <div class="nc-theme-how-body">
                <p>
                    <strong>{{ __('admin.pages.themes.path') }}:</strong>
                    <code>{{ $this->themesPath }}</code>
                </p>
                <p>
                    <strong>{{ __('admin.pages.themes.resolved') }}:</strong>
                    <code>themes/{slug}/views</code> + <code>themes/{slug}/public</code>
                </p>
                <p>{!! __('admin.pages.themes.add_hint') !!}</p>
            </div>
        </details>
    </form>
</x-filament-panels::page>
