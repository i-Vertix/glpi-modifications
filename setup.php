<?php

/**
 * UI Branding plugin for GLPI 12.
 *
 * @copyright Copyright (C) 2026 by i-Vertix/PGUM.
 * @license GPLv3
 */

use Glpi\Plugin\Hooks;
use GlpiPlugin\Mod\BrandingHooks;
use GlpiPlugin\Mod\BrandManager;

const PLUGIN_MOD_VERSION = '12.0.44';
const PLUGIN_MOD_MIN_GLPI_VERSION = '12.0.0';
const PLUGIN_MOD_MAX_GLPI_VERSION = '13.0.0';

if (!defined('PLUGIN_MOD_AUTOLOAD_REGISTERED')) {
    define('PLUGIN_MOD_AUTOLOAD_REGISTERED', true);

    spl_autoload_register(static function (string $class): void {
        $prefix = 'GlpiPlugin\\Mod\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    });
}

function plugin_init_mod(): void
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS[Hooks::CONFIG_PAGE]['mod'] = 'UIBranding';

    BrandingHooks::init();
}

function plugin_version_mod(): array
{
    return [
        'name'         => 'UI Branding',
        'version'      => PLUGIN_MOD_VERSION,
        'author'       => '<a href="https://www.i-vertix.com/">i-Vertix</a>',
        'license'      => 'GPLv3',
        'homepage'     => 'https://github.com/i-Vertix/glpi-modifications',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_MOD_MIN_GLPI_VERSION,
                'max' => PLUGIN_MOD_MAX_GLPI_VERSION,
            ],
            'php' => [
                'min' => '8.2',
            ],
        ],
    ];
}
