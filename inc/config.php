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

include("../../../inc/includes.php");
include("../../../inc/config.php");
include("filemanager.php");

if (!defined("GLPI_MOD_DIR")) {
    define("GLPI_MOD_DIR", GLPI_ROOT . "/plugins/mod");
}

Html::header('UI Modification', $_SERVER["PHP_SELF"],
    "admin", "plugins", "mod");

$plugin = new Plugin();

if ($plugin->isActivated("mod")) {

    $loginOverwritten = isLoginPageBackupPresent();
    $faviconOverwritten = isFaviconBackupPresent();
    $logosOverwritten = isLogoBackupPresent();

    if (isset($_POST['update'])) {
        if (isset($_FILES['background']['name']) && $_FILES['background']['name'] !== '') {
            uploadImage($_FILES['background'], ["jpg", "jpeg"], 'background.jpg');
        }

        $logosChanged = false;
        if (isset($_FILES['logo_s']['name']) && $_FILES['logo_s']['name'] !== '') {
            uploadImage($_FILES['logo_s'], ["png"], 'logo-G-100.png');
            $logosChanged = true;
        }

        if (isset($_FILES['logo_m']['name']) && $_FILES['logo_m']['name'] !== '') {
            uploadImage($_FILES['logo_m'], ["png"], 'logo-GLPI-100.png');
            $logosChanged = true;
        }

        if (isset($_FILES['logo_l']['name']) && $_FILES['logo_l']['name'] !== '') {
            uploadImage($_FILES['logo_l'], ["png"], 'logo-GLPI-250.png');
            $logosChanged = true;
        }

        if ($logosChanged && $logosOverwritten) {
            overwriteLogos();
        }

        if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] !== '') {
            uploadImage($_FILES['favicon'], ["ico"], 'favicon.ico');
            overwriteFavicon();
        }

        if (isset($_POST['show_background'])) {
            if ($_POST['show_background'] === '1') {
                if (!isLoginPageBackupPresent()) {
                    overwriteLoginPage();
                }
            } else {
                if (isLoginPageBackupPresent()) {
                    restoreLoginPage();
                }
            }
        }

        if (isset($_POST['overwrite_header'])) {
            overwriteHead(trim($_POST['overwrite_header']));
        }

        if (isset($_POST['show_custom_logos'])) {
            if ($_POST['show_custom_logos'] === '1') {
                if (!isLogoBackupPresent()) {
                    overwriteLogos();
                }
            } else {
                if (isLogoBackupPresent()) {
                    restoreLogoBackup();
                }
            }
        }

        if (isset($_POST['show_custom_favicon'])) {
            if ($_POST['show_custom_favicon'] === '1') {
                if (!isFaviconBackupPresent()) {
                    overwriteFavicon();
                }
            } else {
                if (isFaviconBackupPresent()) {
                    restoreFaviconBackup();
                }
            }
        }

        Html::back();
    } else {

        echo '<form action="./config.php" method="post" name="config_form" enctype="multipart/form-data">';
        echo "<table class='tab_cadre_fixe'>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Show custom background</td>";
        echo "<td width='20%'>";
        Dropdown::showYesNo("show_background", $loginOverwritten);
        echo "</td>";

        echo "<td>Customize header</td>";
        echo "<td width='20%'>";
        echo "<input type='text' id='overwrite_header' name='overwrite_header' value=''/>";
        echo Html::scriptBlock("$('#overwrite_header').val(document.title.replace('UI Modification - ', ''));");
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Show custom logos</td>";
        echo "<td width='20%'>";
        Dropdown::showYesNo("show_custom_logos", $logosOverwritten);
        echo "</td>";

        echo "<td>Show custom favicon</td>";
        echo "<td width='20%'>";
        Dropdown::showYesNo("show_custom_favicon", $faviconOverwritten);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<th colspan=4 >Upload custom images</th>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Background</td>";
        echo "<td width='20%' style='width: 20%'><input type='file' accept='image/jpeg' name='background' /></td>";
        echo "<td colspan='2' style='text-align: center'>";
        echo "<img style='max-width: 100%; max-height: 250px' src='../resources/background.jpg' alt='Background' />";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Small Logo (53x53 - PNG)</td>";
        echo "<td width='20%'><input type='file' accept='image/png' name='logo_s' /></td>";
        echo "<td colspan='2' style='text-align: center'>";
        echo "<img style='max-width: 100%;' src='../resources/logo-G-100.png' alt='Logo' />";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Medium Logo (100x55 - PNG)</td>";
        echo "<td width='20%'><input type='file' accept='image/png' name='logo_m' /></td>";
        echo "<td colspan='2' style='text-align: center'>";
        echo "<img style='max-width: 100%;' src='../resources/logo-GLPI-100.png' alt='Logo' />";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Large Logo (250x138 - PNG)</td>";
        echo "<td width='20%'><input type='file' accept='image/png' name='logo_l' /></td>";
        echo "<td colspan='2' style='text-align: center'>";
        echo "<img style='max-width: 100%;' src='../resources/logo-GLPI-250.png' alt='Logo' />";
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Favicon (16x16 - ICO)</td>";
        echo "<td width='20%'><input type='file' accept='image/x-icon' name='favicon' /></td>";
        echo "<td colspan='2' style='text-align: center'>";
        echo "<img style='max-width: 100%;' src='../resources/favicon.ico' alt='Favicon' />";
        echo "</td>";
        echo "</tr>";

        echo "</table>";

        echo '<div class="card-body mx-n2 mb-4 border-top d-flex flex-row-reverse align-items-start flex-wrap">';
        echo '<button class="btn btn-primary me-2" type="submit" name="update" value="1">';
        echo '<i class="far fa-save"></i>';
        echo '<span>Save</span>';
        echo '</button>';
        echo '</div>';

        Html::closeForm();
    }
}
?>
