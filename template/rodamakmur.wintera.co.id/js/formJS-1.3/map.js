var geocoder, placesService;

if (!window.maps) window.maps = {};
if (!window.markers) window.markers = {};
if (!window.geocoders) window.geocoders = {};
if (!window.placesServices) window.placesServices = {};

async function initMap(mode, latLng) {
    try {
        const { Map } = await google.maps.importLibrary("maps");
        const { PlacesService } = await google.maps.importLibrary("places");
        const { Geocoder } = await google.maps.importLibrary("geocoding");

        const mapElement = document.getElementById(mode === "add" ? "add" : mode);
        if (!mapElement) {
            console.warn(`Elemen map ${mode} tidak ditemukan.`);
            return;
        }

        // console.log(`Inisialisasi peta untuk mode: ${mode}`);

        const map = new Map(mapElement, {
            zoom: 12,
            center: { lat: -6.1751, lng: 106.8650 }, // Jakarta
            mapId: "DEMO_MAP_ID",
        });

        window.maps[mode] = map;
        window.markers[mode] = [];

        window.geocoders[mode] = new Geocoder();
        window.placesServices[mode] = new PlacesService(map);

        map.addListener("click", (event) => {
            handleMapClick(event.latLng, mode);
        });

        // kalau ada latLng dari DB
        if (latLng) {
            let lat, lng;

            if (typeof latLng === "string" && latLng.includes(",")) {
                const parts = latLng.split(",");
                lat = parseFloat(parts[0]);
                lng = parseFloat(parts[1]);
            } else if (typeof latLng === "object" && "lat" in latLng && "lng" in latLng) {
                lat = parseFloat(latLng.lat);
                lng = parseFloat(latLng.lng);
            }

            if (!isNaN(lat) && !isNaN(lng)) {
                const position = new google.maps.LatLng(lat, lng);

                addMarker(position, `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`, "selected", null, mode);
                map.setCenter(position);
                map.setZoom(15);

                // update hidden input
                document.querySelectorAll(`[data-map='${mode}'][name='hidLatLngEdit[]']`)
                    .forEach((input) => {
                        input.value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
                    });
            }
        } else {
            // kalau gak ada latLng dari DB default jkt 
            const defaultPos = { lat: -6.1751, lng: 106.8650 };
            map.setCenter(defaultPos);
            map.setZoom(12);
        }
    } catch (error) {
        showError("map", "Error loading map: " + error.message);
    }
}

async function geocodeAddress(addressInput, mode) {
    const geocoder = window.geocoders[mode];
    if (!geocoder) {
        console.error(`Geocoder untuk mode ${mode} belum diinisialisasi`);
        return null;
    }

    const addressElement = document.getElementById(`address-geocoding-${mode}`);
    if (!addressElement) {
        console.error(`Element address-geocoding-${mode} tidak ditemukan`);
        showError(`geocoding-results-${mode}`, 'Element alamat tidak ditemukan!');
        return null;
    }

    const address = addressElement.value.trim();
    // console.log(`Geocoding untuk mode: ${mode}, alamat: ${address}`);

    if (!address) {
        showError(`geocoding-results-${mode}`, 'Masukkan alamat terlebih dahulu!');
        return null;
    }

    clearMarkers(mode);

    try {
        const response = await new Promise((resolve, reject) => {
            geocoder.geocode({
                address: address,
                region: 'id',
                language: 'id'
            }, (results, status) => {
                if (status === 'OK') resolve(results);
                else reject(new Error(status));
            });
        });

        if (response.length > 0) {
            const location = response[0].geometry.location;
            addMarker(location, response[0].formatted_address, 'geocoding', null, mode);

            window.maps[mode].setCenter(location);
            window.maps[mode].setZoom(15);

            const geocodedLocation = {
                lat: location.lat(),
                lng: location.lng(),
                address: response[0].formatted_address
            };

            // update hidden input
            document.querySelectorAll(`[data-map='${mode}'][name='hidLatLngEdit[]']`)
                .forEach(input => input.value = `${geocodedLocation.lat.toFixed(6)},${geocodedLocation.lng.toFixed(6)}`);
            document.querySelectorAll(`[name='hidLatLngAdd[]']`)
                .forEach(input => input.value = `${geocodedLocation.lat.toFixed(6)},${geocodedLocation.lng.toFixed(6)}`);
        }

        return response;
    } catch (error) {
        // console.error('Geocoding error:', error);
        showError('','Alamat tidak ditemukan');
        return null;
    }
}


function handleMapClick(location, mode) {
    const lat = location.lat();
    const lng = location.lng();

    // hidden input update sesuai map
    const hidLatLngEdit = document.querySelectorAll(`[data-map='${mode}'][name='hidLatLngEdit[]']`);
    hidLatLngEdit.forEach((input) => {
        input.value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
    });
    const hidLatLngAdd = document.querySelectorAll(`[name='hidLatLngAdd[]']`);
    hidLatLngAdd.forEach((input) => {
        input.value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
    });

    // console.log(`Koordinat (${mode}): ${lat}, ${lng}`);

    clearMarkers(mode);
    addMarker(location, `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`, 'selected', null, mode);
}

function addMarker(location, title, type = 'default', place = null, mode) {
    if (!window.maps || !window.maps[mode]) {
        // console.error(`Map untuk mode "${mode}" belum diinisialisasi.`);
        return null;
    }

    const marker = new google.maps.Marker({
        position: location,
        map: window.maps[mode],
        title: title,
        icon: getMarkerIcon(type)
    });

    if (place) {
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="max-width: 200px;">
                    <h4>${place.name}</h4>
                    <p>${place.vicinity || place.formatted_address || ''}</p>
                </div>
            `
        });

        marker.addListener('click', () => {
            infoWindow.open(window.maps[mode], marker);
        });
    }

    window.markers[mode].push(marker);
    return marker;
}

function clearMarkers(mode) {
    if (window.markers && window.markers[mode]) {
        window.markers[mode].forEach(marker => marker.setMap(null));
        window.markers[mode] = [];
    }
}

function getMarkerIcon(type) {

    // varian warna
    //  'reverse': 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
    //  'place': 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
    //  'nearby': 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
    //  'center': 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png'
    const icons = {
        'geocoding': 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
    };
    return icons[type] || null;
}

function showError(targetId, message) {
    const el = document.getElementById(targetId);
    if (el) {
        el.innerHTML = message;
    } else {
        console.warn(`showError: element ${targetId} tidak ditemukan. Pesan: ${message}`);
        alert(message); // fallback
    }
}