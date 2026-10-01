<?php

namespace GlpiPlugin\Mod;

use Html;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

/**
 * Centralized web paths for the UI Branding plugin.
 *
 * GLPI plugin URLs must not depend on whether the plugin files are physically
 * installed below /plugins or /marketplace. GLPI provides the canonical
 * /plugins/<plugin> web prefix for both installation methods.
 */
final class Paths
{
    private const WEB_PREFIX = '/plugins/mod';

    public static function getWebPrefix(): string
    {
        return Html::getPrefixedUrl(self::WEB_PREFIX);
    }

    public static function getControllerUrl(string $path = ''): string
    {
        return self::getWebPrefix() . '/' . ltrim($path, '/');
    }

    public static function getAssetUrl(string $resource, string $version): string
    {
        return self::getControllerUrl('UIBranding/Asset/' . rawurlencode($resource))
            . '?v=' . rawurlencode($version);
    }

    public static function getBrandingCssUrl(string $version): string
    {
        return self::getControllerUrl('UIBranding/BrandingCss')
            . '?v=' . rawurlencode($version);
    }

    public static function getConfigUrl(): string
    {
        return self::getControllerUrl('UIBranding');
    }
}
