<?php

namespace App\Console\Commands;

use App\Jobs\ProcessProducts;
use App\Jobs\ProcessStockStasuses;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as Com;

class ProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process CSV files of product and stock data.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        ProcessProducts::dispatch();
        //ProcessStockStasuses::dispatchSync();

        return Com::SUCCESS;
    }
}
