<?php

namespace App\Console\Commands;

use App\Services\MoMoRequest;
use Illuminate\Console\Command;

class MoMoCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'momo:pay {phone} {amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $request = new MoMoRequest();

        $request->setParams($this->argument('phone'), $this->argument('amount'));

        $request->start();

        return 0;
    }
}
