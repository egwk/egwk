<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 08/08/2018
 * Time: 08:16
 */

namespace App\EGWK\Translation;

use App\EGWK\Translation;
use App\Facades\Reader;
use App\Facades\Reader\SearchSimilar;
use App\Models\Tables\Translation as TranslationTable;

class CompileBook extends Translation
{

    protected function getTranslations($paraId, $multiTranslation = false, $lang = null, $preferredPublisher = null)
    {
        $translationsQuery = TranslationTable::where('para_id', $paraId);
        !empty($lang) and $translationsQuery->where('lang', $lang);
        !empty($publisher) and $translationsQuery->where('publisher', $preferredPublisher);
        if ($multiTranslation) {
            return $translationsQuery->get();
        } else {
            $translationsTmp = $translationsQuery->first();
            return (empty($translationsTmp) || empty($translationsTmp->content)) ? [] : [$translationsTmp];
        }
    }

    /**
     * @inheritdoc
     */
    public function translate($book, $threshold = 70, $multiTranslation = false, $lang = null, $preferredPublisher = null)
    {
        $compilation = [];
        foreach (Reader::original($book)->get() as $paragraph) {
            $similars = [];
            foreach (
                SearchSimilar::similarParagraphWithContent($paragraph->para_id, $threshold)
                    ->all()
                as $similarParagraph
            ) {
                $similarParagraph['translations'] = $this->getTranslations(
                    $similarParagraph['paragraph']->para_id,
                    $multiTranslation,
                    $lang,
                    $preferredPublisher
                );
                $similars[] = (object) $similarParagraph;
            }
            $compilation[] = (object) [
                'paragraph' => $paragraph,
                'similars' => $similars,
            ];
        }
        return $compilation;
    }

}
