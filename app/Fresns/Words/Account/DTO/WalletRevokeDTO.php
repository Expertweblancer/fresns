<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Account\DTO;

use Fresns\DTO\DTO;

class WalletRevokeDTO extends DTO
{
    public function rules(): array
    {
        return [
            'logId' => ['integer', 'nullable', 'exists:App\Models\AccountWalletLog,id'],
            'aid' => ['string', 'required', 'exists:App\Models\Account,aid'],
            'uid' => ['integer', 'nullable', 'exists:App\Models\User,uid'],
        ];
    }
}
