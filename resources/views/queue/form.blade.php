@extends('layouts.site')

@section('title', $branch->name . ' — Ambil Nombor Giliran')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-brand mb-4">
            <div class="card-header py-3">{{ $branch->name }} — Ambil Nombor Giliran</div>
            <div class="card-body p-4">

                @if ($maxPax < 1)
                    <div class="alert alert-danger mb-0">
                        Maaf, kedai ini buat masa ini tidak dapat menerima tempahan baru.
                        Sila hubungi kedai terus atau cuba lagi kemudian.
                    </div>
                @else
                    <form method="POST" action="{{ route('queue.store', $branch->qr_token) }}" id="queue-form">
                        @csrf

                        <label class="form-label fw-semibold">Nama (optional)</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="form-control mb-3" placeholder="cth: Ahmad">

                        <x-phone-field name="customer_phone" label="No. Telefon (optional)" />

                        <label class="form-label fw-semibold">Bilangan Orang</label>
                        <select id="pax-select" name="pax" class="form-select mb-4" style="max-width:160px;">
                            @for ($i = 1; $i <= $maxPax; $i++)
                                <option value="{{ $i }}" {{ old('pax', 1) == $i ? 'selected' : '' }}>{{ $i }} orang</option>
                            @endfor
                        </select>

                        <div id="pax-services"></div>

                        <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Ambil Nombor Giliran</button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>

@if ($maxPax >= 1)
@push('scripts')
@php
    $serviceOptions = $services->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'price' => number_format($s->price, 2),
        'duration' => $s->duration_minutes,
    ]);
    $oldServicesArray = old('services', []);
@endphp
<script>
    const services = @json($serviceOptions);
    const barbers = @json($barbers->map(fn ($b) => ['id' => $b->id, 'name' => $b->name]));

    // Servis yang pelanggan dah pilih sebelum ni (kalau validation gagal & page reload)
    const oldServices = @json($oldServicesArray);
    const oldBarbers = @json(old('barbers', []));

    const paxSelect = document.getElementById('pax-select');
    const paxServices = document.getElementById('pax-services');

    function serviceOptions(selectedId) {
        return services.map(s =>
            `<option value="${s.id}" ${String(s.id) === String(selectedId) ? 'selected' : ''}>${s.name} — RM${s.price} (~${s.duration} min)</option>`
        ).join('');
    }

    function barberOptions(selectedId) {
        return barbers.map(b =>
            `<option value="${b.id}" ${String(b.id) === String(selectedId) ? 'selected' : ''}>${b.name}</option>`
        ).join('');
    }

    function renderPaxFields() {
        const count = parseInt(paxSelect.value, 10);
        let html = '';
        for (let i = 1; i <= count; i++) {
            const selectedService = oldServices[i - 1] ?? '';
            const selectedBarber = oldBarbers[i - 1] ?? '';
            html += `
                <label class="form-label fw-semibold">Servis untuk Orang ${i}</label>
                <select name="services[]" class="form-select mb-2" required>
                    <option value="" disabled ${selectedService ? '' : 'selected'}>Pilih servis</option>
                    ${serviceOptions(selectedService)}
                </select>
                <label class="form-label small text-muted">Tukang Gunting Pilihan Orang ${i} (optional)</label>
                <select name="barbers[]" class="form-select mb-3">
                    <option value="">Tiada pilihan — sesiapa pun</option>
                    ${barberOptions(selectedBarber)}
                </select>
            `;
        }
        paxServices.innerHTML = html;
    }

    paxSelect.addEventListener('change', renderPaxFields);
    renderPaxFields();
</script>
@endpush
@endif
@endsection
