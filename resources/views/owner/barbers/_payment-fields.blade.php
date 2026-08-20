@php
    $currentType = old('payment_type', $barber->payment_type ?? 'commission');
    $branchCommission = $branch->commission_percent ?? ($barber->branch->commission_percent ?? 0);
@endphp

<label class="form-label fw-semibold">Model Bayaran</label>
<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="service-opt d-block border rounded p-3" style="cursor:pointer; {{ $currentType === 'commission' ? 'border-color:var(--red); background:#FBEFEF;' : 'border-color:#D8CBA6;' }}">
            <input type="radio" name="payment_type" value="commission" class="me-2 payment-type-radio"
                   {{ $currentType === 'commission' ? 'checked' : '' }}>
            <strong>Komisen</strong>
            <span class="d-block small text-muted mt-1">Dapat {{ rtrim(rtrim(number_format($branchCommission, 2), '0'), '.') }}% dari setiap servis (ikut peratus cawangan)</span>
        </label>
    </div>
    <div class="col-6">
        <label class="service-opt d-block border rounded p-3" style="cursor:pointer; {{ $currentType === 'chair_rental' ? 'border-color:var(--red); background:#FBEFEF;' : 'border-color:#D8CBA6;' }}">
            <input type="radio" name="payment_type" value="chair_rental" class="me-2 payment-type-radio"
                   {{ $currentType === 'chair_rental' ? 'checked' : '' }}>
            <strong>Sewa Kerusi</strong>
            <span class="d-block small text-muted mt-1">Bayar sewa tetap, simpan 100% hasil servis sendiri</span>
        </label>
    </div>
</div>

<div id="rental-fields" style="{{ $currentType === 'chair_rental' ? '' : 'display:none;' }}">
    <div class="row">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Jumlah Sewa (RM)</label>
            <input type="number" name="rental_amount" value="{{ old('rental_amount', $barber->rental_amount ?? '') }}"
                   class="form-control mb-3" step="0.01" min="0" placeholder="cth: 50.00">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Tempoh Sewa</label>
            <select name="rental_period" class="form-select mb-3">
                <option value="">Pilih tempoh</option>
                <option value="daily" {{ old('rental_period', $barber->rental_period ?? '') === 'daily' ? 'selected' : '' }}>Sehari</option>
                <option value="weekly" {{ old('rental_period', $barber->rental_period ?? '') === 'weekly' ? 'selected' : '' }}>Seminggu</option>
                <option value="monthly" {{ old('rental_period', $barber->rental_period ?? '') === 'monthly' ? 'selected' : '' }}>Sebulan</option>
            </select>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.payment-type-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.getElementById('rental-fields').style.display = this.value === 'chair_rental' ? 'block' : 'none';
            document.querySelectorAll('.payment-type-radio').forEach(r => {
                r.closest('.service-opt').style.borderColor = r.checked ? 'var(--red)' : '#D8CBA6';
                r.closest('.service-opt').style.background = r.checked ? '#FBEFEF' : 'transparent';
            });
        });
    });
</script>
@endpush
