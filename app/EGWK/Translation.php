<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 08/08/2018
 * Time: 08:15
 */

namespace App\EGWK;


abstract class Translation
{
    /**
     * @param mixed $id ID of item to be translated
     * @param int $threshold Original item similarity threshold
     * @param bool $multiSimilar Add multiple similar paragraphs to the result
     * @param bool $multiTranslation Add multiple translations to the result
     * @param null $lang Translate to a specific language, or all available languages id null
     * @param null $preferredPublisher Using the translation of a preferred publisher only
     * @return mixed Translation
     */
    abstract public function translate($id, $threshold = 70, $multiSimilar = false, $multiTranslation = false, $lang = null, $preferredPublisher = null);

}
