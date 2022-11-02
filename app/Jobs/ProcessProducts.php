<?php

namespace App\Jobs;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use const DIRECTORY_SEPARATOR as DS;

class ProcessProducts implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const FILE_PATH = 'app'.DS.'dataset'.DS.'price.tsv';
    private const BATCH_SIZE = 100;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // start the bus and add the batches later
        $bus = Bus::batch([])->dispatch();

        $resource = fopen(storage_path(self::FILE_PATH), 'r');
        $products = [];
        $timestamp = new Carbon;
        $timeString = $timestamp->toDateTimeString();

        while ($fields = fgetcsv($resource, null, "\t")) {
            // skip the header if applicable
            if (self::isHeader($fields)) {
                continue;
            }

            $productArray = [
                'sku' => $fields[1],
                'supplier_id' => $fields[0],
                'cost_price' => $fields[4],
                'rrp' => $fields[5],
                'created_at' => $timeString,
                'updated_at' => $timeString,
            ];

            $products[] = $productArray;

            // Upon reaching batch size, send the products to a new job and start again
            if (count($products) === self::BATCH_SIZE) {
                $batch = new ProcessProductBatch($products);
                $bus->add($batch);
                $products = [];
            }
        }
    }

    private static function isHeader(array $fields): bool
    {
        return $fields[0] === 'ProdNr';
    }
}
