var geocoder, placesService;

if (!window.maps) window.maps = {};
if (!window.markers) window.markers = {};
if (!window.geocoders) window.geocoders = {};
if (!window.placesServices) window.placesServices = {};

async function initMap(mode, latLng) {
    try {
        const {
            Map
        } = await google.maps.importLibrary("maps");
        const {
            PlacesService
        } = await google.maps.importLibrary("places");
        const {
            Geocoder
        } = await google.maps.importLibrary("geocoding");
        const {
            Autocomplete
        } = await google.maps.importLibrary("places");

        const mapElement = document.getElementById(mode === "add" ? "add" : mode);
        if (!mapElement) {
            console.warn(`Elemen map ${mode} tidak ditemukan.`);
            return;
        }


        const map = new Map(mapElement, {
            zoom: 12,
            center: {
                lat: -6.1751,
                lng: 106.8650
            }, // Jakarta
            mapId: "DEMO_MAP_ID",
        });

        window.maps[mode] = map;
        window.markers[mode] = [];

        window.geocoders[mode] = new Geocoder();
        window.placesServices[mode] = new PlacesService(map);

        // Initialize autocomplete for address input
        initAddressAutocomplete(mode);

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

                // Reverse geocode untuk mendapatkan alamat
                reverseGeocode(position, mode);
            }
        } else {
            // kalau gak ada latLng dari DB default jkt 
            const defaultPos = {
                lat: -6.1751,
                lng: 106.8650
            };
            map.setCenter(defaultPos);
            map.setZoom(12);
        }
    } catch (error) {
        showError("map", "Error loading map: " + error.message);
    }
}

function parseAddressComponents(addressComponents) {
    const components = {
        street_number: '',
        route: '',           // nama jalan
        sublocality: '',     // kelurahan
        administrative_area_level_4: '', // kecamatan
        administrative_area_level_3: '', // kabupaten
        administrative_area_level_2: '', // kota
        administrative_area_level_1: '', // provinsi
        postal_code: '',     // kode pos
        country: ''
    };

    addressComponents.forEach(component => {
        const types = component.types;
        const longName = component.long_name;
        
        if (types.includes('street_number')) {
            components.street_number = longName;
        } else if (types.includes('route')) {
            components.route = longName;
        } else if (types.includes('sublocality_level_1') || types.includes('sublocality')) {
            components.sublocality = longName;
        } else if (types.includes('administrative_area_level_4')) {
            components.administrative_area_level_4 = longName;
        } else if (types.includes('administrative_area_level_3')) {
            components.administrative_area_level_3 = longName;
        } else if (types.includes('administrative_area_level_2') || types.includes('locality')) {
            components.administrative_area_level_2 = longName;
        } else if (types.includes('administrative_area_level_1')) {
            components.administrative_area_level_1 = longName;
        } else if (types.includes('postal_code')) {
            components.postal_code = longName;
        } else if (types.includes('country')) {
            components.country = longName;
        }
    });

    return components;
}

function formatParsedAddress(components) {
    const streetAddress = [components.route, components.street_number]
        .filter(item => item)
        .join(' ');
    
    const city = components.administrative_area_level_2 || 
                 components.administrative_area_level_3 || 
                 components.sublocality || '';
    
    return {
        street: streetAddress,
        subdistrict: components.administrative_area_level_4 || components.sublocality,
        district: components.administrative_area_level_3,
        city: city,
        province: components.administrative_area_level_1,
        postal_code: components.postal_code,
        country: components.country,
        full_address: [
            streetAddress,
            components.sublocality,
            components.administrative_area_level_4,
            components.administrative_area_level_3,
            components.administrative_area_level_2,
            components.administrative_area_level_1,
            components.postal_code
        ].filter(item => item).join(', ')
    };
}

function updateAddressFields(parsedAddress, mode) {
    
    // Update individual fields
    const customFieldMappings = {
        [`for-map`]: parsedAddress.street + ', ' + parsedAddress.subdistrict + ', ' + parsedAddress.district + ', ' + parsedAddress.city + ', ' + parsedAddress.province + ', ' + parsedAddress.postal_code
    }

    // Update fields
    Object.entries(customFieldMappings).forEach(([fieldId, value]) => {
        const field = document.getElementById(fieldId);
        if (field && value) {
            field.innerHTML = value;
        }
    });
}

