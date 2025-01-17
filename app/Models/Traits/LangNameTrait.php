<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Models\Language;

trait LangNameTrait
{
    public function names(): mixed
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
            ->where('table_column', 'name')
            ->where('table_name', $this->getTable());
    }

    public function getLangName($langTag): ?string
    {
        return $this->names->where('lang_tag', $langTag)->first()?->lang_content ?: $this->name;
    }
}
