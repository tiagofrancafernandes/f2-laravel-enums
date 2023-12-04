<?php

namespace TiagoF2\Helpers;

use Illuminate\Support\Facades\App;

class LaravelHelpers
{
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    public static function trans($key = null, $replace = [], $locale = null)
    {
        /**
         * @var \Illuminate\Contracts\Translation\Translator $translator
         */
        $translator = App::get('translator');

        if (is_null($key)) {
            return $translator;
        }

        return $translator->get($key, $replace, $locale);
    }
}
