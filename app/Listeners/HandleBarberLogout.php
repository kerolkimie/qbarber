<?php

namespace App\Listeners;

use App\Models\QueueTicket;
use Illuminate\Auth\Events\Logout;

/**
 * Safety net: kalau tukang gunting log keluar (atau session tamat) semasa masih
 * ada tiket 'waiting'/'in_progress' pada dia, lepaskan semula tiket tu ke giliran
 * supaya tukang gunting lain boleh ambil alih — elak pelanggan terperangkap
 * menunggu orang yang dah tak ada.
 */
class HandleBarberLogout
{
    public function handle(Logout $event): void
    {
        $barber = $event->user?->barber ?? null;

        if (! $barber) {
            return;
        }

        QueueTicket::where('barber_id', $barber->id)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->update([
                'barber_id' => null,
                'preferred_barber_id' => null,
                'status' => 'waiting',
                'started_at' => null,
                'called_at' => null,
            ]);

        // Lepaskan juga tiket yang BELUM dipanggil lagi tapi pelanggan khusus
        // pilih barber ni — elak terperangkap sebab barber ni dah offline.
        QueueTicket::where('preferred_barber_id', $barber->id)
            ->where('status', 'waiting')
            ->whereNull('barber_id')
            ->update(['preferred_barber_id' => null]);

        $barber->update(['current_state' => 'offline']);

        $shift = $barber->todayShift();

        if ($shift && ! $shift->clock_out) {
            $shift->update(['clock_out' => now()]);
        }
    }
}
