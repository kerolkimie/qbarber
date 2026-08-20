@extends('layouts.site')

@section('title', 'Pakej Subscription')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h4 class="font-display mb-0">Pakej Subscription</h4>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-brand btn-sm">+ Tambah Pakej</a>
</div>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Tempoh</th>
                    <th>Had Cawangan</th>
                    <th>Had Kerusi</th>
                    <th>Digunakan</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                    <tr>
                        <td>{{ $plan->name }}</td>
                        <td>RM{{ number_format($plan->price, 2) }}</td>
                        <td>{{ $plan->duration_days }} hari</td>
                        <td>{{ $plan->max_branches }}</td>
                        <td>{{ $plan->max_barbers }}{{ $plan->is_per_branch_limit ? ' /cawangan' : ' total' }}</td>
                        <td>{{ $plan->subscriptions_count }}x</td>
                        <td>
                            <span class="badge {{ $plan->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $plan->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="d-inline"
                                  onsubmit="return confirm('Padam/nyahaktifkan pakej {{ $plan->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Padam</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pakej.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
