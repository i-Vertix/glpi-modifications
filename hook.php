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

function plugin_mod_install()
{
    include_once(GLPI_ROOT . '/plugins/mod/inc/filemanager.php');
    createLoginPageBackup();
    createHeadBackup();
    createLogoBackup();
    createFaviconBackup();
    return true;
}

function plugin_mod_uninstall()
{
    return true;
}

function plugin_mod_activate()
{
    include_once(GLPI_ROOT . '/plugins/mod/inc/filemanager.php');
    overwriteLoginPage();
    overwriteHead("i-Vertix");
    overwriteLogos();
    overwriteFavicon();
}

function plugin_mod_deactivate()
{
    include_once(GLPI_ROOT . '/plugins/mod/inc/filemanager.php');
    restoreLoginPage();
    restoreHeadBackup();
    restoreLogoBackup();
    restoreFaviconBackup();
}

?>
