<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Content\DTO;

use Fresns\DTO\DTO;

class LogicalDeletionContentDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', 'in:1,2'],
            'contentType' => ['integer', 'required', 'in:1,2'],
            'contentFsid' => ['string', 'nullable', 'required_if:contentType,1'],
            'contentLogId' => ['integer', 'nullable', 'required_if:contentType,2'],
        ];
    }
}
