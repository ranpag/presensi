<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServerCommand extends Command
{
    protected $signature = 'srv';
    protected $description = 'Start Laravel Server, Websocket Reverb, and Queue Worker';

    protected ?Process $serveProcess = null;
    protected ?Process $reverbProcess = null;
    protected ?Process $queueProcess = null;

    protected $closureCommand = null;

    public function handle($thos)
    {
        $this->closureCommand = $thos;

        $this->closureCommand->info("Starting Laravel server...");
        $this->serveProcess = $this->startProcess(['php', 'artisan', 'serve'], 'Laravel Serve');

        sleep(2);

        $this->closureCommand->info("Starting Websocket Laravel Reverb...");
        $this->reverbProcess = $this->startProcess(['php', 'artisan', 'reverb:start'], 'Reverb WebSocket');

        $this->closureCommand->info("Starting Queue Worker...");
        $this->queueProcess = $this->startProcess(['php', 'artisan', 'queue:work'], 'Queue Worker');

        $this->closureCommand->warn("\nPress Ctrl + C to stop all processes.");

        // Tangani Ctrl + C untuk menghentikan semua proses dengan aman
        register_shutdown_function(function () {
            echo "Skrip dihentikan, membersihkan semua proses...\n";
            $this->stopAllProcesses();
        });
        

        // Loop utama untuk memantau proses
        while (true) {
            if ($this->serveProcess && !$this->serveProcess->isRunning()) {
                $this->closureCommand->warn("\nServe process stopped! Stopping all processes...");
                $this->stopAllProcesses();
                exit(1);
            }

            if ($this->reverbProcess && !$this->reverbProcess->isRunning()) {
                $this->closureCommand->warn("\nWebsocket reverb process stopped! Stopping queue worker...");
                $this->stopProcess($this->queueProcess);
            }

            if ($this->queueProcess && !$this->queueProcess->isRunning()) {
                $this->closureCommand->warn("\nQueue worker stopped! Stopping websocket reverb...");
                $this->stopProcess($this->reverbProcess);
            }

            usleep(5000000);
        }
    }


    protected function startProcess(array $command, string $name): ?Process
    {
        $process = new Process($command);
        $process->setTimeout(null); // Pastikan tidak timeout
        $process->start(function ($type, $buffer) use ($name) {
            $outputType = ($type === Process::OUT) ? 'info' : 'error';
            $this->closureCommand->$outputType("[$name] " . trim($buffer));
        });

        return $process;
    }


    protected function stopProcess(?Process &$process)
    {
        if ($process && $process->isRunning()) {
            $process->stop();
            $process = null;
        }
    }

    protected function stopAllProcesses()
    {
        $this->stopProcess($this->queueProcess);
        $this->stopProcess($this->reverbProcess);
        $this->stopProcess($this->serveProcess);
        $this->closureCommand->info("\nAll processes stopped.");
    }
}
