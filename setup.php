<?php

/*
This file is part of the GLPI Modifications plugin.

The glpi-modifications plugin is free software;
you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
PGUM srl; either version 2 of the License, or
(at your option) any later version of the GNU General Public License.

The glpi-modifications plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with glpi-modifications. If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------
@package	glpi-modifications
@author		PGUM srl (https://github.com/i-Vertix/glpi-modifications)
@author		Stevenes Donato (https://github.com/stdonato/glpi-modifications)
@copyright	Copyright (c) 2022 PGUM srl
@license	GPLv3
            http://www.gnu.org/licenses/gpl.txt
@link		https://github.com/i-Vertix/glpi-modifications
@link		https://github.com/stdonato/glpi-modifications
@link		https://i-vertix.com
@link		http://www.glpi-project.org/
@since		2022
--------------------------------------------------------------------------
*/

const PLUGIN_GLPI_MODIFICATIONS_VERSION = "10.0.1";

// Minimal GLPI version, included
const PLUGIN_GLPI_MODIFICATIONS_GLPI_MIN_VERSION = "10.0";
// Maximal GLPI version, excluded
const PLUGIN_GLPI_MODIFICATIONS_GLPI_MAX_VERSION = "11.0";

function plugin_init_mod()
{
    global $PLUGIN_HOOKS, $LANG;

    $PLUGIN_HOOKS['csrf_compliant']['mod'] = true;

    $plugin = new Plugin();
    if ($plugin->isInstalled('mod') && $plugin->isActivated('mod')) {

        Plugin::registerClass('PluginMod', [
            'addtabon' => ['Config']
        ]);

        $PLUGIN_HOOKS['config_page']['mod'] = './front/config.php';
    }
}

function plugin_version_mod()
{
    global $LANG;

    return array('name' => __('GLPI Modifications'),
        'version' => PLUGIN_GLPI_MODIFICATIONS_VERSION,
        'author' => '<a href="https://www.pgum.eu/">PGUM s.r.l.</a>',
        'license' => 'GPLv2+',
        'homepage' => 'https://github.com/i-Vertix/glpi-modifications',
        'minGlpiVersion' => PLUGIN_GLPI_MODIFICATIONS_GLPI_MIN_VERSION,
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_GLPI_MODIFICATIONS_GLPI_MIN_VERSION,
                'max' => PLUGIN_GLPI_MODIFICATIONS_GLPI_MAX_VERSION
            ]
        ]);
}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_mod_check_prerequisites() {
    if (version_compare(GLPI_VERSION, PLUGIN_GLPI_MODIFICATIONS_GLPI_MIN_VERSION, 'lt') || version_compare(GLPI_VERSION, PLUGIN_GLPI_MODIFICATIONS_GLPI_MAX_VERSION, 'ge')) {
        echo "This plugin requires GLPI >= " . PLUGIN_GLPI_MODIFICATIONS_GLPI_MIN_VERSION . " and GLPI < " . PLUGIN_GLPI_MODIFICATIONS_GLPI_MAX_VERSION;
        return false;
    }
    return true;
}

function plugin_mod_check_config($verbose = false)
{
    if ($verbose) {
        echo 'Installed / not configured';
    }
    return true;
}

?>
