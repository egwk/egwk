<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 14/08/2018
 * Time: 16:43
 */

namespace App\EGWK;


use App\EGWK\Datamining\StorageDriver;
use Foolz\SphinxQL\Drivers\Pdo\Connection;
use App\EGWK\Tools\Bench;
use Illuminate\Support\Facades\DB;

abstract class Datamining
{

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
    protected $glue = ',';

    /**
     * Ranker expression
     * Note: Alternative ranker: "expr('doc_word_count*10000/query_word_count')";
     *
     * @var string
     */
    protected $ranker = '';

    /**
     * @var StorageDriver
     */
    protected $storage = null;

    public function __construct(StorageDriver $storage)
    {
        $this->storage = $storage;
        $this->index = config('scout.prefix', '') . $this->index;
    }

    protected function connect()
    {
        $connection = new Connection();
        $driver = config('scout.driver', 'sphinxsearch');
        $connection->setParams(['host' => config('scout.' . $driver . '.host', 'sphinx'), 'port' => config('scout.' . $driver . '.port', 9306)]);
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
                $this->storage->storeException($this->toString($item));
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
    public function mine($start, $limit, $offset)
    {
        Bench::start(true);
        $this->storage->init();

        foreach ($this->loop($start, $limit, $offset) as $data) {
            $this->storage->store($data);
        }

        $this->storage->done();
        Bench::stop(true);
    }

}
