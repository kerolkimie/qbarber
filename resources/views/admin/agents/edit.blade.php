@extends('layouts.site')

@section('title', 'Edit Ejen')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Edit Ejen — {{ $agent->user->name }}</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Emel login: <strong>{{ $agent->user->email }}</strong> &middot;
                    Kod referral: <strong>{{ $agent->agent_code }}</strong> (tak boleh ditukar)
                </p>

                <form method="POST" action="{{ route('admin.agents.update', $agent) }}">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="name" value="{{ old('name', $agent->user->name) }}"
                           class="form-control mb-3" required>

                    <x-phone-field name="phone" label="No. Telefon" :value="$agent->user->phone" />

                    <label class="form-label fw-semibold">Peratus Komisen (%)</label>
                    <input type="number" name="commission_percent" value="{{ old('commission_percent', $agent->commission_percent) }}"
                           class="form-control mb-3" step="0.01" min="0" max="100" required>

                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select mb-4">
                        <option value="active" {{ $agent->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $agent->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>

                    <button type="submit" class="btn btn-brand w-100 py-2">Kemaskini</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
