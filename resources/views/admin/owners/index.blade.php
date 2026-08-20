@extends('layouts.site')

@section('title', 'Senarai Owner')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Senarai Owner</h4>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Perniagaan</th>
                    <th>Owner</th>
                    <th>Ejen</th>
                    <th>Cawangan</th>
                    <th>Subscription</th>
                    <th>Status</th>
                    <th>Emel Disahkan</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($owners as $owner)
                    <tr>
                        <td>{{ $owner->business_name }}</td>
                        <td class="small text-muted">{{ $owner->user->name }}<br>{{ $owner->user->email }}</td>
                        <td class="small text-muted">{{ $owner->agent->user->name ?? '—' }}</td>
                        <td>{{ $owner->branches_count }}</td>
                        <td class="small">
                            @if ($owner->activeSubscription)
                                {{ $owner->activeSubscription->plan->name }}<br>
                                <span class="text-muted">sehingga {{ $owner->activeSubscription->end_date->format('d M Y') }}</span>
                            @else
                                <span class="text-muted">Tiada aktif</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $owner->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $owner->status }}
                            </span>
                        </td>
                        <td>
                            @if ($owner->user->hasVerifiedEmail())
                                <span class="badge text-bg-success">Ya</span>
                            @else
                                <span class="badge text-bg-danger">Belum</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.owners.show', $owner) }}" class="btn btn-sm btn-pine">Lihat Detail</a>
                            @unless ($owner->user->hasVerifiedEmail())
                                <form method="POST" action="{{ route('admin.owners.activate', $owner) }}" class="d-inline"
                                      onsubmit="return confirm('Aktifkan akaun {{ $owner->business_name }} secara manual?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-brand">Aktifkan</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada owner mendaftar.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
