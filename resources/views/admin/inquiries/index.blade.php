@extends('layouts.site')

@section('title', 'Pertanyaan Pelanggan')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-2">
    <h4 class="font-display mb-0">Pertanyaan Pelanggan (Hubungi Kami)</h4>
    @if ($newCount > 0)
        <span class="badge text-bg-danger">{{ $newCount }} baru</span>
    @endif
</div>

<form method="GET" action="{{ route('admin.inquiries.index') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold small mb-0">Status:</label>
        <select name="status" class="form-select" style="max-width:200px;">
            <option value="">Semua Status</option>
            <option value="new" {{ $status === 'new' ? 'selected' : '' }}>Baru</option>
            <option value="contacted" {{ $status === 'contacted' ? 'selected' : '' }}>Sudah Dihubungi</option>
            <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Ditutup</option>
        </select>
        <button type="submit" class="btn btn-pine btn-sm">Tapis</button>
        @if ($status)
            <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        @endif
    </div>
</form>

<div class="card card-brand mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telefon</th>
                    <th>Emel</th>
                    <th>Nama Perniagaan</th>
                    <th>Mesej</th>
                    <th>Tarikh</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->name }}</td>
                        <td class="small"><a href="tel:{{ $inquiry->phone }}" class="text-decoration-none">{{ $inquiry->phone }}</a></td>
                        <td class="small text-muted">{{ $inquiry->email ?: '—' }}</td>
                        <td class="small text-muted">{{ $inquiry->business_name ?: '—' }}</td>
                        <td class="small" style="max-width:220px;">{{ $inquiry->message ?: '—' }}</td>
                        <td class="small text-muted">{{ $inquiry->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            @php
                                $badgeClass = match($inquiry->status) {
                                    'new' => 'text-bg-danger',
                                    'contacted' => 'text-bg-warning',
                                    'closed' => 'text-bg-secondary',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-capitalize">{{ $inquiry->status }}</span>
                        </td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.inquiries.updateStatus', $inquiry) }}" class="d-flex gap-1 justify-content-end">
                                @csrf
                                <select name="status" class="form-select form-select-sm" style="max-width:140px;" onchange="this.form.submit()">
                                    <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>Baru</option>
                                    <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                                    <option value="closed" {{ $inquiry->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pertanyaan.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
{{ $inquiries->links('pagination::bootstrap-5') }}
@endsection
