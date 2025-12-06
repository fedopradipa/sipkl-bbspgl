/**
 * Geolocation Handler untuk Presensi
 * Memantau lokasi user dan mengaktifkan tombol check-in/out
 */

document.addEventListener("DOMContentLoaded", function () {
  const btnIn = document.getElementById('btn-checkin');
  const btnOut = document.getElementById('btn-checkout');

  if (!navigator.geolocation) {
    alert("Browser tidak mendukung Geolocation.");
    return;
  }

  const options = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0
  };

  function success(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;

    // Set koordinat untuk check-in
    const latIn = document.getElementById('lat');
    const longIn = document.getElementById('long');
    if (latIn) {
      latIn.value = lat;
      longIn.value = lng;
    }

    // Set koordinat untuk check-out
    const latOut = document.getElementById('lat_out');
    const longOut = document.getElementById('long_out');
    if (latOut) {
      latOut.value = lat;
      longOut.value = lng;
    }

    // Aktifkan tombol check-in
    if (btnIn) {
      btnIn.disabled = false;
      btnIn.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i> CHECK IN MASUK';
    }

    // Aktifkan tombol check-out
    if (btnOut) {
      btnOut.disabled = false;
      btnOut.innerHTML = '<i class="fas fa-sign-out-alt me-2"></i> CHECK OUT PULANG';
    }
  }

  function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
    const msg = "Gagal mendeteksi lokasi.";

    if (btnIn) {
      btnIn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + msg;
    }
    if (btnOut) {
      btnOut.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + msg;
    }
  }

  // Mulai tracking lokasi
  navigator.geolocation.watchPosition(success, error, options);
});