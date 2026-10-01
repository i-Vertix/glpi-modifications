## 12.0.44 - 2026-10-01

### Fixed

- Separated bundled default branding resources from administrator uploads.
- Store only administrator-uploaded resources under `GLPI_PLUGIN_DOC_DIR/mod/uploads`.
- Resolve bundled defaults directly from the installed plugin `resources/images` directory instead of copying them into persistent data during installation.
- This keeps plugin defaults updateable while preserving administrator customizations independently.

## 12.0.44 - 2026-10-01

### Fixed

- Centralized plugin web URL generation using GLPI's canonical `/plugins/mod` path, independent of the physical installation location (`plugins` or `marketplace`).
- Removed duplicated `/plugins` versus `/marketplace` detection from controllers and branding hooks.
- Kept physical plugin filesystem resolution unchanged via GLPI's `Plugin::getPhpDir()` mechanism.

## [12.0.42] — 2026-10-01

### Performance
- Added preload hints for the branding logos used by the current GLPI interface state.
- Preloads only the active theme logo variants and avoids loading unnecessary logo sizes on each page.
- Preserved the existing runtime logo overrides and immutable asset caching without modifying GLPI core files.

## [12.0.41] — 2026-10-01

### Fixed
- Restored runtime logo overrides so custom Small, Medium and Large logos are applied again on GLPI 12.
- Kept content-based stylesheet caching from 12.0.40 while using the selectors proven to work with GLPI's stylesheet order.
- Preserved the collapsed-sidebar behavior: Small logo only when the vertical menu is collapsed.

## [12.0.40] — 2026-10-01

### Changed
- Reworked runtime logo branding to override GLPI 12's native `--glpi-logo*` CSS variables instead of replacing `.glpi-logo` background rules.
- Small, Medium and Large logo selection now follows GLPI's native reduced-logo mechanism for the collapsed sidebar and login page.
- Added content-based versioning to the generated branding stylesheet and long-lived immutable browser caching to avoid visible branding reloads during normal navigation.
- Branding asset URLs now use SHA-256 fingerprints so cached images are replaced only when their contents change.

## [12.0.39] — 2026-10-01

### Fixed
- Removed the obsolete `Please activate the plugin` translation entry from all language catalogs.
- Updated the configuration page CSS comment to reflect the current GLPI 12 `page_skeleton` layout.

## [12.0.38] — 2026-10-01

### Fixed
- Fixed the vertical sidebar logo selection so the Small logo is used only when the left menu is collapsed; the Medium logo is used when the menu is expanded.
- Preserved the existing theme-based logo behavior for light and dark themes.

## [12.0.36] — 2026-09-16

### Added
- Live image previews: newly selected background, logo, and favicon files are previewed immediately in the configuration page before saving.

### Changed
- The save confirmation now recommends clearing both the GLPI cache and the browser cache to ensure all branding changes are visible.
- Reworked the configuration page to use the full available GLPI content width.
- Restored the visual hierarchy of the original GLPI 11 interface with blue ribbon-style section headings.
- Added visible nested section cards for Apply Modifications, Custom Images, Standard logos, and Theme-based logos.
- Improved responsive layout for configuration fields and image previews.
- Kept image previews non-clickable and without download controls.

## [12.0.28] - 2026-09-15

### Fixed
- Fixed favicon replacement on GLPI 12 by applying a single custom favicon after the document head is parsed.
- Prevented the native GLPI favicon from remaining as a competing icon declaration.

## [12.0.27] - 2026-09-15

### Fixed
- Fixed custom favicon application on GLPI 12 by replacing GLPI's built-in favicon link after the document head has been parsed.
- Kept favicon delivery fully runtime-based without modifying GLPI core files.

# Changelog

## [12.0.26] - 2026-09-15

### Fixed
- Corrected the bundled favicon file so it is a valid ICO resource.
- Explicitly serves the favicon route with the `image/x-icon` content type.

All notable changes to UI Branding are documented in this file.

## [12.0.25] — 2026-09-15

### Fixed
- Fixed bundled branding resources returning HTTP 404 when the plugin is installed from the GLPI Marketplace.
- Resource paths are now resolved through GLPI's `Plugin::getPhpDir()` API instead of deriving paths from the PHP class location.
- Configuration previews and runtime branding now resolve the same plugin resource directory.

## [12.0.24] — 2026-09-15

### Fixed
- Unified configuration previews and runtime branding assets under the same `AssetController` route.
- Removed the obsolete `ResourceController` endpoint.
- Fixed broken preview URLs in Marketplace and regular plugin installations.

## [12.0.23] — 2026-09-15

### Fixed
- Added bundled-asset fallback when an administrator has not uploaded a custom resource.
- Fixed configuration previews returning 404 when the plugin data directory has not been initialized.

## [12.0.22] — 2026-09-14

### Fixed
- Reworked runtime branding asset delivery for GLPI 12 Marketplace installations.
- Replaced the invalid dynamic CSS implementation that used `sprintf()` with direct CSS construction.
- Branding CSS is served by a public GLPI Controller and linked through the supported header-tag hook.
- Preview resources no longer require the configuration permission.
- Custom logos, login background and favicon resolve through plugin-owned runtime routes without modifying GLPI core files.
- Removed the obsolete browser-side branding JavaScript and static branding CSS files.

## [12.0.21] — 2026-09-15

### Fixed
- Fixed runtime branding not overriding GLPI 12 core logo styles because the previous branding stylesheet was loaded before core CSS.
- Moved runtime branding CSS to a plugin-controlled route loaded through the supported plugin header integration.
- Branding remains fully runtime-based and does not modify GLPI core files.

## [12.0.20] — 2026-09-14

### Fixed
- Fixed malformed `ADD_HEADER_TAG` registration that rendered `< /> < />` on GLPI pages.
- Registered header tags in the structure consumed by GLPI 12's plugin view extension instead of registering a callback.
- Fixed dynamic branding CSS URLs for both `/plugins/mod` and `/marketplace/mod`.

## [12.0.19] — 2026-09-14

### Fixed
- Fixed runtime plugin resource path resolution.
- Fixed configuration and asset URLs for regular and Marketplace installations.
- Exceptions raised while saving settings are passed to GLPI's error handler.

## [12.0.18] — 2026-09-14

### Fixed
- Fixed unqualified GLPI class references in the plugin namespace.
- Removed exception swallowing from plugin lifecycle hooks.

## [12.0.14] — 2026-09-14

### Changed
- Rewritten branding architecture for GLPI 12.
- Branding is applied at runtime without modifying GLPI core assets under `public/pics`.
- Logos are applied through GLPI CSS variables and runtime rules.
- Favicon and login background are served through plugin routes.
- Removed legacy `front/` and `ajax/` plugin entry points.
- Added a clean GLPI 12 data area under `GLPI_PLUGIN_DOC_DIR/mod/v12`.
- Previous plugin generations are not migrated; administrators must configure this generation again.

### Security
- Public asset routes only expose allowlisted branding resources.
- Administrative configuration operations require the GLPI configuration update right.
- Configuration POST requests use GLPI's native CSRF mechanism.

### Compatibility
- GLPI 12.0.x
- PHP >= 8.2
- No Composer
