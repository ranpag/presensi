<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
 
class SiswaAlfa implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $siswaAlfa;

    public function __construct($siswaAlfa)
    {
        $this->siswaAlfa = $siswaAlfa;
    }

    public function broadcastOn()
    {
        return new Channel('siswa');
    }

    public function broadcastAs()
    {
        return 'siswa.alfa';
    }
}
