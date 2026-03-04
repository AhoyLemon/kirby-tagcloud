<?php
/**
 * Tag Cloud template
 *
 * Handles two views depending on the request:
 *   /tags         → full tag cloud sorted by usage count
 *   /tags?tag=foo → all pages tagged "foo"
 *
 * Copy this file to site/templates/tagcloud.php
 *
 * This template uses snippet('header') and snippet('footer') — replace
 * these calls with whatever layout system your project uses.
 */

$activeTag = trim(strip_tags(kirby()->request()->get('tag') ?? ''));
?>
<?php snippet('header') ?>

<main>

<?php if ($activeTag !== ''): ?>

  <?php
    $results = $site->index()->listed()
      ->filterBy('tags', $activeTag, ',')
      ->sortBy('date', 'desc')
      ->paginate(15);
  ?>

  <h1>Pages tagged: <?= html($activeTag) ?></h1>

  <p><a href="<?= $site->url() ?>/tags">&larr; All tags</a></p>

  <?php if ($results->isEmpty()): ?>

    <p>No pages found for this tag.</p>

  <?php else: ?>

    <ul>
      <?php foreach ($results as $page): ?>
        <li>
          <a href="<?= $page->url() ?>"><?= $page->title() ?></a>
        </li>
      <?php endforeach ?>
    </ul>

    <?php if ($results->pagination()->hasPages()): ?>
      <nav>
        <?php if ($results->pagination()->hasPrevPage()): ?>
          <a href="<?= $results->pagination()->prevPageUrl() ?>">&larr; Previous</a>
        <?php endif ?>
        <?php if ($results->pagination()->hasNextPage()): ?>
          <a href="<?= $results->pagination()->nextPageUrl() ?>">Next &rarr;</a>
        <?php endif ?>
      </nav>
    <?php endif ?>

  <?php endif ?>

<?php else: ?>

  <h1><?= $page->title() ?></h1>

  <?php
    /**
     * OPTION A — all tags across the entire site, sorted by count (default)
     *   $tags = $site->tagCounts();
     *
     * OPTION B — scoped to specific page templates
     *   $tags = $site->tagCounts(['templates' => ['article', 'project']]);
     *
     * OPTION C — hide tags that appear only once
     *   $tags = $site->tagCounts(['minCount' => 2]);
     *
     * OPTION D — scoped AND hiding single-use tags
     *   $tags = $site->tagCounts([
     *       'templates' => ['article', 'project'],
     *       'minCount'  => 2,
     *   ]);
     *
     * OPTION E — alphabetical order, limited to 30 tags
     *   $tags = $site->tagCounts(['sortBy' => 'name', 'limit' => 30]);
     *
     * OPTION F — random order (e.g. for a decorative cloud)
     *   $tags = $site->tagCounts(['sortBy' => 'random']);
     */
    $tags = $site->tagCounts();
  ?>

  <?php if (empty($tags)): ?>

    <p>No tags found.</p>

  <?php else: ?>

    <ul class="tagcloud">
      <?php foreach ($tags as $tagName => $count): ?>
        <li>
          <a class="tag" href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tagName) ?>">
            <span class="tag-name"><?= html($tagName) ?></span>
            <span class="tag-count"><?= $count ?></span>
          </a>
        </li>
      <?php endforeach ?>
    </ul>

  <?php endif ?>

<?php endif ?>

</main>

<?php snippet('footer') ?>
