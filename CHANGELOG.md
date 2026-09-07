## 12.0.12
- Removed the duplicate UI Branding page heading.
- Added a successful-save notification with a cache-clear recommendation.
- Added translations for the save notification in all supported plugin locales.

## 12.0.4
- Targeted and validated against GLPI 12.0.0-rc1 APIs.
- Removed unnecessary ReAuthManager dependency from controllers to avoid Symfony autowiring issues.
- Added CSRF token to the configuration form.
- Fixed Symfony UploadedFile handling for controller-based uploads.
- Fixed subdirectory-safe anonymous background URL.

# Changelog

## 12.0.10

- Placed the "Clear cache" button at the far left of the configuration footer and kept "Save" at the far right.


## 12.0.8

- Localized the cache button and cache status messages in all supported plugin languages.
- Kept the clear-cache refresh icon positioned before the button label on the left side.


## [12.0.7] - 2026-09-07

### Added
- Added a **Clear cache** button to the UI Branding configuration page.
- The button clears GLPI cache contexts, Symfony cache, and compiled Twig templates using GLPI 12 `CacheManager::resetAllCaches()`.
- Cache clearing is protected by the same configuration permission and CSRF protection as the configuration form.

## [12.0.6] - 2026-09-07

### Fixed
- Keep the configuration page heading as **UI Branding** instead of using the configured application title.
- Preserve the configured page title from `resources/modifiers.ini` and from subsequent saves.
- Removed the activation behavior that silently replaced `GLPI` with `i-Vertix`.

## 12.0.3
- Remove hard dependency on `Glpi\Security\ReAuth\ReAuthManager` from controller constructor.
- Use the GLPI 12 re-authentication service only when the class is available at runtime.
- Prevent Symfony plugin container compilation from failing on GLPI 12 builds where that service is not present.

## 12.0.2 - 2026-08-30

- Fixed activation-time 500 errors caused by executable branding logic in `plugin_init_mod()`.
- Kept `plugin_init_mod()` declarative and read-only.
- Added read-only branding state accessors.
- Hardened activation/deactivation with controlled failure handling.
- Kept the plugin compatible with GLPI 12 and without Composer.

## 12.0.1 - 2026-08-30

- GLPI 12 compatibility baseline.
