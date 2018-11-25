<?php
namespace App\EGWK\Hymnal\Lily;



class Config
{

    /**
     * @var string Score type
     */
    public $type = 'SATB';

    /**
     * @var string | null Verse numbers
     *
     * null : verse #1
     * 1,3  : verse #1 and #3
     * all  : all verses
     * none : no verses
     */
    public $verses = null;

    /**
     * @var string Output format
     */
    public $format = 'png';

    /**
     * @var string Normal size
     */
    public $size = 'normal';

    /**
     * @var string Hymnal slug identifier
     */
    public $slug = '';

    /**
     * @var string Hymn number
     */
    public $no = '1';

    /**
     * @var bool Use cache?
     */
    public $cache = true;

    /**
     * @var bool Minify soprano?
     */
    public $minifySoprano = false;

    /**
     * @var bool Is header needed?
     */
    public $header = false;

    /**
     * @var mixed|string Server Address
     */
    public $server = 'lily';

    /**
     * @var mixed|string Server Port
     */
    public $port = '8008';

    /**
     * @var string Lily Server URL
     */
    public $url = '';

    /**
     * @var bool Piano Reduction
     */
    public $pianoReduction = false;

    protected $allowedKeys = ['cache', 'type', 'verses', 'format',
        'size', 'slug', 'no', 'pianoReduction', 'minifySoprano', 'header'];

    public function __construct(array $config = [])
    {
        $this->server = env('LILY_HOST', 'lily');
        $this->port = env('LILY_PORT', '8008');
        $this->url = $this->server . ':' . $this->port . '/lilyserver.php';
        foreach ($config as $key => $value) {
            if (in_array($key, $this->allowedKeys)) {
                $this->{$key} = $value;
            }
        }
    }

}
