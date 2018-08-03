<?php

/**
 * Class to cover Hungarian Sabbath School Quarterly
 *
 */

namespace App\EGWK;

use App\Models\Tables\SabbathSchoolEGW;
use GuzzleHttp\Client;

class SabbathSchool
    {

    public function __construct()
        {
        //\Cache::flush();
        }

    public function getList()
        {
        $currentQuarter = $this->getQuarterNo();
        $currentYear = date('Y');
        $data = [];
        for ($year = $currentYear; $year >= 2005; $year--)
            {
            for ($quarter = 4; $quarter >= 1; $quarter--)
                {
                if ($currentYear == $year && $quarter > $currentQuarter)
                    {
                    continue;
                    }
                $data[] = "/sabbathschool/$year/$quarter";
                }
            }
        return $data;
        }

    public function getQuarter($year, $quarter)
        {
        $data = collect($this->getCachedSSQWrapper("$year-$quarter"))
            ->pluck('uri');
        return $data;
        }

    public function getNoWeeks($year, $quarter)
    {
        return [
            'count' => ceil(count($this->getQuarter($year, $quarter)) / 7),
            'year' => (int)$year,
            'quarter' => (int)$quarter,
        ];
    }

    public function getByDate($date)
        {
        $year = substr($date, 0, 4);
        $quarterNo = $this->getQuarterNo($date);
        foreach ([
                     "$year-$quarterNo",
                     $this->decrQCode($year, $quarterNo),
                     $this->incrQCode($year, $quarterNo)
                 ] as $quarterCode)
            {
            $SSQData = $this->getCachedSSQWrapper($quarterCode);
            $data = array_get($SSQData, $date, []);
            if (!empty($data))
                {
                return $data;
                }
            }
        return [];
        }

    public function getListByWeekNo($year, $quarter, $weekNo)
    {
        $data = $this->getQuarter($year, $quarter)
            ->splice(($weekNo - 1) * 7, 7);
        return $data;
    }

    public function getContentByWeekNo($year, $quarter, $weekNo)
        {
        return collect($this->getCachedSSQWrapper("$year-$quarter"))
            ->splice(($weekNo - 1) * 7, 7);
    }

    protected function getCachedSSQWrapper($quarterCode)
    {
        $data = $this->getCachedSSQ($quarterCode);
        if (empty($data))
        {
            \Cache::forget('sabbathschool.' . $quarterCode);
            $data = $this->getCachedSSQ($quarterCode);
        }
        return $data;
        }

    protected function getCachedSSQ($quarterCode)
        {
        //\Cache::forget('sabbathschool.' . $quarterCode);
        $data = \Cache::rememberForever('sabbathschool.' . $quarterCode, function () use ($quarterCode)
            {
            $data = [];
            foreach ($this->downloadSSQ($quarterCode) as $key => $SSQentry)
                {
                $date = preg_replace('/[^0-9]/', '', $key);
                if (strlen($date) != 8)
                    {
                    continue;
                    }
                $parsed = $this->parseSSQEntry(array_get($SSQentry, 'html', ''));
                $data[$date] = [
                    'title' => $parsed->title,
                    'date' => $this->formatDate($date),
                    'content' => $parsed->content,
                    'egw' => $this->getEGWByDate($date),
                    'uri' => "/sabbathschool/date/$date/",
                ];
                }
            ksort($data);
            return $data;
            });
        return $data;
        }

    protected function downloadSSQ($quarterCode)
        {
        $client = new Client();
        try
            {
            $response = $client->get(env('SSQ_API', '/ssq-api'), [
                'connect_timeout' => 10,
                'query' => ['q' => $quarterCode],
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

    protected function getEGWByDate($date)
        {
        $EGW = SabbathSchoolEGW::where('date', $date)->get()->pluck('content', 'seq');
        return $this->tagScriptures($EGW);
        }

    protected function parseSSQEntry($SSQEntry)
        {
        $parsed = strip_tags($SSQEntry, '<h1><p><span><em>');
        $parsed = $this->characterReplacements($parsed);
        //$parsed = $this->tagScriptures($parsed);
        list($title, $content) = ['', $parsed];
        preg_match('/^\s*<h1>\s*(.+?)\s*<\/h1>\s*(.+?)\s*$/', $parsed, $matches);
        if (count($matches) >= 3)
        {
            array_shift($matches);
            list($title, $content) = $matches;
        }
        return (object)[
            'title' => $title,
            'content' => $content,
        ];
        }

    protected function tagScriptures($parsed)
        {
        //
        // @todo: develop Scripture Markup algorithm
        //
        return $parsed;
        }

    protected function characterReplacements($parsed)
        {
        $parsed = str_replace(
            [
                'class="kerdes"',
                'class="emphasis"',
                'class="elso"',
                'class="kerdesekkekfolyo"',
                'class="xmmbehuzas"',
                'õ',
                'û',
                'Õ',
                'Û',
                ' </p>',
                "\xC2\x80",
                "\xC2\x82",
                "\xC2\x83",
                "\xC2\x84",
                "\xC2\x85",
                "\xC2\x86",
                "\xC2\x87",
                "\xC2\x88",
                "\xC2\x89",
                "\xC2\x8A",
                "\xC2\x8B",
                "\xC2\x8C",
                "\xC2\x8E",
                "\xC2\x91",
                "\xC2\x92",
                "\xC2\x93",
                "\xC2\x94",
                "\xC2\x95",
                "\xC2\x96",
                "\xC2\x97",
                "\xC2\x98",
                "\xC2\x99",
                "\xC2\x9A",
                "\xC2\x9B",
                "\xC2\x9C",
                "\xC2\x9E",
                "\xC2\x9F",
            ],
            [
                'class="question"',
                'class="emphasis"',
                'class="first"',
                'class="question"',
                'class="footer-question"',
                'ő',
                'ű',
                'Ő',
                'Ű',
                '</p>',
                "\xE2\x82\xAC",
                "\xE2\x80\x9A",
                "\xC6\x92",
                "\xE2\x80\x9E",
                "\xE2\x80\xA6",
                "\xE2\x80\xA0",
                "\xE2\x80\xA1",
                "\xCB\x86",
                "\xE2\x80\xB0",
                "\xC5\xA0",
                "\xE2\x80\xB9",
                "\xC5\x92",
                "\xC5\xBD",
                "\xE2\x80\x98",
                "\xE2\x80\x99",
                "\xE2\x80\x9C",
                "\xE2\x80\x9D",
                "\xE2\x80\xA2",
                "\xE2\x80\x93",
                "\xE2\x80\x94",
                "\xCB\x9C",
                "\xE2\x84\xA2",
                "\xC5\xA1",
                "\xE2\x80\xBA",
                "\xC5\x93",
                "\xC5\xBE",
                "\xC5\xB8",
            ],
            $parsed
        );
        return $parsed;
        }

    protected function formatDate($date)
    {
        return substr($date, 0, 4) . "-" .
            substr($date, 4, 2) . "-" .
            substr($date, 6);
    }

    protected function getQuarterNo($date = null)
        {
        $time = null == $date ? time() : strtotime($date);
        return ceil(date("m", $time) / 3);
        }

    protected function decrQCode($year, $quarterNo)
        {
        if ($quarterNo > 1)
            {
            return "$year-" . ($quarterNo - 1);
            }
        return ($year - 1) . "-4";
        }

    protected function incrQCode($year, $quarterNo)
        {
        if ($quarterNo < 4)
            {
            return "$year-" . ($quarterNo + 1);
            }
        return ($year + 1) . "-1";
        }
    }
 
