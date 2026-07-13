# NoorBlocks

A Gutenberg blocks and patterns library built with a modern OOP architecture: advanced blocks, reusable sections, full page templates, and an editor template library.

## Development setup

```bash
npm install          # JS build tooling (@wordpress/scripts)
composer install     # PHP coding-standards tooling
npm run build        # compile blocks + library app to build/
```

Use `npm start` for watch mode while developing.

## Quality checks

| Command | What it does |
| --- | --- |
| `composer lint` | PHPCS with WordPress Coding Standards (`phpcs.xml.dist`) |
| `composer lint:fix` | Auto-fix PHPCS violations |
| `npm run lint:js` | ESLint via @wordpress/scripts |
| `npm run lint:css` | Stylelint via @wordpress/scripts |

All of these run in CI on every push (`.github/workflows/ci.yml`), which also uploads an installable `noorblocks.zip` artifact.

## Releasing

```bash
npm run release
```

Builds the assets and produces `noorblocks.zip` containing only the distributable files (defined in the `files` field of `package.json`) — no sources, tooling, or dev dependencies.

## Architecture

- `noorblocks.php` — bootstrap: constants, PSR-4 autoloader, boots `NoorBlocks\Plugin`
- `includes/` — all PHP under the `NoorBlocks\` namespace:
  - `Blocks\Manager` — auto-registers every `build/blocks/*/block.json`; per-block disable option
  - `Patterns\Manager` — auto-registers `patterns/sections/*.php` and `patterns/pages/*.php`; feeds the template library
  - `Assets\Manager` — enqueues the template library app in the editor
  - `Admin\Dashboard` — block enable/disable settings page
- `src/blocks/<name>/` — block sources (block.json apiVersion 3, edit/save, SCSS)
- `src/library/` — the editor Template Library app (Templates button in the top bar)
- `patterns/` — section and page templates; page templates compose sections via `Patterns\Manager::get_section_content()`

### Adding a block

Copy an existing folder in `src/blocks/`, rename the block in its `block.json`, and run `npm run build`. Registration, the dashboard toggle, and the inserter category are automatic.

### Adding a section or page template

Drop a new `.php` file returning a pattern array into `patterns/sections/` or `patterns/pages/`. It is registered and appears in the template library automatically — no rebuild needed.
