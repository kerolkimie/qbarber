<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    // Tiada perubahan struktur — column 'status' dah string bebas (tiada enum
    // constraint DB), jadi nilai baru 'scheduled' terus boleh digunakan tanpa
    // migration struktur. Fail ni sengaja kosong sebagai penanda versi logik.
    public function up(): void {}
    public function down(): void {}
};
