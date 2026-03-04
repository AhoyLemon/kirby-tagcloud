## Genericize This Plugin

This repo was originally spun off from https://github.com/AhoyLemon/TheFPlus/tree/issue/113-kirby5/site/plugins/tags

It works great over there, but now I'd like to make it available for anyone to use. I plan to list it on https://plugins.getkirby.com

## The Plan

I'd like for you to use https://github.com/bnomei/kirby3-feed/ as an example. and create one or more sample templates or snippets that will show to use the plugin in a realworld scenario. I've included `samples\fplus-tags.php` to show how I'm using it in my project, but I want something not with any project-specific code in it.

## Acceptance Criteria
- [x] README describes installation
- [x] Shows how to build a tag cloud
- [x] Shows how to filter by tag
- [x] Shows an example of a tag cloud that's filtered to certain templates
- [x] Shows an example of a tag cloud that doesn't list pages with only 1 use.


## Bonus
- [x] If it's possibe to get this plugin installable via composer, that would be great.

## Completed Work

- Rewrote `README.md` following the kirby plugin ecosystem conventions (badges, installation methods, usage examples, API docs, disclaimer, license, credits)
- Added three installation methods: download, git submodule, and `composer require`
- Updated `composer.json`: added `getkirby/composer-installer` as a requirement (enables `composer require ahoylemon/kirby-tags`), fixed license to MIT, bumped version to `1.0.0`
- Fixed `LICENSE` file (it contained a copy of the old README; replaced with proper MIT license text)
- Created `samples/templates/tagcloud.php` — generic tag cloud + filter results template with four commented usage variants (all tags, scoped, min-count, scoped + min-count)
- Created `samples/snippets/tags.php` — per-page tag list snippet; single-use tags render as plain text rather than links