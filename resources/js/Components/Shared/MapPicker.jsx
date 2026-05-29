import React, { useState, useEffect, useCallback, useMemo } from 'react';
import { MapContainer, TileLayer, Marker, Tooltip, useMapEvents, GeoJSON } from 'react-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-defaulticon-compatibility';
import 'leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.css';
import { MapPin } from 'lucide-react';

// ── Ikon kantor desa (oranye, berbeda dari marker biasa) ──────────────────────
const kantorDesaIcon = new L.DivIcon({
    html: `<div style="
        background: #ea580c;
        border: 3px solid white;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        width: 28px; height: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.35);
    "></div>`,
    iconSize: [28, 28],
    iconAnchor: [14, 28],
    className: '',
});

// Komponen untuk menangani klik pada peta
function ClickHandler({ setPosition }) {
    useMapEvents({
        click(e) {
            setPosition(e.latlng);
        },
    });
    return null;
}

/**
 * MapPicker
 *
 * Props:
 * - value           : { latitude, longitude } posisi marker yg bisa digeser user
 * - onChange        : callback saat user klik peta
 * - kantorDesa      : { lat, lng } koordinat kantor desa — tampil sebagai marker oranye tetap
 * - geojsonData     : objek GeoJSON langsung (FeatureCollection)
 * - geojsonUrl      : URL fallback jika geojsonData null (compat lama)
 * - height          : string CSS height
 */
export default function MapPicker({
    value = {},
    onChange,
    kantorDesa = null,
    geojsonData: geojsonDataProp = null,
    geojsonUrl = null,
    height = '400px',
}) {
    // Tentukan default center: prioritas value → kantorDesa → hardcode cibatu
    const defaultLat = parseFloat(value?.latitude) || kantorDesa?.lat || -6.5001403;
    const defaultLng = parseFloat(value?.longitude) || kantorDesa?.lng || 107.5342964;

    const [position, setPosition] = useState(
        value?.latitude && !isNaN(parseFloat(value.latitude))
            ? { lat: parseFloat(value.latitude), lng: parseFloat(value.longitude) }
            : { lat: defaultLat, lng: defaultLng }
    );
    const [geojsonData, setGeojsonData] = useState(geojsonDataProp);
    const [loadingGeojson, setLoadingGeojson] = useState(false);

    // Jika geojsonData prop berubah (dari parent), update
    useEffect(() => {
        if (geojsonDataProp) setGeojsonData(geojsonDataProp);
    }, [geojsonDataProp]);

    // Fallback: fetch dari URL jika tidak ada prop langsung
    useEffect(() => {
        if (!geojsonDataProp && geojsonUrl) {
            setLoadingGeojson(true);
            fetch(geojsonUrl)
                .then(res => res.json())
                .then(data => { setGeojsonData(data); setLoadingGeojson(false); })
                .catch(err => { console.error('Gagal memuat GeoJSON', err); setLoadingGeojson(false); });
        }
    }, [geojsonUrl, geojsonDataProp]);

    const handleSetPosition = useCallback((latlng) => {
        setPosition(latlng);
        if (onChange) onChange({ latitude: latlng.lat.toString(), longitude: latlng.lng.toString() });
    }, [onChange]);

    const geoJsonStyle = useMemo(() => ({
        color: '#16a34a',
        weight: 2.5,
        opacity: 0.85,
        fillColor: '#22c55e',
        fillOpacity: 0.12,
    }), []);

    // Tampilkan label nama wilayah dari properti GeoJSON jika ada
    const onEachFeature = useCallback((feature, layer) => {
        const nama = feature?.properties?.name
            || feature?.properties?.nama
            || feature?.properties?.NAMOBJ
            || feature?.properties?.DESA
            || null;
        if (nama) {
            layer.bindTooltip(nama, {
                permanent: false,
                direction: 'center',
                className: 'bg-white text-green-800 font-bold text-[10px] border-green-200 rounded-lg shadow',
            });
        }
    }, []);

    return (
        <div className="relative w-full rounded-2xl overflow-hidden shadow-sm border border-gray-200">
            {/* Loading indicator */}
            {loadingGeojson && (
                <div className="absolute top-4 left-1/2 -translate-x-1/2 z-[1000] bg-white/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-green-100 flex items-center gap-2">
                    <div className="w-4 h-4 border-2 border-green-600 border-t-transparent rounded-full animate-spin" />
                    <span className="text-[10px] font-black uppercase tracking-widest text-green-700">Memuat Batas Wilayah...</span>
                </div>
            )}

            {/* Legend */}
            {kantorDesa && (
                <div className="absolute top-3 right-3 z-[1000] bg-white/90 backdrop-blur-sm px-3 py-2 rounded-xl shadow border border-gray-100 space-y-1 text-[9px] font-bold uppercase tracking-widest text-gray-600">
                    <div className="flex items-center gap-1.5">
                        <span className="inline-block w-3 h-3 rounded-full bg-orange-500 border-2 border-white shadow" />
                        Kantor Desa
                    </div>
                    <div className="flex items-center gap-1.5">
                        <span className="inline-block w-3 h-3 rounded-full bg-blue-500 border-2 border-white shadow" />
                        Lokasi Dipilih
                    </div>
                    {geojsonData && (
                        <div className="flex items-center gap-1.5">
                            <span className="inline-block w-4 h-2 rounded bg-green-400 border border-green-600 opacity-70" />
                            Batas Wilayah
                        </div>
                    )}
                </div>
            )}

            {/* Hint */}
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 z-[1000] pointer-events-none">
                <div className="bg-gray-900/80 backdrop-blur-md text-white px-4 py-2 rounded-full shadow-lg border border-white/10 flex items-center gap-2">
                    <MapPin className="w-4 h-4 text-green-400" />
                    <span className="text-[10px] font-black uppercase tracking-widest">Klik area peta untuk menentukan lokasi</span>
                </div>
            </div>

            <MapContainer
                center={[defaultLat, defaultLng]}
                zoom={14}
                scrollWheelZoom={true}
                style={{ height, width: '100%', zIndex: 1 }}
            >
                <TileLayer
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />

                {/* GeoJSON batas wilayah */}
                {geojsonData && (
                    <GeoJSON
                        key={JSON.stringify(geojsonData).slice(0, 50)}
                        data={geojsonData}
                        style={geoJsonStyle}
                        onEachFeature={onEachFeature}
                    />
                )}

                {/* Marker kantor desa — tetap, tidak bisa digeser */}
                {kantorDesa && (
                    <Marker position={[kantorDesa.lat, kantorDesa.lng]} icon={kantorDesaIcon}>
                        <Tooltip permanent direction="top" offset={[0, -28]}
                            className="bg-orange-600 text-white text-[9px] font-black uppercase tracking-widest border-0 rounded-lg shadow px-2 py-1"
                        >
                            🏛️ Kantor Desa
                        </Tooltip>
                    </Marker>
                )}

                {/* Marker lokasi yang dipilih user */}
                <Marker position={position} />
                <ClickHandler setPosition={handleSetPosition} />
            </MapContainer>
        </div>
    );
}
