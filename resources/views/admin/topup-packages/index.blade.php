@extends('layouts.site')

@section('title', 'Pakej Topup Point')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h4 class="font-display mb-0">Pakej Topup Point</h4>
    <a href="{{ route('admin.topup-packages.create') }}" class="btn btn-brand btn-sm">+ Tambah Pakej Topup</a>
</div>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Point</th>
                    <th>Harga</th>
                    <th>RM / Point</th>
                    <th>Kali Digunakan</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packages as $package)
                    <tr>
                        <td>{{ $package->points }} point</td>
                        <td>RM{{ number_format($package->price, 2) }}</td>
                        <td class="text-muted small">RM{{ number_format($package->price / max(1, $package->points), 3) }}</td>
                        <td>{{ $package->point_batches_count }}x</td>
                        <td>
                            <span class="badge {{ $package->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $package->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.topup-packages.edit', $package) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.topup-packages.destroy', $package) }}" class="d-inline"
                                  onsubmit="return confirm('Padam/nyahaktifkan pakej {{ $package->points }} point ni?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Padam</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pakej topup.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
