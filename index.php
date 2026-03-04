<?php

/**
 * kirby-tagcloud plugin
 *
 * Adds a $site->tagCounts(array $options = []) method that returns
 * [ tagName => count ] with configurable field, template filter,
 * minimum count, sort order, and limit.
 *
 * Tag filtering uses plain query strings (?tag=value) so no custom
 * route is needed — /tags?tag=travel renders the tagcloud template
 * as a normal Kirby page.
 *
 * Global defaults can be set in site/config/config.php:
 *
 *   'ahoylemon.tagcloud.field'     => 'tags',
 *   'ahoylemon.tagcloud.templates' => [],
 *   'ahoylemon.tagcloud.minCount'  => 1,
 *   'ahoylemon.tagcloud.sortBy'    => 'count',
 *   'ahoylemon.tagcloud.limit'     => null,
 */

Kirby::plugin('ahoylemon/tagcloud', [

    'options' => [
        'field'     => 'tags',   // string or array of field names
        'templates' => [],       // array of template names; empty = all pages
        'minCount'  => 1,        // minimum tag occurrences to include
        'sortBy'    => 'count',  // 'count' | 'name' | 'random'
        'limit'     => null,     // int or null (unlimited)
    ],

    // ------------------------------------------------------------------
    // $site->tagCounts(array $options = [])
    //
    // Returns [ 'tagName' => int $count, ... ].
    //
    // Per-call options override global config defaults:
    //
    //   $site->tagCounts([
    //       'field'     => 'tags',
    //       'templates' => ['article', 'project'],
    //       'minCount'  => 2,
    //       'sortBy'    => 'name',
    //       'limit'     => 20,
    //   ])
    // ------------------------------------------------------------------
    'siteMethods' => [
        'tagCounts' => function (array $options = []): array {
            /** @var \Kirby\Cms\Site $this */

            $opts = array_merge([
                'field'     => option('ahoylemon.tagcloud.field',     'tags'),
                'templates' => option('ahoylemon.tagcloud.templates', []),
                'minCount'  => option('ahoylemon.tagcloud.minCount',  1),
                'sortBy'    => option('ahoylemon.tagcloud.sortBy',    'count'),
                'limit'     => option('ahoylemon.tagcloud.limit',     null),
            ], $options);

            // Build page collection
            $pages = $this->index()->listed();

            // Filter by template name(s)
            if (!empty($opts['templates'])) {
                $templates = (array)$opts['templates'];
                $pages = $pages->filter(
                    fn($page) => in_array($page->intendedTemplate()->name(), $templates, true)
                );
            }

            // Count tags across one or more fields
            $fields = (array)$opts['field'];
            $counts = [];

            foreach ($pages as $page) {
                foreach ($fields as $fieldName) {
                    $value = $page->$fieldName();
                    if ($value->isEmpty()) continue;
                    foreach (str_getcsv((string)$value) as $raw) {
                        $tag = trim($raw);
                        if ($tag === '') continue;
                        $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                    }
                }
            }

            // Apply minimum count
            if ($opts['minCount'] > 1) {
                $counts = array_filter($counts, fn($c) => $c >= $opts['minCount']);
            }

            // Sort
            switch ($opts['sortBy']) {
                case 'name':
                    ksort($counts);
                    break;
                case 'random':
                    $keys = array_keys($counts);
                    shuffle($keys);
                    $shuffled = [];
                    foreach ($keys as $key) {
                        $shuffled[$key] = $counts[$key];
                    }
                    $counts = $shuffled;
                    break;
                case 'count':
                default:
                    arsort($counts);
                    break;
            }

            // Apply limit
            if ($opts['limit'] !== null) {
                $counts = array_slice($counts, 0, (int)$opts['limit'], true);
            }

            return $counts;
        },
    ],

]);
