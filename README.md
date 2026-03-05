# Kirby Tag Cloud

[![Kirby 5](https://img.shields.io/badge/Kirby_5+-000?style=flat-square&logo=kirby&logoColor=fff&labelColor=000&color=222)](https://getkirby.com/)
[![PHP 8.1](https://img.shields.io/badge/PHP_8.1+-777BB4?style=flat-square&logo=php&logoColor=fff)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

A Kirby 5 plugin that provides a `$site->tagCounts()` site method for building of tag clouds and filtering pages by tag — all without custom routes or controllers.

```
/tags              → full tag cloud, sorted by usage count
/tags?tag=travel   → all pages tagged "travel"
/tags?tag=new+york → all pages tagged "new york"
```

---

## Requirements

- Kirby 5.x
- PHP 8.1+

---

## Installation

### Download

[Download](https://github.com/ahoylemon/kirby-tagcloud/archive/main.zip) and copy the folder into your project as `site/plugins/tagcloud`.

### Git Submodule

```bash
git submodule add https://github.com/ahoylemon/kirby-tagcloud.git site/plugins/tagcloud
```

---

## Setup

### Content page

Create a page at the slug `tags` with a content file named `tagcloud.txt`:

**`content/tags/tagcloud.txt`**

```
Title: Tag Cloud

----

Text: Browse all tags used across the site.
```

### Template

Copy [`samples/templates/tagcloud.php`](samples/templates/tagcloud.php) to `site/templates/tagcloud.php`, or write your own using the examples below.

---

## Usage

### Basic tag cloud

Outputs all tags sorted by frequency, each linking to the filtered results view:

```php
<?php foreach ($site->tagCounts() as $tagName => $count): ?>
  <a href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tagName) ?>">
    <?= html($tagName) ?> (<?= $count ?>)
  </a>
<?php endforeach ?>
```

### Tag cloud scoped to specific templates

Pass a `templates` array to limit which page types are scanned:

```php
<?php foreach ($site->tagCounts(['templates' => ['article', 'project']]) as $tagName => $count): ?>
  <a href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tagName) ?>">
    <?= html($tagName) ?> (<?= $count ?>)
  </a>
<?php endforeach ?>
```

### Hide tags used only once

Use `minCount` to exclude low-frequency tags:

```php
<?php foreach ($site->tagCounts(['minCount' => 2]) as $tagName => $count): ?>
  <a href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tagName) ?>">
    <?= html($tagName) ?> (<?= $count ?>)
  </a>
<?php endforeach ?>
```

### Scoped to a template and hiding single-use tags

```php
<?php foreach ($site->tagCounts(['templates' => ['article'], 'minCount' => 2]) as $tagName => $count): ?>
  <a href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tagName) ?>">
    <?= html($tagName) ?> (<?= $count ?>)
  </a>
<?php endforeach ?>
```

### Alphabetical or random order

```php
// Alphabetical
$site->tagCounts(['sortBy' => 'name'])

// Random (e.g. for a decorative cloud)
$site->tagCounts(['sortBy' => 'random'])
```

### Limiting the number of results

```php
// Top 10 most-used tags
$site->tagCounts(['limit' => 10])
```

### Custom tag field name

If your tag field is not named `tags`, pass the field name:

```php
$site->tagCounts(['field' => 'categories'])
```

To scan multiple fields at once, pass an array:

```php
$site->tagCounts(['field' => ['tags', 'genres']])
```

### Filtering results by tag

The template reads `?tag=` from the request and displays matching pages.
With no `?tag=` parameter the full tag cloud is shown instead.

```php
<?php
$activeTag = trim(strip_tags(kirby()->request()->get('tag') ?? ''));

if ($activeTag !== ''):
    $results = $site->index()->listed()
        ->filterBy('tags', $activeTag, ',')
        ->sortBy('date', 'desc')
        ->paginate(15);
?>
  <h1>Pages tagged: <?= html($activeTag) ?></h1>
  <a href="<?= $site->url() ?>/tags">&larr; All tags</a>

  <?php foreach ($results as $page): ?>
    <a href="<?= $page->url() ?>"><?= $page->title() ?></a>
  <?php endforeach ?>

<?php else: ?>

  <?php foreach ($site->tagCounts() as $tagName => $count): ?>
    <a href="<?= $site->url() ?>/tags?tag=<?= rawurlencode($tagName) ?>">
      <?= html($tagName) ?> (<?= $count ?>)
    </a>
  <?php endforeach ?>

<?php endif ?>
```

---

## `$site->tagCounts(array $options = []): array`

Returns `['tagName' => count]`. All options are optional.

| Option | Type | Default | Description |
|---|---|---|---|
| `field` | `string\|array` | `'tags'` | Field name(s) to read tags from |
| `templates` | `array` | `[]` | Template names to include. Empty = all listed pages |
| `minCount` | `int` | `1` | Minimum occurrences for a tag to be included |
| `sortBy` | `string` | `'count'` | Sort order: `'count'`, `'name'`, or `'random'` |
| `limit` | `int\|null` | `null` | Maximum number of tags to return. `null` = unlimited |

```php
$site->tagCounts([
    'field'     => 'tags',
    'templates' => ['article', 'project'],
    'minCount'  => 2,
    'sortBy'    => 'count',
    'limit'     => 20,
])
```

---

## Configuration

Defaults can be overridden globally in `site/config/config.php` so you don't need to repeat options on every call:

```php
return [
    'ahoylemon.tagcloud.field'     => 'tags',
    'ahoylemon.tagcloud.templates' => [],
    'ahoylemon.tagcloud.minCount'  => 1,
    'ahoylemon.tagcloud.sortBy'    => 'count',
    'ahoylemon.tagcloud.limit'     => null,
];
```

Per-call options always take precedence over config values.

---

## Sample files

| File | Description |
|---|---|
| [`samples/templates/tagcloud.php`](samples/templates/tagcloud.php) | Full tag cloud + filter results template |
| [`samples/snippets/tags.php`](samples/snippets/tags.php) | Per-page tag list linking back to the tag cloud |

---

## License

[MIT](LICENSE)

---

## Credits

Inspired by the Kirby 2 `tagcloud()` helper. Originally developed as part of [TheFPlus](https://github.com/AhoyLemon/TheFPlus).
