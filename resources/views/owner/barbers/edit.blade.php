@extends('layouts.site')

@section('title', 'Edit Tukang Gunting')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Edit Tukang Gunting — {{ $barber->name }}</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Emel login: <strong>{{ $barber->user->email }}</strong> (tak boleh ditukar di sini)
                </p>

                <form method="POST" action="{{ route('owner.barbers.update', $barber) }}">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="name" value="{{ old('name', $barber->name) }}"
                           class="form-control mb-3" required>

                    <x-phone-field name="phone" label="No. Telefon" :value="$barber->user->phone" />

                    @if ($branches->count() > 1)
                        <label class="form-label fw-semibold">Cawangan</label>
                        <select name="branch_id" class="form-select mb-1">
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ (old('branch_id', $barber->branch_id) == $b->id) ? 'selected' : '' }}>
                                    {{ $b->name }}{{ $b->id === $barber->branch_id ? ' (semasa)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-muted small mb-3">
                            Tukar cawangan untuk <strong>pindahkan</strong> tukang gunting ni. Tiket aktif dia di cawangan lama akan dilepaskan.
                        </p>
                    @else
                        <input type="hidden" name="branch_id" value="{{ $barber->branch_id }}">
                    @endif

                    @include('owner.barbers._payment-fields')

                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select mb-4">
                        <option value="active" {{ $barber->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $barber->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>

                    <button type="submit" class="btn btn-brand w-100 py-2">Kemaskini</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
