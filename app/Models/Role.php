<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class Role extends Model
{
    const TYPE_ADMIN = 1;
    const TYPE_SYSTEM = 2;
    const TYPE_USER = 3;

    use Traits\LangNameTrait;
    use Traits\IsEnabledTrait;

    protected $casts = [
        'permissions' => 'json',
    ];
}
