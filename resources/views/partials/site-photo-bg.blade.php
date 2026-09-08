{{-- Foto latar suasana kedai gunting untuk seluruh halaman (kawasan putih).
     Guna: @include('partials.site-photo-bg') selepas baris @section('title', ...).
     Foto: public/images/hero-barbershop.jpg (lihat public/images/README.txt). --}}
@push('styles')
<style>
    body{
        background-image:
            linear-gradient(180deg, rgba(245,247,251,.93) 0%, rgba(245,247,251,.96) 100%),
            url('{{ asset('images/hero-barbershop.jpg') }}');
        background-size:cover;
        background-position:center top;
        background-repeat:no-repeat;
        background-attachment:fixed;
    }
    @media (max-width:767.98px){
        body{ background-attachment:scroll; }
    }
</style>
@endpush
