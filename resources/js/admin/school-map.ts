// Staff console map of directory schools (D-170). Data comes from the page as JSON.
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

type Point = {
    npsn: string;
    name: string;
    level: string;
    district: string;
    regency: string;
    customer: boolean;
    lat: number;
    lng: number;
};

type MapData = {
    points: Point[];
    labels: { customer: string; prospect: string; count: string };
};

const COLORS = { customer: '#0F766E', prospect: '#D97706' }; // --brand and amber, as in the legend

function popup(point: Point, labels: MapData['labels']): HTMLElement {
    // Built with textContent: names come from an outside site.
    const box = document.createElement('div');
    const lines: [string, string][] = [
        ['strong', point.name],
        ['span', `NPSN ${point.npsn} · ${point.level}`],
        ['span', `${point.district}, ${point.regency}`],
        ['span', point.customer ? labels.customer : labels.prospect],
    ];
    for (const [tag, text] of lines) {
        const line = document.createElement(tag);
        line.textContent = text;
        line.style.display = 'block';
        box.append(line);
    }
    return box;
}

function start(): void {
    const element = document.getElementById('school-map');
    const source = document.getElementById('school-map-data');
    if (!element || !source || element.dataset.ready) {
        return;
    }
    element.dataset.ready = '1';

    const data = JSON.parse(source.textContent ?? '{}') as MapData;
    const regencyFilter = document.querySelector<HTMLSelectElement>('[data-map-filter="regency"]');
    const statusFilter = document.querySelector<HTMLSelectElement>('[data-map-filter="status"]');
    const count = document.querySelector<HTMLElement>('[data-map-count]');

    const map = L.map(element, { preferCanvas: true }).setView([-6.8, 110.85], 10);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);
    const layer = L.layerGroup().addTo(map);

    function render(fit: boolean): void {
        const regency = regencyFilter?.value ?? '';
        const status = statusFilter?.value ?? '';
        const shown = data.points.filter((point) => (regency === '' || point.regency === regency)
            && (status === '' || (status === 'customer') === point.customer));

        layer.clearLayers();
        for (const point of shown) {
            L.circleMarker([point.lat, point.lng], {
                radius: point.customer ? 7 : 5,
                color: '#ffffff',
                weight: 1,
                fillColor: point.customer ? COLORS.customer : COLORS.prospect,
                fillOpacity: 0.9,
            }).bindPopup(() => popup(point, data.labels)).addTo(layer);
        }

        if (count) {
            count.textContent = data.labels.count
                .replace(':shown', shown.length.toLocaleString('id-ID'))
                .replace(':customers', shown.filter((point) => point.customer).length.toLocaleString('id-ID'));
        }
        if (fit && shown.length > 0) {
            map.fitBounds(L.latLngBounds(shown.map((point) => [point.lat, point.lng])), { padding: [24, 24] });
        }
    }

    regencyFilter?.addEventListener('change', () => render(true));
    statusFilter?.addEventListener('change', () => render(false));
    render(true);
}

start();
document.addEventListener('livewire:navigated', start);
