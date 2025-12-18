@extends('layouts.app')

@section('content')
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Edit Lowongan: {{ $lowongan->judul }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('lowongan.update', $lowongan->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- =================================================== --}}
            {{-- 1. INFORMASI DASAR --}}
            {{-- =================================================== --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="judul" class="form-label fw-bold">Judul Lowongan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul"
                        value="{{ old('judul', $lowongan->judul) }}" placeholder="Contoh: Web Developer">
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="divisi" class="form-label fw-bold">Divisi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('divisi') is-invalid @enderror" id="divisi" name="divisi"
                        value="{{ old('divisi', $lowongan->divisi) }}" placeholder="Contoh: Teknologi Informasi">
                    @error('divisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label fw-bold">Deskripsi Lowongan</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4"
                    placeholder="Jelaskan tanggung jawab dan jobdesk...">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jurusan Terkait <span class="text-danger">*</span></label>
                    <input type="text" name="jurusan_terkait" class="form-control @error('jurusan_terkait') is-invalid @enderror"
                        value="{{ old('jurusan_terkait', $lowongan->jurusan_terkait) }}"
                        placeholder="Contoh: Teknik Informatika, DKV">
                    <div class="form-text small">Pisahkan dengan koma jika lebih dari satu.</div>
                    @error('jurusan_terkait') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fasilitas / Benefit</label>
                    <input type="text" name="fasilitas" class="form-control @error('fasilitas') is-invalid @enderror"
                        value="{{ old('fasilitas', $lowongan->fasilitas) }}"
                        placeholder="Contoh: Uang Transport, Sertifikat">
                    @error('fasilitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- =================================================== --}}
            {{-- 2. PERIODE PENDAFTARAN --}}
            {{-- =================================================== --}}
            <fieldset class="border p-3 mb-4 rounded bg-light">
                <legend class="w-auto h6 fw-bold text-uppercase text-secondary px-2"><i class="far fa-calendar-alt me-1"></i> Periode & Durasi</legend>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="window_start_date" class="form-label">Buka Pendaftaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('window_start_date') is-invalid @enderror" id="window_start_date" name="window_start_date"
                            value="{{ old('window_start_date', \Carbon\Carbon::parse($lowongan->window_start_date)->format('Y-m-d')) }}">
                        @error('window_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="window_end_date" class="form-label">Tutup Pendaftaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('window_end_date') is-invalid @enderror" id="window_end_date" name="window_end_date"
                            value="{{ old('window_end_date', \Carbon\Carbon::parse($lowongan->window_end_date)->format('Y-m-d')) }}">
                        @error('window_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="min_duration" class="form-label">Durasi Minimal (Bulan) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('min_duration') is-invalid @enderror" id="min_duration" name="min_duration"
                            value="{{ old('min_duration', $lowongan->min_duration) }}" min="1">
                        @error('min_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </fieldset>

            {{-- =================================================== --}}
            {{-- 3. ATURAN KERJA --}}
            {{-- =================================================== --}}
            <fieldset class="border p-3 mb-4 rounded bg-light">
                <legend class="w-auto h6 fw-bold text-uppercase text-secondary px-2"><i class="fas fa-clock me-1"></i> Aturan & Jadwal Kerja</legend>

                {{-- A. PILIHAN TIPE JADWAL (Toggle) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Jadwal Magang</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleJadwal" name="is_fulltime" value="1"
                            {{ old('is_fulltime', $lowongan->is_fulltime) ? 'checked' : '' }} onchange="toggleHari()">
                        <label class="form-check-label fw-bold text-primary" for="toggleJadwal" id="labelJadwal">
                            Jadwal Terikat (Ditentukan Admin)
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1" id="infoText">
                        Peserta wajib masuk sesuai hari yang Anda tentukan di bawah.
                    </small>
                </div>

                {{-- B. PILIHAN HARI (Muncul jika Toggle Aktif) --}}
                <div id="pilihanHari" class="mb-3 p-3 border rounded bg-white">
                    <label class="form-label small text-uppercase text-secondary fw-bold mb-2">Pilih Hari Kerja Wajib:</label>
                    <div class="d-flex flex-wrap gap-3">
                        @php
                        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        // Ambil data hari tersimpan (pastikan di model sudah dicasting array)
                        $savedDays = $lowongan->working_days ?? [];
                        @endphp
                        @foreach($days as $day)
                        <div class="form-check">
                            {{-- Logic Checked: 
                                1. Jika ada error validasi (old input ada), cek di old. 
                                2. Jika fresh load, cek di database ($savedDays). 
                            --}}
                            <input class="form-check-input" type="checkbox" name="working_days[]" value="{{ $day }}" id="day_{{ $day }}"
                                {{ (is_array(old('working_days')) && in_array($day, old('working_days'))) || (!old('working_days') && is_array($savedDays) && in_array($day, $savedDays)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="day_{{ $day }}">
                                {{ $day }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('working_days')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- C. JAM KERJA STANDAR --}}
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label for="jam_masuk_standar" class="form-label">Jam Masuk Standar <span class="text-danger">*</span></label>
                        {{-- Gunakan Carbon format H:i agar sesuai input type time --}}
                        <input type="time" class="form-control @error('jam_masuk_standar') is-invalid @enderror" id="jam_masuk_standar" name="jam_masuk_standar"
                            value="{{ old('jam_masuk_standar', \Carbon\Carbon::parse($lowongan->jam_masuk_standar)->format('H:i')) }}">
                        <div class="form-text">Acuan untuk status "Terlambat".</div>
                        @error('jam_masuk_standar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jam_pulang_standar" class="form-label">Jam Pulang Standar <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_pulang_standar') is-invalid @enderror" id="jam_pulang_standar" name="jam_pulang_standar"
                            value="{{ old('jam_pulang_standar', \Carbon\Carbon::parse($lowongan->jam_pulang_standar)->format('H:i')) }}">
                        <div class="form-text">Acuan untuk status "Auto-Close".</div>
                        @error('jam_pulang_standar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </fieldset>

            {{-- =================================================== --}}
            {{-- 4. STATUS & AKSI --}}
            {{-- =================================================== --}}
            <div class="form-check mb-4 p-3 border rounded bg-light">
                <input class="form-check-input" type="checkbox" id="is_open" name="is_open" value="1"
                    {{ old('is_open', $lowongan->is_open) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark" for="is_open">
                    Langsung Buka Lowongan Ini (Publish)
                </label>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('lowongan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Lowongan
                </button>
            </div>

        </form>
    </div>
</div>

{{-- SCRIPT LOGIKA TAMPILAN --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        toggleHari(); // Jalankan saat halaman dimuat untuk set state awal
    });

    function toggleHari() {
        const isFulltime = document.getElementById('toggleJadwal').checked;
        const divHari = document.getElementById('pilihanHari');
        const infoText = document.getElementById('infoText');
        const labelJadwal = document.getElementById('labelJadwal');

        if (isFulltime) {
            // TAMPILKAN CHECKBOX HARI
            divHari.style.display = 'block';
            labelJadwal.innerText = "Jadwal Terikat (Ditentukan Admin)";
            labelJadwal.classList.add('text-primary');
            labelJadwal.classList.remove('text-secondary');
            infoText.innerText = "Peserta wajib masuk sesuai hari yang Anda tentukan di bawah.";
        } else {
            // SEMBUNYIKAN CHECKBOX HARI
            divHari.style.display = 'none';
            labelJadwal.innerText = "Jadwal Fleksibel (Peserta Mengajukan)";
            labelJadwal.classList.remove('text-primary');
            labelJadwal.classList.add('text-secondary');
            infoText.innerText = "Peserta bebas memilih hari kerja mereka sendiri saat melamar.";
        }
    }
</script>

@endsection