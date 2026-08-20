@extends('layouts.site')

@section('title', 'Semua Cawangan')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Semua Cawangan</h4>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Cawangan</th>
                    <th>Owner</th>
                    <th>Komisen %</th>
                    <th>Tukang Gunting</th>
                    <th>Servis</th>
                    <th>QR Token</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td class="small text-muted">{{ $branch->owner->business_name ?? '—' }}</td>
                        <td>{{ rtrim(rtrim(number_format($branch->commission_percent, 2), '0'), '.') }}%</td>
                        <td>{{ $branch->barbers_count }}</td>
                        <td>{{ $branch->services_count }}</td>
                        <td><code class="small">{{ \Illuminate\Support\Str::limit($branch->qr_token, 12) }}</code></td>
                        <td>
                            <span class="badge {{ $branch->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $branch->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada cawangan.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
