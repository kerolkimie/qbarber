@extends('layouts.site')

@section('title', 'Tukang Gunting — ' . $branch->name)

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <div>
        <p class="text-muted small mb-0">{{ $branch->name }}</p>
        <h4 class="font-display mb-0">Tukang Gunting</h4>
    </div>
    <a href="{{ route('owner.branches.barbers.create', $branch) }}" class="btn btn-brand btn-sm">+ Daftar Tukang Gunting</a>
</div>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Emel Login</th>
                    <th>Telefon</th>
                    <th>Model Bayaran</th>
                    <th>Status</th>
                    <th>Semasa</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barbers as $barber)
                    <tr>
                        <td>{{ $barber->name }}</td>
                        <td class="small text-muted">{{ $barber->user->email }}</td>
                        <td class="small text-muted">{{ $barber->user->phone ?: '—' }}</td>
                        <td class="small">
                            @if ($barber->isChairRental())
                                Sewa Kerusi<br>
                                <span class="text-muted">RM{{ number_format($barber->rental_amount, 2) }} / {{ \App\Models\Barber::PERIOD_LABELS[$barber->rental_period] ?? $barber->rental_period }}</span>
                            @else
                                Komisen {{ rtrim(rtrim(number_format($branch->commission_percent, 2), '0'), '.') }}%
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $barber->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $barber->status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $barber->current_state === 'available' ? 'text-bg-success' : ($barber->current_state === 'busy' ? 'text-bg-danger' : 'text-bg-secondary') }} text-capitalize">
                                {{ $barber->current_state }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('owner.barbers.edit', $barber) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @if ($barber->status === 'active')
                                <form method="POST" action="{{ route('owner.barbers.destroy', $barber) }}" class="d-inline"
                                      onsubmit="return confirm('Nyahaktifkan {{ $barber->name }}? Dia tak akan boleh log masuk lagi.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Nyahaktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada tukang gunting didaftarkan di cawangan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
