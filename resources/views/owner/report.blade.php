@extends('layouts.site')

@section('title', 'Laporan Pendapatan')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<h4 class="font-display mb-3 mt-3">Laporan Pendapatan</h4>

{{-- Tab Hari / Minggu / Bulan --}}
<div class="btn-group mb-3 w-100" role="group">
    <a href="{{ route('owner.report', ['period' => 'day', 'branch_id' => $selectedBranchId]) }}"
       class="btn {{ $period === 'day' ? 'btn-brand' : 'btn-outline-secondary' }}">Hari</a>
    <a href="{{ route('owner.report', ['period' => 'week', 'branch_id' => $selectedBranchId]) }}"
       class="btn {{ $period === 'week' ? 'btn-brand' : 'btn-outline-secondary' }}">Minggu</a>
    <a href="{{ route('owner.report', ['period' => 'month', 'branch_id' => $selectedBranchId]) }}"
       class="btn {{ $period === 'month' ? 'btn-brand' : 'btn-outline-secondary' }}">Bulan</a>
</div>

{{-- Penapis --}}
<form method="GET" action="{{ route('owner.report') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <input type="hidden" name="period" value="{{ $period }}">

        <label class="fw-semibold small mb-0">Cawangan:</label>
        <select name="branch_id" class="form-select" style="max-width:220px;">
            <option value="">Semua Cawangan</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}" {{ (string) $selectedBranchId === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>

        @if ($period === 'day')
            <label class="fw-semibold small mb-0">Tarikh:</label>
            <input type="date" name="date" value="{{ $inputValue }}" class="form-control" style="max-width:180px;">
        @elseif ($period === 'week')
            <label class="fw-semibold small mb-0">Dalam Minggu:</label>
            <input type="date" name="date" value="{{ $inputValue }}" class="form-control" style="max-width:180px;">
        @else
            <label class="fw-semibold small mb-0">Bulan:</label>
            <input type="month" name="month" value="{{ $inputValue }}" class="form-control" style="max-width:180px;">
        @endif

        <button type="submit" class="btn btn-pine btn-sm">Tapis</button>

        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('owner.report', ['period' => $period, 'branch_id' => $selectedBranchId, ($period === 'month' ? 'month' : 'date') => $prevAnchor]) }}"
               class="btn btn-outline-secondary btn-sm">&larr; Sebelum</a>
            <a href="{{ route('owner.report', ['period' => $period, 'branch_id' => $selectedBranchId, ($period === 'month' ? 'month' : 'date') => $nextAnchor]) }}"
               class="btn btn-outline-secondary btn-sm">Seterusnya &rarr;</a>
        </div>
    </div>
</form>

{{-- Ringkasan keseluruhan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-brand h-100">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Jumlah Pelanggan</div>
                <div class="font-display fs-4">{{ $totals->customers }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100" style="border-color:var(--pine) !important;">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Jumlah Hasil</div>
                <div class="font-display fs-5">RM{{ number_format($totals->revenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100" style="border-color:var(--brass) !important;">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Komisen Tukang Gunting</div>
                <div class="font-display fs-5">RM{{ number_format($totals->commission, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100" style="border-color:var(--green-ok) !important;">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Untung Bersih Kedai</div>
                <div class="font-display fs-5">RM{{ number_format($totals->net, 2) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Pecahan ikut cawangan --}}
<div class="card card-brand mb-4">
    <div class="card-header py-3">Pecahan Ikut Cawangan — {{ $label }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Cawangan</th>
                    <th>Komisen %</th>
                    <th>Pelanggan</th>
                    <th>Hasil</th>
                    <th>Komisen</th>
                    <th>Untung Bersih</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report as $row)
                    <tr>
                        <td>
                            {{ $row->branch->name }}
                            @if ($row->rental_barber_count > 0)
                                <br><span class="text-muted small">({{ $row->rental_barber_count }} tukang gunting bawah sewa kerusi — sewa tak termasuk kiraan komisen)</span>
                            @endif
                        </td>
                        <td>{{ rtrim(rtrim(number_format($row->branch->commission_percent, 2), '0'), '.') }}%</td>
                        <td>{{ $row->customers }}</td>
                        <td>
                            <a href="{{ route('owner.tickets.history', ['branch_id' => $row->branch->id, 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="text-decoration-none">
                                RM{{ number_format($row->revenue, 2) }} <i class="bi bi-box-arrow-up-right small"></i>
                            </a>
                        </td>
                        <td>RM{{ number_format($row->commission, 2) }}</td>
                        <td class="fw-semibold">RM{{ number_format($row->net, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tiada data untuk tempoh ni.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>

{{-- Pecahan ikut tukang gunting (bila satu cawangan dipilih) --}}
@if ($barberBreakdown !== null)
    <div class="card card-brand">
        <div class="card-header py-3">Pecahan Ikut Tukang Gunting — {{ $label }}</div>
        <div class="card-body p-0">
            <div class="table-responsive">
<table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tukang Gunting</th>
                        <th>Model</th>
                        <th>Pelanggan</th>
                        <th>Hasil Dijana</th>
                        <th>Komisen Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barberBreakdown as $row)
                        <tr>
                            <td>{{ $row->barber->name }}</td>
                            <td class="small">
                                @if ($row->is_rental)
                                    Sewa Kerusi<br>
                                    <span class="text-muted">RM{{ number_format($row->barber->rental_amount, 2) }} / {{ \App\Models\Barber::PERIOD_LABELS[$row->barber->rental_period] ?? $row->barber->rental_period }}</span>
                                @else
                                    Komisen
                                @endif
                            </td>
                            <td>{{ $row->customers }}</td>
                            <td>RM{{ number_format($row->revenue, 2) }}</td>
                            <td class="fw-semibold">
                                {{ $row->is_rental ? '—' : 'RM' . number_format($row->commission, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tiada tukang gunting di cawangan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>

            </div>
        </div>
    </div>
@else
    <p class="text-muted small text-center">Pilih satu cawangan spesifik di atas untuk lihat pecahan ikut tukang gunting.</p>
@endif
@endsection
