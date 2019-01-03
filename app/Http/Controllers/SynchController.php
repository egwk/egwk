<?php

namespace App\Http\Controllers;

use App\EGWK\Install\Writings\Store;
use App\Models\Tables\Original;
use App\Models\Tables\TranslationDraft;
use Illuminate\Http\Request;
use Facades\App\EGWK\Synch;
use Illuminate\Support\Facades\Storage;

class SynchController extends Controller
{

    /**
     * Get draft file list
     *
     * @return array
     */
    public function translations()
    {
        return [
            'translations' => array_map(
                function ($item) {
                    return basename($item, '.txt');
                },
                glob(Storage::path('synch/') . '*.txt')
            ),
        ];
    }

    /**
     * Get paginated Synch data
     *
     * @param string $translationCode
     * @return array
     */
    public function synch(string $translationCode): array
    {
        $bookCode = str_before($translationCode, '.');
//        $translation = Synch::getTranslationCache($translationCode);
//        $paginatedTranslation = collect($translation)
//            ->paginate($limit);
        return [
            'original' => Original::where('refcode_1', $bookCode)
                ->orderBy('puborder', 'asc')
                ->paginate($this->limit)
            ,
            'translation' => TranslationDraft::select('content', 'seq')
                ->where('code', $translationCode)
                ->orderBy('seq')
                ->paginate($this->limit)
        ];

    }

    /**
     * Save draft segment to DB
     *
     * @param string $translationCode
     * @param array $translation
     * @param int $limit
     * @param int $page
     * @return array
     * @throws \Throwable
     */
    public function save(Request $request, string $translationCode, int $limit)
    {
        return Synch::save(
            $translationCode,
            $request->post('translation'),
            $limit,
            $request->get('page')
        );
    }

}
