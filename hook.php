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

use GlpiPlugin\Mod\BrandManager;

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_mod_install()
{
    $brandManager = new BrandManager();
    $brandManager->install();
    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_mod_uninstall()
{
    $brandManager = new BrandManager();
//    $brandManager->changeTitle("i-Vertix");
    $brandManager->uninstall();
    return true;
}

function plugin_mod_activate()
{
    $brandManager = new BrandManager();
    $brandManager->changeTitle("i-Vertix");
    foreach (array_keys(BrandManager::getImageResources()) as $resourceName) {
        $brandManager->applyResource($resourceName);
    }
    $brandManager->applyLoginPageModifier();
}

function plugin_mod_deactivate()
{
    $brandManager = new BrandManager();
//    $brandManager->changeTitle("i-Vertix");
    foreach (array_keys(BrandManager::getImageResources()) as $resourceName) {
        $brandManager->restoreResource($resourceName);
    }
    $brandManager->disableLoginPageModifier();
}
