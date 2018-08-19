<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 16/08/2018
 * Time: 09:12
 */

namespace App\EGWK\Install\Writings\Store;

use App\EGWK\Install\Writings\Filter;
use App\EGWK\Install\Writings\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Database extends Store
{
    /**
     * Number of operations in a transactions
     */
    const TRANSACTION_LIMIT = 32;

    /**
     * Table name without prefix
     */
    const WORK_TABLE_PARAGRAPHS = 'original_work';
    const WORK_TABLE_SENTENCES = 'original_sentence_work';

    /**
     * @var array Word list
     */
    protected $words = [];

    /**
     * @var string Table name
     *
     * env('DB_TABLE_PREFIX', '') . self::WORK_TABLE;
     *
     * Used for plan SQL queries only
     */
    protected $tableName = '';

    /**
     * @var \Illuminate\Database\Query\Builder Table object
     */
    protected $table = null;

    /**
     * @var \Illuminate\Database\Query\Builder Table object
     */
    protected $tableSentences = null;

    /**
     * @var int Insert Counter
     */
    protected $insertCounter = 0;

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter object
     * @return void
     */
    public function __construct(Filter $filter)
    {
        parent::__construct($filter);
        $this->table = \DB::table(self::WORK_TABLE_PARAGRAPHS);
        $this->tableSentences = \DB::table(self::WORK_TABLE_SENTENCES);
        $this->begin();
    }

    /**
     * Begin storing
     *
     * @access public
     * @return void
     */
    public function begin()
    {
        // $this->table->truncate();
        \DB::beginTransaction();
    }

    /**
     * End storing
     *
     * @access public
     * @return void
     */
    public function end()
    {
        \DB::commit();
        Storage::put('egw.words.list', implode("\n", $this->words));
    }

    /**
     * @inheritdoc
     */
    protected function before()
    {
        // TODO: Implement before() method.
    }

    /**
     * @inheritdoc
     */
    protected function after()
    {
        $this->insertCounter++;
        if (self::TRANSACTION_LIMIT <= $this->insertCounter) {
            \DB::commit();
            \DB::beginTransaction();
            $this->insertCounter = 0;
        }
    }

    /**
     * Store words
     *
     * @param object $paragraph
     * @param string $words
     * @return void
     */
    protected function storeWords($paragraph, $words)
    {
        $this->words = array_merge($this->words, array_flip(explode(' ', $words)));
    }

    /**
     * @param object $paragraph
     * @param string $sentence
     * @param string $sentenceWordList
     * @param int $index
     * @return void
     */
    protected function storeSentence($paragraph, $sentence, $sentenceWordList, $index)
    {
        try {
            $this->tableSentences->insert([
                'para_id' => $paragraph->para_id,
                'index' => $index,
                'content' => $sentence,
                'stemmed_wordlist' => $sentenceWordList,
            ]);
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            if (!str_contains($message, 'Duplicate entry')) {
                Log::error($message);
            }
        }
    }

    /**
     * @param object $paragraph
     * @param array $sentences
     * @param string $words
     * @return void
     */
    protected function storeParagraph($paragraph, $sentences, $parents, $words)
    {
        $paragraphArray = (array)$paragraph;
        unset($paragraphArray['translations']);
        try {
            $this->table->insert(array_merge($paragraphArray, ['stemmed_wordlist' => $words], array_combine(['parent_1', 'parent_2', 'parent_3', 'parent_4', 'parent_5', 'parent_6'], $parents)));
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            if (!str_contains($message, 'Duplicate entry')) {
                Log::error($message);
            }
        }
    }
}
