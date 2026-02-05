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

use Glpi\Exception\Http\AccessDeniedHttpException;
use GlpiPlugin\Mod\BrandManager;
use function Safe\readfile;

if (!Plugin::isPluginActive("mod")) {
    throw new AccessDeniedHttpException();
}

Session::checkRight("config", UPDATE);

$key = $_GET['resource'] ?? '';
$imageResources = BrandManager::getImageResources();
if ($key === "" || !isset($imageResources[$key]["current"]) || !file_exists($imageResources[$key]["current"])) {
    throw new AccessDeniedHttpException();
}

$file = $imageResources[$key]["current"];

header('Content-Type: ' . Toolbox::getMime($file));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: Sun, 30 Jan 1966 06:30:00 GMT');
header('Content-disposition: filename="' . basename($file) . '"');

readfile($file);
