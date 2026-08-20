@extends('layouts.site')

@section('title', 'Senarai Ejen')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h4 class="font-display mb-0">Senarai Ejen</h4>
    <a href="{{ route('admin.agents.create') }}" class="btn btn-brand btn-sm">+ Daftar Ejen</a>
</div>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Emel</th>
                    <th>Kod Referral</th>
                    <th>Komisen %</th>
                    <th>Owner Direferral</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agents as $agent)
                    <tr>
                        <td>{{ $agent->user->name }}</td>
                        <td class="small text-muted">{{ $agent->user->email }}</td>
                        <td><code>{{ $agent->agent_code }}</code></td>
                        <td>{{ rtrim(rtrim(number_format($agent->commission_percent, 2), '0'), '.') }}%</td>
                        <td>{{ $agent->owners_count }}</td>
                        <td>
                            <span class="badge {{ $agent->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $agent->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @if ($agent->status === 'active')
                                <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}" class="d-inline"
                                      onsubmit="return confirm('Nyahaktifkan ejen {{ $agent->user->name }}?');">
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
                            Belum ada ejen didaftarkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
