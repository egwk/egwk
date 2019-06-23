<?php

namespace App\Console\Commands\Export;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class Buffered extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'e4a:buffered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export buffered data';

    protected function exportQueryResults($query, $limit = 3000, $fileName = 'output.json', $outputPath = 'buffered')
    {
        \Storage::makeDirectory($outputPath);
        $output = \Storage::path($outputPath . DIRECTORY_SEPARATOR . $fileName);
        file_put_contents($output, "[\n\t");

        $count = $query->count();
        $max = ceil($count / $limit);
        $progress = new ProgressBar($this->output, $count);
        $first = true;

        for ($i = 0; $i < $max; $i++) {
            $q = $query
                ->offset($i * $limit)
                ->limit($limit);

            $data = $q->get()->map(
                function ($item) {
                    return json_encode($item);
                }
            )->implode(",\n\t");

            if (!$first) {
                file_put_contents($output, ",\n\t", FILE_APPEND);
            } else {
                $first = false;
            }


            file_put_contents($output, $data, FILE_APPEND);
            $progress->advance($limit);
        }
        file_put_contents($output, "\n]\n", FILE_APPEND);
        $progress->finish();
        $this->info("\ndone.");

        return $output;
    }
}
