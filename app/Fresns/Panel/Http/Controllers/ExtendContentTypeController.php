<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Language;
use App\Models\Plugin;
use App\Models\PluginUsage;
use Illuminate\Http\Request;

class ExtendContentTypeController extends Controller
{
    public function index()
    {
        $plugins = Plugin::all();

        $plugins = $plugins->filter(function ($plugin) {
            return in_array('extendContentType', $plugin->scene);
        });

        $pluginUsages = PluginUsage::type(PluginUsage::TYPE_CONTENT)->orderBy('rating')->with('plugin', 'names')->paginate();

        return view('FsView::extends.content-type', compact('plugins', 'pluginUsages'));
    }

    public function store(Request $request)
    {
        $pluginUsage = new PluginUsage;
        $pluginUsage->usage_type = PluginUsage::TYPE_CONTENT;
        $pluginUsage->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $pluginUsage->scene = $request->scene ? implode(',', $request->scene) : '';
        $pluginUsage->plugin_fskey = $request->plugin_fskey;
        $pluginUsage->is_enabled = $request->is_enabled;
        $pluginUsage->rating = $request->rating;
        $pluginUsage->can_delete = 1;
        $pluginUsage->data_sources = [
            'postByAll' => [
                'pluginFskey' => $request->post_list,
                'pluginRating' => [],
            ],
            'postByFollow' => [
                'pluginFskey' => $request->post_follow,
                'pluginRating' => [],
            ],
            'postByNearby' => [
                'pluginFskey' => $request->post_nearby,
                'pluginRating' => [],
            ],
        ];
        $pluginUsage->save();

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('plugin_usages')
                    ->where('table_id', $pluginUsage->id)
                    ->where('lang_tag', $langTag)
                    ->first();

                if (! $language) {
                    // create but no content
                    if (! $content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'plugin_usages',
                        'table_column' => 'name',
                        'table_id' => $pluginUsage->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }

        return $this->createSuccess();
    }

    public function update($id, Request $request)
    {
        $pluginUsage = PluginUsage::findOrFail($id);
        $pluginUsage->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $pluginUsage->scene = $request->scene ? implode(',', $request->scene) : '';
        $pluginUsage->plugin_fskey = $request->plugin_fskey;
        $pluginUsage->is_enabled = $request->is_enabled;
        $pluginUsage->rating = $request->rating;
        $dataSources = $pluginUsage->data_sources;

        if ($request->post_all != ($dataSources['postByAll']['pluginFskey'] ?? null)) {
            $dataSources['postByAll'] = [
                'pluginFskey' => $request->post_all,
                'pluginRating' => [],
            ];
        }

        if ($request->post_follow != ($dataSources['postByFollow']['pluginFskey'] ?? null)) {
            $dataSources['postByFollow'] = [
                'pluginFskey' => $request->post_follow,
                'pluginRating' => [],
            ];
        }

        if ($request->post_nearby != ($dataSources['postByNearby']['pluginFskey'] ?? null)) {
            $dataSources['postByNearby'] = [
                'pluginFskey' => $request->post_nearby,
                'pluginRating' => [],
            ];
        }

        if ($request->comment_all != ($dataSources['commentByAll']['pluginFskey'] ?? null)) {
            $dataSources['commentByAll'] = [
                'pluginFskey' => $request->comment_all,
                'pluginRating' => [],
            ];
        }

        if ($request->comment_follow != ($dataSources['commentByFollow']['pluginFskey'] ?? null)) {
            $dataSources['commentByFollow'] = [
                'pluginFskey' => $request->comment_follow,
                'pluginRating' => [],
            ];
        }

        if ($request->comment_nearby != ($dataSources['commentByNearby']['pluginFskey'] ?? null)) {
            $dataSources['commentByNearby'] = [
                'pluginFskey' => $request->comment_nearby,
                'pluginRating' => [],
            ];
        }

        $pluginUsage->data_sources = $dataSources;
        $pluginUsage->save();

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('plugin_usages')
                    ->where('table_id', $pluginUsage->id)
                    ->where('lang_tag', $langTag)
                    ->first();

                if (! $language) {
                    // create but no content
                    if (! $content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'plugin_usages',
                        'table_column' => 'name',
                        'table_id' => $pluginUsage->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }

        return $this->createSuccess();
    }

    public function destroy($id)
    {
        $pluginUsage = PluginUsage::findOrFail($id);
        $pluginUsage->delete();

        return $this->deleteSuccess();
    }

    public function updateSource($id, $key, Request $request)
    {
        $pluginUsage = PluginUsage::findOrFail($id);
        $dataSources = $pluginUsage->data_sources;

        $requestTitles = $request->titles ?: [];
        $requestDescriptions = $request->descriptions ?: [];

        $data = [];
        foreach ($request->ids as $itemKey => $id) {
            $intro = [];
            $titles = json_decode($requestTitles[$itemKey] ?? '', true) ?: [];
            $descriptions = json_decode($requestDescriptions[$itemKey] ?? '', true) ?: [];
            foreach ($this->optionalLanguages as $language) {
                $title = $titles[$language['langTag']] ?? '';
                $description = $descriptions[$language['langTag']] ?? '';
                if (! $title && ! $description) {
                    continue;
                }
                $intro[] = [
                    'title' => $title,
                    'description' => $description,
                    'langTag' => $language['langTag'],
                ];
            }

            $data[] = [
                'id' => $id,
                'intro' => $intro,
            ];
        }

        $dataSources[$key]['pluginRating'] = $data;
        $pluginUsage->data_sources = $dataSources;
        $pluginUsage->save();

        return $this->updateSuccess();
    }
}
