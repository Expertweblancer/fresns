<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Feature\DTO;

use Fresns\DTO\DTO;

/**
 * Class AddSubscribeItemDTO.
 */
class AddSubscribeItemDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', 'in:1,2,3'],
            'fskey' => ['string', 'required', 'exists:App\Models\Plugin,fskey'],
            'cmdWord' => ['string', 'required'],
            'subTableName' => ['nullable'],
        ];
    }
}