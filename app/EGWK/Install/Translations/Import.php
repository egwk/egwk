<?php

namespace App\EGWK\Install\Translations;

use App\EGWK\Install\Exception\TranslationsException;

/**
 * Import translations
 * 
 * @author Peter
 */
class Import
{

    use \App\EGWK\Install\Writings\Tools\ProcessLog;

    /**
     * Default publisher code
     */
    const DEFAULT_PUBLISHER = 'unknown';

    /**
     * Buffer size limit in bytes
     */
    const LIMIT_SIZE = 65535;

    /**
     * Buffer record count limit
     */
    const LIMIT_COUNT = 255;

    /**
     *
     * @var \App\EGWK\Install\Translations\Store
     */
    protected $metadataService = null;

    /**
     *
     * @var \App\EGWK\Install\Translations\Metadata
     */
    protected $storeService = null;

    /**
     *
     * @var string
     */
    protected $lang = 'hu';

    /**
     *
     * @var string
     */
    protected $extension = '.xlsx';

    /**
     *
     * @var string 
     */
    protected $dataType = DataFile\Excel\Filtered::class;

    /**
     * Class constructor
     * 
     * @param \App\EGWK\Install\Translations\DataFile $dataFileFactory Data file service
     * @param \App\EGWK\Install\Translations\Metadata $metadataService Metadata service
     * @param \App\EGWK\Install\Translations\Store $storeService Record insertion service
     * @param string $lang Language
     * @param string $extension Extension
     * @return void
     */
    public function __construct(Metadata $metadataService, Store $storeService, string $lang = 'hu', string $dataType = DataFile\Excel\Filtered::class)
    {
        $this->metadataService = $metadataService;
        $this->storeService = $storeService;
        $this->lang = $lang;
        $this->extension = DataFile::extensionFor($dataType);
        $this->dataType = $dataType;
    }

    /**
     * Imports translations
     *  
     * @access public
     * @return void
     */
    public function translations()
    {
        foreach (glob(storage_path("import/$this->lang") . '/*' . $this->extension) as $dataFilePath)
        {
            $basename = basename($dataFilePath, $this->extension);
            $this
                    ->logBr()
                    ->logBr()
                    ->logProc([$dataFilePath])
                    ->logProc($this->getDetails($basename), 1);

            $metadata = $this->metadataService->getMetadata($basename);
            $this->storeMetadata($this->storeService, $metadata);

            $dataFile = DataFile::factory($this->dataType, $dataFilePath);

            $recordBuffer = [];
            foreach ($dataFile->iterate() as $row)
            {
                $this->logTick();
                $recordBuffer[] = $this->getCurrentRecord($dataFile, $metadata, $this->lang);
                if ($this->isBufferFull($recordBuffer))
                {
                    $this->storeRecords($this->storeService, $recordBuffer);
                    $recordBuffer = [];
                }
            }
            if (!empty($recordBuffer))
            {
                $this->storeRecords($this->storeService, $recordBuffer);
            }
        }
        $this->logBr();
    }

    /**
     * Is the record buffer full?
     * 
     * @param array $recordBuffer Record buffer
     * @return bool
     */
    protected function isBufferFull(array $recordBuffer): bool
    {
        $size = mb_strlen(serialize($recordBuffer), '8bit');
        $count = count($recordBuffer);
        return $size >= self::LIMIT_SIZE || $count >= self::LIMIT_COUNT;
    }

    /**
     * Details
     * 
     * @param string $snakeCaseDetails String containing book details separated by underscore (derived from file name)
     * @return array Book code, publisher and year
     * @throws Exception
     */
    protected function getDetails(string $snakeCaseDetails): array
    {
        $details = explode('_', $snakeCaseDetails);
        $publisher = self::DEFAULT_PUBLISHER;
        $code = $year = '';
        switch (count($details))
        {
            case 1:
                list($code) = $details;
                break;
            case 2:
                list($code, $publisher) = $details;
                break;
            case 3:
                return $details;
            default:
                throw new Exception("Invalid translation ID: $snakeCaseDetails.");
        }
        return [$code, $publisher, $year];
    }

    /**
     * Getting current record
     * 
     * @param \App\EGWK\Install\Translations\DataFile $dataFile
     * @param \stdClass $metadata
     * @param string $lang
     * @return array Current record
     */
    protected function getCurrentRecord(DataFile $dataFile, \stdClass $metadata, string $lang): array
    {
        $translation = $dataFile->getTranslation();
        list(,, $para_id, ) = $dataFile->getOriginal();
        return [
            'lang' => $lang,
            'book_code' => $metadata->book_code,
            'publisher' => $metadata->publisher_code,
            'year' => $metadata->year,
            'no' => $metadata->no,
            'para_id' => $para_id,
            'content' => $translation
        ];
    }

    /**
     * Buffer interval
     * 
     * @param array $recordBuffer
     * @return array
     */
    protected function getRecordsInterval(array $recordBuffer): array
    {
        $first = array_shift($recordBuffer);
        $last = array_pop($recordBuffer);
        return [array_get($first, 'para_id'), array_get($last, 'para_id')];
    }

    /**
     * Stores metadata
     * 
     * @param \App\EGWK\Install\Translations\Store $storeService
     * @param \stdClass $metadata Metadata
     */
    protected function storeMetadata(Store $storeService, \stdClass $metadata)
    {
        try
        {
            $storeService->metadata($metadata);
        } catch (TranslationsException $e)
        {
            $this
                    ->logBr()
                    ->logProc(["Note: not inserted: $metadata->book_code"], 1)
                    ->logBr();
        }
    }

    /**
     * Stores Records
     * 
     * @param \App\EGWK\Install\Translations\Store $storeService
     * @param array $recordBuffer Buffer
     */
    protected function storeRecords(Store $storeService, array $recordBuffer)
    {
        try
        {
            $storeService->records($recordBuffer);
        } catch (TranslationsException $e)
        {
            $boundaries = $this->getRecordsInterval($recordBuffer);
            $this
                    ->logBr()
                    ->logProc(array_merge(['Note: not inserted!'], $boundaries), 1)
                    ->logBr();
        }
    }

}
