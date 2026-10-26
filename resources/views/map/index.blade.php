@php
    $type = request('type', 'all');
    $mapLocations = $locations->map(fn ($location) => [
        'id' => $location->id,
        'name' => $location->name,
        'type' => $location->type,
        'description' => $location->description,
        'address' => $location->address,
        'phone' => $location->phone,
        'latitude' => (float) $location->latitude,
        'longitude' => (float) $location->longitude,
    ])->values();
@endphp

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQ3z1U5H0l7K0bV8d8QfQ7jIY5jS4=" crossorigin="">
    <style>#pet-map { min-height: 28rem; }</style>
@endpush

<x-app-layout :title="__('ui.map.title')" :description="__('ui.map.subtitle')">
    <div class="container-page py-10 sm:py-14">
        <div class="max-w-3xl">
            <p class="eyebrow">{{ __('ui.nav.map') }}</p>
            <h1 class="mt-2 text-4xl">{{ __('ui.map.title') }}</h1>
            <p class="mt-3 text-ink-muted">{{ __('ui.map.subtitle') }}</p>
        </div>

        <div class="mt-8 flex flex-wrap gap-3" role="group" aria-label="{{ __('ui.map.type') }}">
            @foreach (['all' => 'all', 'shop' => 'shop', 'vet' => 'vet', 'pet_friendly' => 'pet_friendly'] as $value => $label)
                <a href="{{ route('map', ['type' => $value]) }}" @class([
                    'rounded-full px-4 py-2 text-sm font-semibold transition',
                    'bg-primary-700 text-white' => $type === $value,
                    'bg-white text-primary-800 ring-1 ring-line hover:bg-primary-50' => $type !== $value,
                ])>{{ __('ui.map.'.$label) }}</a>
            @endforeach
            <button type="button" id="near-me" class="rounded-full bg-accent-400 px-4 py-2 text-sm font-semibold text-primary-950 transition hover:bg-accent-300">
                {{ __('ui.map.near_me') }}
            </button>
        </div>
        <p id="map-status" class="mt-3 min-h-6 text-sm text-ink-muted" role="status"></p>

        <div class="mt-4 grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(18rem,0.75fr)]">
            <div id="pet-map" class="overflow-hidden rounded-3xl shadow-card ring-1 ring-line" aria-label="{{ __('ui.map.title') }}"></div>
            <section>
                <h2 class="mb-3 text-xl">{{ __('ui.map.list') }}</h2>
                <div id="location-list" class="space-y-3">
                    @forelse ($locations as $location)
                        <button type="button" class="location-card block w-full rounded-2xl bg-white p-4 text-start shadow-card ring-1 ring-line transition hover:-translate-y-0.5 hover:ring-primary-300" data-location-id="{{ $location->id }}">
                            <span class="text-xs font-bold uppercase tracking-wide text-primary-700">{{ __('ui.map.'.$location->type) }}</span>
                            <span class="mt-1 block font-heading text-lg font-bold text-primary-950">{{ $location->name }}</span>
                            <span class="mt-1 block text-sm text-ink-muted">{{ $location->address }}</span>
                            @if ($location->phone)<span class="mt-2 block text-sm text-primary-700">{{ $location->phone }}</span>@endif
                        </button>
                    @empty
                        <x-empty-state :title="__('ui.map.empty')" />
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (() => {
            const locations = @json($mapLocations);
            const map = L.map('pet-map').setView([10.7769, 106.7009], 12);
            const markers = new Map();
            const list = document.getElementById('location-list');
            const status = document.getElementById('map-status');
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[character]));
            const label = (location) => `<strong>${escapeHtml(location.name)}</strong><br>${escapeHtml(location.address)}${location.phone ? `<br>${escapeHtml(location.phone)}` : ''}`;
            const bounds = [];

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            locations.forEach((location) => {
                const marker = L.marker([location.latitude, location.longitude]).addTo(map).bindPopup(label(location));
                markers.set(String(location.id), marker);
                bounds.push([location.latitude, location.longitude]);
            });
            if (bounds.length) map.fitBounds(bounds, { padding: [24, 24] });

            document.querySelectorAll('.location-card').forEach((card) => {
                card.addEventListener('click', () => {
                    const marker = markers.get(card.dataset.locationId);
                    if (!marker) return;
                    map.setView(marker.getLatLng(), 15);
                    marker.openPopup();
                });
            });

            document.getElementById('near-me').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    status.textContent = @json(__('ui.map.location_unavailable'));
                    return;
                }
                status.textContent = '';
                navigator.geolocation.getCurrentPosition((position) => {
                    const user = [position.coords.latitude, position.coords.longitude];
                    L.circleMarker(user, { radius: 8, color: '#166534', fillColor: '#86efac', fillOpacity: 0.9 }).addTo(map).bindPopup(@json(__('ui.map.near_me'))).openPopup();
                    const distance = (location) => {
                        const lat = (location.latitude - user[0]) * 111;
                        const lng = (location.longitude - user[1]) * 111 * Math.cos(user[0] * Math.PI / 180);
                        return Math.sqrt(lat * lat + lng * lng);
                    };
                    [...list.querySelectorAll('.location-card')].sort((a, b) => {
                        const left = locations.find((location) => String(location.id) === a.dataset.locationId);
                        const right = locations.find((location) => String(location.id) === b.dataset.locationId);
                        return distance(left) - distance(right);
                    }).forEach((card) => list.appendChild(card));
                    map.setView(user, 13);
                    status.textContent = @json(__('ui.map.list')).concat(': ', locations.length);
                }, (error) => {
                    status.textContent = error.code === 1
                        ? @json(__('ui.map.permission_denied'))
                        : @json(__('ui.map.location_unavailable'));
                });
            });
        })();
    </script>
@endpush
