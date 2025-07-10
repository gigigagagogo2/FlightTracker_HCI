<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Carbon\Carbon;

class CheckArrivi extends Command
{
    protected $signature = 'check:arrivi';

    protected $description = 'Controlla gli arrivi dei voli e invia notifiche';

    public function handle()
    {
        $onlineUsers = Http::get('http://websocket-server:3000/online-users')->json('users');

        if (empty($onlineUsers)) {
            $this->info('Nessun utente online');
            return 0;
        }

        $utentiDaNotificare = User::whereIn('id', $onlineUsers)
            ->whereHas('flights', function ($query) {
                $query->where('arrival_time', '<=', Carbon::now())
                    ->where('user_flight.notified', false);
            })
            ->with(['flights' => function ($query) {
                $query->where('arrival_time', '<=', Carbon::now())
                    ->where('user_flight.notified', false);
            }])
            ->get();

        $notifiche = [];

        foreach ($utentiDaNotificare as $user) {
            $flightIds = $user->flights->pluck('id')->all();

            if (!empty($flightIds)) {
                $notifiche[$user->id] = $flightIds;

                $user->flights()->updateExistingPivot($flightIds, ['notified' => true]);
            }
        }

        if (!empty($notifiche)) {
            Http::post('http://websocket-server:3000/notify', ['notifications' => $notifiche]);
        }

        $this->info('Notifiche inviate a: ' . json_encode(array_keys($notifiche)));

        return 0;
    }
}
