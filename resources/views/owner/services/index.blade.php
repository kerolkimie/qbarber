@extends('layouts.site')

@section('title', 'Servis — ' . $branch->name)

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <div>
        <p class="text-muted small mb-0">{{ $branch->name }}</p>
        <h4 class="font-display mb-0">Servis</h4>
    </div>
    <a href="{{ route('owner.branches.services.create', $branch) }}" class="btn btn-brand btn-sm">+ Tambah Servis</a>
</div>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama Servis</th>
                    <th>Harga</th>
                    <th>Tempoh</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>RM{{ number_format($service->price, 2) }}</td>
                        <td>{{ $service->duration_minutes }} minit</td>
                        <td>
                            <span class="badge {{ $service->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $service->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('owner.services.edit', $service) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @if ($service->status === 'active')
                                <form method="POST" action="{{ route('owner.services.destroy', $service) }}" class="d-inline"
                                      onsubmit="return confirm('Nyahaktifkan servis {{ $service->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Nyahaktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada servis didaftarkan di cawangan ini. Pelanggan tak boleh ambil nombor giliran sehingga sekurang-kurangnya satu servis aktif ditambah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
