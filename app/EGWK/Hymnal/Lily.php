<?php

namespace App\EGWK\Hymnal;

use GuzzleHttp\Client;


class Lily
{
    /**
     * @var array English language numbers used for verse names
     */
    protected $englishNumbers = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
    ];

    /**
     * @var Lily\Config Lily configuration
     */
    protected $config = null;

    /**
     * @var mixed|string Server address
     */
    protected $server = '';

    /**
     * @var mixed|string Port number
     */
    protected $port = '';

    /**
     * @var string Lily Service url
     */
    protected $url = '';

    /**
     * Lily constructor.
     */
    public function setup(Lily\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Copy value by key
     * Helper function
     *
     * @param array $source
     * @param array $target
     * @param string $key
     * @param null $default
     */
    protected function copyByKey(?array $source, array &$target, string $key, $default = null)
    {
        if ($source) {
            $target[$key] = array_get($source, $key, $default);
        } else {
            throw new \Exception('No score');
        }
    }

    /**
     * Convert number to English word
     * Helper function
     *
     * @param string $number
     * @return string
     */
    protected function englishNumber(string $number): string
    {
        return ucfirst(array_get($this->englishNumbers, $number, $number));
    }

    /**
     * Load Lily template by type
     *
     * @param string $type
     * @return string
     */
    protected function getTemplate(): string
    {
        $path = storage_path("data/hymnal/{$this->config->type}.template.ly");
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
    }

    /**
     * Compile verse link tag
     *
     * @param string $number
     * @return string
     */
    protected function compileVerseLink(string $number): string
    {
        $englishNumber = $this->englishNumber($number);
        return
            starts_with($this->config->type, '4') ?
                "\\addlyrics { \\verse$englishNumber }"
                :
                "\\new Lyrics \\lyricsto \"soprano\" \\verse$englishNumber";
    }

    /**
     * Compile hyphenated verse to lyric tag
     *
     * @param string $number
     * @param string $text
     * @return string
     */
    protected function compileVerse(string $number, string $text): string
    {
        $englishNumber = $this->englishNumber($number);
        return "verse$englishNumber = \\lyricmode {\n" .
            "  \\set stanza = \"$number.\"\n" .
            "$text\n" .
            "}\n";
    }

    /**
     * Adds verses to the score, if type requires
     *
     * @param array $data
     */
    protected function addVerses(array &$data): void
    {
        if (strtolower($this->config->verses) !== 'none') {
            $data['verses'] = '';
            $data['verseLinks'] = '';
            $query = \DB::table('api_hymnal_verse')
                ->select()
                ->where('slug', $this->config->slug)
                ->where('hymn_no', $this->config->no);
            if (str_contains(strtolower($this->config->verses), 'all')) {
                $query->orderBy('verse_no');
            } elseif (str_contains($this->config->verses, ',')) {
                $verses = array_filter(explode(',', $this->config->verses),
                    function ($item) {
                        return is_numeric($item);
                    });
                $query->whereIn('verse_no', $verses);
            } else { // (null == $this->config->verses || is_numeric($this->config->verses))
                $this->config->verses = null == $this->config->verses ? 1 : $this->config->verses;
                $query->where('verse_no', $this->config->verses);
            }
            foreach ($query->get() as $verse) {
                $data['verses'] .= $this->compileVerse($verse->verse_no, $verse->lily_hyphenated);
                $data['verseLinks'] .= $this->compileVerseLink($verse->verse_no);
            }
        }
    }


    /**
     * Convert template from score
     *
     * @param array $data
     * @param string $template
     * @return string
     */
    protected function templateToScore(array $data, string $template): string
    {

        $compiled = str_replace([
            '$title',
            '$key',
            '$pianoReduction',
            '$minifySoprano',
            '$header',
            '$mobileSize',
            '$tabletSize',
        ], [
            array_get($data, 'hymn_no', 0) . '. ' . array_get($data, 'title', ''),
            array_get($data, 'key', 'c \\major'),
            $this->config->pianoReduction ? '' : '%',
            $this->config->minifySoprano ? '' : '%',
            $this->config->header && !in_array($this->config->header, ['false', 'off', 0]) ? '' : '%',
            $this->config->size == 'mobile' ? '' : '%',
            $this->config->size == 'tablet' ? '' : '%',
        ], $template);
        foreach (['poet', 'composer', 'arranger', 'tagline', 'key', 'time',
                     'partial', 'soprano', 'alto', 'tenor', 'bass', 'pianoReduction',
                     'verses', 'verseLinks', 'header']
                 as $key) {
            $compiled = str_replace('$' . $key,
                array_get($data, $key, ''), // all empty by default
                $compiled);
        }
        return $compiled;
    }

    /**
     * Compile score
     *
     * @param string $type
     * @param string $slug
     * @param string $no
     * @param string|null $verse
     * @return string
     */
    protected function compile(): string
    {
        $template = $this->getTemplate($this->config->type);
        $result = \DB::table('api_hymnal_song')
            ->select()
            ->where('slug', $this->config->slug)
            ->where('hymn_no', $this->config->no)
            ->get()
            ->toArray();
        $data = (array)array_shift($result);
        $lilyScore = json_decode(array_get($data, 'lily_score', ''), true);
        unset($data['lily_score']);

        foreach (['key', 'time', 'partial',
                     'soprano', 'alto', 'tenor', 'bass',]
                 as $key) {
            $this->copyByKey($lilyScore, $data, $key, '');
        }

        if (empty($data['soprano'])) {
            return '';
        }

        $this->addVerses($data);

        return $this->templateToScore($data, $template);
    }

    /**
     * Fallback image, if not exists
     *
     * @return string
     */
    protected function fallbackImage(): string
    {
        return file_get_contents(storage_path('data/hymnal/score_fallback.png'));
    }

    /**
     * Download image from Lily service
     *
     * @param string $lilyCode
     * @return string
     */
    protected function downloadImage(string $lilyCode): string
    {
        $client = new Client();
        try {
            $response = $client->post($this->config->url, [
                'form_params' => [
                    'png' => 'true',
                    'autotrim' => 'true',
                    'lilycode' => base64_encode($lilyCode),
                ],
            ]);
        } catch (RequestException $e) {
            // todo: handle error, $e->getResponse();
        } catch (\Exception $e) {
            // todo: handle error, $e->getMessage();
        }
        return (string)$response->getBody();
    }

    /**
     * Get cache key
     *
     * @return string
     */
    protected function getKey(): string
    {
        return "hymnal.{$this->config->type}.{$this->config->slug}.{$this->config->no}.{$this->config->verses}.{$this->config->size}";
    }

    public function contentType(): string
    {
//        return 'text/plain'; // for testing only
        switch (strtolower($this->config->format)) {
            case 'png':
                return 'image/png';
        }
    }

    /**
     * Get image
     *
     * @return string
     */
    public function get(): string
    {
        $image = null;
        if ($this->config->cache) {
//            \Cache::flush(); // for testing only
//            \Cache::forget($this->getKey()); // for testing only
            $image = \Cache::get($this->getKey(), null);
        }
        if (null == $image) {
            $lilyCode = $this->compile();
            if (empty($lilyCode)) {
                throw new \Exception('No score');
                // return $this->fallbackImage();
            }
            $image = $this->downloadImage($lilyCode);
            if ($this->config->cache) {
                \Cache::forever($this->getKey(), $image);
            }
        }
        return $image;
    }

}
