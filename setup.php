<?php

/**
 * UI Branding plugin for GLPI 12.
 *
 * @copyright Copyright (C) 2026 by i-Vertix/PGUM.
 * @license GPLv3
 */

use Glpi\Plugin\Hooks;
use GlpiPlugin\Mod\BrandManager;

define('PLUGIN_MOD_VERSION', '12.0.12');
define('PLUGIN_MOD_MIN_GLPI_VERSION', '12.0.0');
define('PLUGIN_MOD_MAX_GLPI_VERSION', '13.0.0');

/**
 * Initialize the plugin.
 *
 * This function must only declare hooks. Installation/activation work is
 * intentionally kept out of the initialization phase.
 */
function plugin_init_mod(): void
{
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['config_page']['mod'] = 'UIBranding';

    // Read-only branding values. Never create/write plugin files here.
    try {
        $title = BrandManager::getConfiguredTitle();
        if ($title !== '') {
            $CFG_GLPI['app_name'] = $title;
        }

        if (BrandManager::isLoginPageModifierEnabled()) {
            $PLUGIN_HOOKS[Hooks::ADD_CSS_ANONYMOUS_PAGE]['mod'] = 'css/mod_anonymous.css';
        }
    } catch (Throwable) {
        // Initialization must never prevent GLPI from loading the plugin.
    }
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