function initAddressAutocomplete(mode) {
    const addressInput = document.getElementById(`address-geocoding-${mode}`);
    if (!addressInput) {
        console.warn(`Address input untuk mode ${mode} tidak ditemukan`);
        return;
    }


    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
        componentRestrictions: { country: 'id' }, // Restrict to Indonesia
        fields: ['formatted_address', 'geometry', 'name', 'place_id', 'address_components'],
        types: ['geocode'] // Focus on addresses
    });

    // Store autocomplete instance
    if (!window.autocompletes) window.autocompletes = {};
    window.autocompletes[mode] = autocomplete;

    // Handle place selection
    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) {
            showError(`geocoding-results-${mode}`, 'Alamat tidak valid!');
            return;
        }

            // Jika ada button close terpisah

        // Clear existing markers
        clearMarkers(mode);

        const location = place.geometry.location;
        
        // Add marker
        addMarker(location, place.formatted_address, 'geocoding', null, mode);

        // Center map
        window.maps[mode].setCenter(location);
        window.maps[mode].setZoom(15);

        // Update hidden inputs
        updateHiddenInputs(location.lat(), location.lng(), mode);

        // Parse komponen alamat dari autocomplete
        if (place.address_components) {
            const addressComponents = parseAddressComponents(place.address_components);
            const parsedAddress = formatParsedAddress(addressComponents);
            
            // Update individual address fields
            updateAddressFields(parsedAddress, mode);
        
        }
    });
}

async function reverseGeocode(location, mode) {
    const geocoder = window.geocoders[mode];
    if (!geocoder) {
        console.error(`Geocoder untuk mode ${mode} belum diinisialisasi`);
        return;
    }


    try {
        const response = await new Promise((resolve, reject) => {
            geocoder.geocode({
                location: location,
                language: 'id'
            }, (results, status) => {
                if (status === 'OK') resolve(results);
                else reject(new Error(status));
            });
        });

        if (response && response.length > 0) {
            const result = response[0];
            const address = result.formatted_address;
            
            
            // Parse komponen alamat
            const addressComponents = parseAddressComponents(result.address_components);
            const parsedAddress = formatParsedAddress(addressComponents);
            
            // Update address input field
            const addressInput = document.getElementById(`address-geocoding-${mode}`);
            if (addressInput) {
                addressInput.value = address;
            }

            // Update individual address fields
            updateAddressFields(parsedAddress, mode);
            
            return { address, parsedAddress };
        }
    } catch (error) {
        console.error('Reverse geocoding error:', error);
        showError(`geocoding-results-${mode}`, 'Tidak dapat menemukan alamat untuk koordinat ini');
    }
    return null;
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
            const result = response[0];
            const location = result.geometry.location;
            addMarker(location, result.formatted_address, 'geocoding', null, mode);

            window.maps[mode].setCenter(location);
            window.maps[mode].setZoom(15);

            const geocodedLocation = {
                lat: location.lat(),
                lng: location.lng(),
                address: result.formatted_address
            };

            // Update hidden inputs
            updateHiddenInputs(geocodedLocation.lat, geocodedLocation.lng, mode);
            
            // Parse komponen alamat dari geocoding result
            if (result.address_components) {
                const addressComponents = parseAddressComponents(result.address_components);
                const parsedAddress = formatParsedAddress(addressComponents);
                // Update individual address fields
                updateAddressFields(parsedAddress, mode);
  
            }
        }

        return response;
    } catch (error) {
        console.error('Geocoding error:', error);
        showError(`geocoding-results-${mode}`, 'Alamat tidak ditemukan');
        return null;
    }
}

function handleMapClick(location, mode) {
    const lat = location.lat();
    const lng = location.lng();

    updateHiddenInputs(lat, lng, mode);


    clearMarkers(mode);
    addMarker(location, `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`, 'selected', null, mode);

    reverseGeocode(location, mode);
}

function updateHiddenInputs(lat, lng, mode) {
    const latLngString = `${lat.toFixed(6)},${lng.toFixed(6)}`;

    document.querySelectorAll(`[data-map='${mode}'][name='hidLatLngEdit[]']`)
        .forEach((input) => {
            input.value = latLngString;
        });

    document.querySelectorAll(`[name='hidLatLngAdd[]']`)
        .forEach((input) => {
            input.value = latLngString;
        });

    document.querySelectorAll(`[name='hidLatLng']`)
        .forEach((input) => {
            input.value = latLngString;
        });
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