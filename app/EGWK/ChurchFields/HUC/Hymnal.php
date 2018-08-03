<?php

namespace App\EGWK\ChurchFields\HUC;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Hymnal extends \App\EGWK\Hymnal
    {

    public function getScore($no)
        {
        $scoreList = Cache::rememberForever('score', function () use ($no)
            {
            return $this->scoreList();
            });
        return array_get($scoreList, $no, null);
        }

    public function scoreList()
        {
        $client = new Client();
        try
            {
            $response = $client->get('lily:8080', [
                'connect_timeout' => 10,
                'query' => ['all' => ''],
            ]);
            }
        catch (RequestException $e)
            {
            return $this->errorResponse($e->getResponse());
            }
        catch (\Exception $e)
            {
            return $this->error('Bad Request');
            }
        $data = json_decode((string)$response->getBody(), true);
        return $data;
        }
    }