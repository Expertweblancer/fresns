<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\PluginUsage;
use Illuminate\Http\Request;

class PluginUsageController extends Controller
{
    public function updateRating(int $id, Request $request)
    {
        $pluginUsage = PluginUsage::findOrFail($id);
        $pluginUsage->rating = $request->rating;
        $pluginUsage->save();

        return $this->updateSuccess();
    }

    public function destroy(PluginUsage $pluginUsage)
    {
        $pluginUsage->delete();

        return $this->deleteSuccess();
    }
}
