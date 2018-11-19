<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 18/11/2018
 * Time: 18:57
 */

namespace App\EGWK\Hymnal;

use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\str;

class Lily
{

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
    public function __construct()
    {
        $this->server = env('LILY_HOST', 'lily');
        $this->port = env('LILY_PORT', '8008');
        $this->url = $this->server . ':' . $this->port . '/lilyserver.php';
    }

    /**
     * Load Lily template by type
     *
     * @param string $type
     * @return string
     */
    protected function getTemplate(string $type): string
    {

        $path = storage_path('app/score/' . strtoupper($type) . '.template.ly');
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
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
            '$poet',
            '$composer',
            '$arranger',
            '$tagline',
            '$key',
            '$time',
            '$partial',
            '$soprano',
            '$alto',
            '$tenor',
            '$bass',
            '$pianoReduction',
        ], [
            array_get($data, 'hymn_no', 0) . '. ' . array_get($data, 'title', ''),
            array_get($data, 'poet', ''),
            array_get($data, 'composer', ''),
            array_get($data, 'arranger', ''),
            array_get($data, 'tagline', ''),
            array_get($data, 'key', 'c \\major'),
            array_get($data, 'time', ''),
            array_get($data, 'partial', ''),
            array_get($data, 'soprano', ''),
            array_get($data, 'alto', ''),
            array_get($data, 'tenor', ''),
            array_get($data, 'bass', ''),
            array_get($data, 'pianoReduction', false) ? '' : '%',
        ], $template);
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
    protected function compile(string $type, string $slug, string $no, string $verse = null): string
    {
        $template = $this->getTemplate($type);
        $result = \DB::table('api_hymnal_song')
            ->select()
            ->where('slug', $slug)
            ->where('hymn_no', $no)
            ->get()
            ->toArray();
        $data = (array)array_shift($result);
        $lilyScore = json_decode(array_get($data, 'lily_score', ''), true);
        unset($data['lily_score']);
        $data['soprano'] = array_get($lilyScore, 'soprano', '');
        $data['alto'] = array_get($lilyScore, 'alto', '');
        $data['tenor'] = array_get($lilyScore, 'tenor', '');
        $data['bass'] = array_get($lilyScore, 'bass', '');

        if (empty($data['soprano'])) {
            return '';
        }
        return $this->templateToScore($data, $template);
    }

    /**
     * Fallback image, if not exists
     *
     * @return string
     */
    protected function fallbackImage(): string
    {
        return file_get_contents(storage_path('app/score/score_fallback.png'));
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
            $response = $client->post($this->url, [
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
     * Get (cached) image
     *
     * @param string $type
     * @param string $slug
     * @param string $no
     * @param string|null $verse
     * @return string
     */
    public function getImage(string $type, string $slug, string $no, string $verse = null): string
    {
        $key = "hymnal.$type.$slug.$no.$verse";
//        \Cache::forget($key);
        $image = \Cache::get($key, null);
        if (null == $image) {
            $lilyCode = $this->compile($type, $slug, $no, $verse);
            if (empty($lilyCode)) {
                return $this->fallbackImage();
            }
            $image = $this->downloadImage($lilyCode);
            \Cache::forever($key, $image);
        }
        return $image;
    }

}
