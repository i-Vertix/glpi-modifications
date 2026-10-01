# UI Branding 12.0.44

Visual customization plugin for GLPI 12.

## Features

- Custom application title.
- Login screen background.
- Standard and theme-specific logos.
- Custom favicon.
- Resource preview for administrators.
- GLPI cache clearing from the configuration page.
- Resources served through GLPI Controllers.

## Architecture

Version 12.0.44 applies branding **at runtime through hooks and Controllers**, using targeted GLPI 12 logo selectors without replacing files in GLPI `public/pics`.

Administrator-uploaded resources are stored under `GLPI_PLUGIN_DOC_DIR/mod/uploads` and served through plugin routes. Bundled default resources remain inside the installed plugin under `resources/images` and are resolved using GLPI's plugin path API; they are never copied into persistent plugin data. Logos and login branding are applied by runtime CSS/JavaScript loaded through GLPI plugin asset hooks. The favicon is updated by the same runtime asset script.

There are no `front/` or `ajax/` entry points, and the plugin does not include `composer.json`, `composer.lock`, or its own `vendor/` directory.

This version is a **clean architecture and configuration break**. It does not read, migrate, or restore configuration, images, backups, or state from previous plugin generations. An installation coming from an earlier version starts with this version's defaults and must be configured again.

## Requirements

- GLPI 12.0.x
- PHP 8.2 or newer
- Composer is not required.

## Installation

The distributed ZIP must contain a top-level `mod/` directory. In GLPI, install the ZIP from **Setup → Plugins**, then install and enable **UI Branding**.

## Security

Public resource routes only allow known, internally mapped resource keys. Administrative configuration changes require the `config` / `UPDATE` right. Public asset routes expose only the plugin's allowlisted branding resources. Write operations are protected by GLPI's native CSRF mechanism for Controllers.

Uploaded files are validated by extension, actual MIME type, size, and minimum dimensions for the background.
