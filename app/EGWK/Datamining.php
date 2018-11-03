<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 14/08/2018
 * Time: 16:43
 */

namespace App\EGWK;


use Foolz\SphinxQL\Drivers\Pdo\Connection;
use App\EGWK\Tools\Bench;
use Illuminate\Support\Facades\Storage;

abstract class Datamining
{

    const OUTPUT_PATH = 'datamine/csv/';

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'cache_search';

    /**
     * Index name.
     *
     * @var string
     */
    protected $index = 'datamine';

    /**
     * Minimal Word Count
     *
     * @var integer
     */
    protected $minWordCount = 2;

    /**
     * Quorum Percentage
     *
     * @var float
     */
    protected $quorumPercentage = '0.40';

    /**
     * @var string
     */
    protected $filePath = '';

    /**
     * @var string
     */
    protected $exceptionFilePath = '';

    /**
     * @var string
     */
    protected $glue = ',';

    /**
     * Ranker expression
     * Note: Alternative ranker: "expr('doc_word_count*10000/query_word_count')";
     *
     * @var string
     */
    protected $ranker = "expr('sum(hit_count)*10000/query_word_count')";

   public function __construct()
   {
       $this->index = env('SCOUT_PREFIX', '') . $this->index;
   }

    protected function saveToFile($data, $filePath = null)
    {
        $filePath = $filePath ?: $this->filePath;
        if (!empty(trim($data))) {
            file_put_contents(Storage::path($filePath), $data, FILE_APPEND);
            // Storage::append($filePath, $data); // Note: Storage::append resets the file in vain
        }
    }

    protected function prepareFiles($outputFileName)
    {
        $this->filePath = self::OUTPUT_PATH . $outputFileName;
        $this->exceptionFilePath = self::OUTPUT_PATH . str_replace('.csv', '.exceptions.csv', $outputFileName);
        Storage::makeDirectory(self::OUTPUT_PATH);
        Storage::delete([$this->filePath, $this->exceptionFilePath]);
    }

    protected function connect()
    {
        $connection = new Connection();
        $connection->setParams(['host' => env('SCOUT_HOST', 'sphinx'), 'port' => env('SCOUT_PORT', 9306)]);
        return $connection;
    }

    protected function toString($item)
    {
        return implode($this->glue, (array)$item);
    }

    protected function isParaId($code)
    {
        return strpos($code, '.') !== false;
    }

    protected function getParagraph($code)
    {
        return DB::table($this->table)
            ->where('para_id', 'LIKE', $code)
            ->first();
    }

    abstract protected function query($start = 0, $limit = 0, $offset = 0);

    abstract protected function search($connection, $items);

    abstract protected function skipCondition($items);

    abstract protected function specCondition($items);

    abstract protected function processResult($results, $items);

    protected function loop($start = 0, $limit = 0, $offset = 0)
    {
        $connection = $this->connect();
        $items = $this->query($start, $limit, $offset);
        foreach ($items as $item) {
            Bench::step(true);
            if ($this->specCondition($item)) {
                $this->saveToFile($this->toString($item), $this->exceptionFilePath);
                continue;
            }
            if ($this->skipCondition($item)) {
                continue;
            }
            yield $this->processResult(
                $this->search(
                    $connection,
                    $item
                ),
                $item
            );
        }
    }

    /**
     * Do text mining
     *
     * @return void
     */
    public function mine($start, $limit, $offset, $outputFileName)
    {
        Bench::start(true);
        $this->prepareFiles($outputFileName);

        foreach ($this->loop($start, $limit, $offset) as $data) {
            $this->saveToFile($data);
        }
        Bench::stop(true);
    }

}
