<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 17/02/2019
 * Time: 12:31
 */

namespace App\EGWK\Datamining;


use App\Facades\Reader;
use App\Models\Tables\Original;
use Facades\App\EGWK\Reader\SearchSimilar;
use Illuminate\Support\Collection;

class ComparePublications
{

    protected $bookList = [];

    protected $bookdata = [];

    protected function bookIds(array $books): array
    {
        return Original::select('refcode_1', 'para_id')
            ->whereIn('refcode_1', $books)
            ->where('puborder', 1)
            ->get()
            ->keyBy('refcode_1')
            ->map(function ($item) {
                return preg_replace('/\..*$/', '.', $item->para_id);
                return $item;
            })
            ->toArray();
    }

    protected function query(string $code)
    {
        return Reader::original($code)
            ->select('para_id', 'refcode_1', 'refcode_4', 'refcode_short', 'element_type', 'content', 'puborder')
//            ->take(100)// for testing only
            ->orderBy('puborder');
    }

    protected function getSimilars($paragraph, $bookIds): Collection
    {
        return SearchSimilar::similarParagraphWithContent($paragraph->para_id, 30)
            ->filter(function ($item) use ($bookIds) {
                return starts_with($item['paragraph']->para_id, $bookIds);
            })
            ->groupBy('paragraph.refcode_1')
            ->map(function ($item) {
                $r = $item
                    ->where('covers', $item->max('covers'))
                    ->first();
                $r['paragraph'] = $r['paragraph']->only('para_id', 'refcode_1', 'refcode_4', 'refcode_short', 'element_type', 'content', 'puborder');
                return $r;
            });
    }

    protected function processBooklist(): void
    {
        $firstBook = array_get($this->bookList, 0);
        $otherBooks = array_slice($this->bookList, 1);
        $bookIds = $this->bookIds($otherBooks);
        foreach ($this->query($firstBook)
                     ->get()
                     ->keyBy('para_id') as $paragraph) {
            $similars = $this->getSimilars($paragraph, $bookIds);
            $this->bookdata[$paragraph->para_id] =
                [
                    'self' => $paragraph->toArray(),
                    'similars' => $similars,
                ];
        }
    }

    public function set(array $bookList): ComparePublications
    {
        $this->bookList = array_filter(array_map('trim', $bookList));
        return $this;
    }

    public function get(): Collection
    {
        $this->processBooklist();
        return collect($this->bookdata);
    }

    public function getBookListTag(array $bookList): string
    {
        return implode('-', $bookList);
    }

    public function getFilePath(array $bookList): string
    {
        return 'compare/' . $this->getBookListTag($bookList) . '.json';
    }

    public function books(): array
    {
        return $this->bookList;
    }

}
