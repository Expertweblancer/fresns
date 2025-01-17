<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class Hashtag extends Model
{
    use Traits\HashtagServiceTrait;
    use Traits\IsEnabledTrait;
}
