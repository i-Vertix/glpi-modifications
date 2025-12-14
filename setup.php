// Required by GLPI: alias for plugin_version_mod
function plugin_version_modifications() {
    return plugin_version_mod();
}
<?php

/**
 * -------------------------------------------------------------------------
 * UI Branding plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of UI Branding plugin for GLPI.
 *
 * "UI Branding plugin for GLPI" is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * "UI Branding plugin for GLPI" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "UI Branding plugin for GLPI". If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2025 by i-Vertix/PGUM.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://github.com/i-Vertix/glpi-modifications
 * -------------------------------------------------------------------------
 */

use Glpi\Plugin\Hooks;
use GlpiPlugin\Mod\BrandManager;

const PLUGIN_MOD_VERSION = "11.0.1";

function plugin_init_mod()
{
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['config_page']['mod'] = './front/uibranding.php';
    if (Plugin::isPluginActive("mod")) {
        $CFG_GLPI["app_name"] = BrandManager::getCurrentTitle();
        if (BrandManager::isLoginPageModified()) {
            // little bit hacky - could maybe be obsolete in upcoming versions
            // this piece of hard-researched code enables the public/background.php wrapper file to be accessed without user being logged in
            \Glpi\Http\Firewall::addPluginStrategyForLegacyScripts("mod", '/^\/background.php$/', \Glpi\Http\Firewall::STRATEGY_NO_CHECK);

            $PLUGIN_HOOKS[Hooks::ADD_CSS_ANONYMOUS_PAGE]["mod"] = "./css/mod_anonymous.css";
        }
    }
}

function plugin_version_mod()
{
    global $LANG;

    return array('name' => 'UI Branding',
        'version' => PLUGIN_MOD_VERSION,
        'author' => '<a href="https://www.i-vertix.com/">i-Vertix</a>',
        'license' => 'GPLv3',
        'homepage' => 'https://github.com/i-Vertix/glpi-modifications',
        'requirements' => [
            'glpi' => [
                'min' => "11.0",
                'max' => "12.0"
            ]
        ]);
}
