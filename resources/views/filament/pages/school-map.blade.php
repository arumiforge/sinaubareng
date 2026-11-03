{{-- Inline styles: the console's CSS only holds Filament's own classes. --}}
<x-filament-panels::page>
    @vite('resources/js/admin/school-map.ts')

    <div style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem;">
        <label style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 14rem;">
            <span class="fi-fo-field-label-content">{{ __('console.directory.fields.region') }}</span>
            <x-filament::input.wrapper>
                <x-filament::input.select data-map-filter="regency">
                    <option value="">{{ __('console.map.all') }}</option>
                    @foreach ($this->regencies() as $regency)
                        <option value="{{ $regency }}">{{ $regency }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>
        <label style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 14rem;">
            <span class="fi-fo-field-label-content">{{ __('console.map.show') }}</span>
            <x-filament::input.wrapper>
                <x-filament::input.select data-map-filter="status">
                    <option value="">{{ __('console.map.all') }}</option>
                    <option value="customer">{{ __('console.directory.filters.customers') }}</option>
                    <option value="prospect">{{ __('console.directory.filters.not_customers') }}</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>
        <p data-map-count aria-live="polite" style="margin: 0 0 0.5rem;"></p>
    </div>

    <div wire:ignore id="school-map" role="region" aria-label="{{ __('console.map.title') }}"
        style="height: 70vh; min-height: 22rem; width: 100%; border-radius: 0.75rem; overflow: hidden; z-index: 0;"></div>

    <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem;">
        <span style="display: flex; align-items: center; gap: 0.5rem;"><span style="display: inline-block; width: 0.75rem; height: 0.75rem; border-radius: 9999px; background: #0F766E;"></span>{{ __('console.directory.filters.customers') }}</span>
        <span style="display: flex; align-items: center; gap: 0.5rem;"><span style="display: inline-block; width: 0.75rem; height: 0.75rem; border-radius: 9999px; background: #D97706;"></span>{{ __('console.directory.filters.not_customers') }}</span>
        @if ($this->missingLocations() > 0)
            <span>{{ trans_choice('console.map.missing_locations', $this->missingLocations(), ['count' => $this->missingLocations()]) }}</span>
        @endif
    </div>

    <script type="application/json" id="school-map-data">@json($this->mapData())</script>
</x-filament-panels::page>
