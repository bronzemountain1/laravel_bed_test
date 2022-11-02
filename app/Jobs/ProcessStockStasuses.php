<?php

namespace App\Jobs;

use App\Models\StockStatus;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use const DIRECTORY_SEPARATOR as DS;

class ProcessStockStasuses implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const FILE_PATH = 'app'.DS.'dataset'.DS.'stock.csv';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $resource = fopen(storage_path(self::FILE_PATH), 'r');
        $counter = 1;
        $bigArray = [];
        $timestamp = new Carbon;
        $timeString = $timestamp->toDateTimeString();
        while ($fields = fgetcsv($resource, null, "\t")) {
            $stockStatusArray = [
                'product_id' => $fields[0],
                'stock_quantity' => $fields[1],
                'time' => $fields[2],
                'created_at' => $timeString,
                'updated_at' => $timeString,
            ];

            $bigArray[] = $stockStatusArray;

            $counter ++;
        }

        StockStatus::insert($bigArray);
    }
}
