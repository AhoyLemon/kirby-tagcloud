<?php
/**
 * Tags snippet — per-page tag list
 *
 * Renders the tags for a single page as links back to the tag cloud.
 * Tags that appear only once across the site are shown as plain text
 * (not linked) since there is nothing else to filter to.
 *
 * Usage in a template:
 *   <?php snippet('tags', ['page' => $page]) ?>
 *
 * Copy this file to site/snippets/tags.php
 */

/** @var \Kirby\Cms\Page $page */
/** @var \Kirby\Cms\Site $site */

// Use the same field name that is configured for the plugin.
$fieldName = option('ahoylemon.tagcloud.field', 'tags');

if ($page->$fieldName()->isEmpty()) return;

// Build a lookup of all tag counts so we can decide what to link.
// Calling tagCounts() here is inexpensive — results are not cached by
// the plugin, so consider caching this yourself if you display this
// snippet on many pages in a single request.
$allTagCounts = $site->tagCounts();

$tags = array_filter(
    array_map('trim', str_getcsv((string)$page->$fieldName())),
    fn($t) => $t !== ''
);

if (empty($tags)) return;
?>

<ul class="tags">
  <?php foreach ($tags as $tag): ?>
    <li>
      <?php if (($allTagCounts[$tag] ?? 0) > 1): ?>
        <a class="tag" href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tag) ?>">
          <?= html($tag) ?>
        </a>
      <?php else: ?>
        <span class="tag tag--single"><?= html($tag) ?></span>
      <?php endif ?>
    </li>
  <?php endforeach ?>
</ul>
