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

$key = $_GET['resource'] ?? '';
if ($key === "" || !isset(BrandManager::IMAGE_RESOURCES[$key])) {
    http_response_code(404);
    exit("Unknown resource");
}

$file = BrandManager::IMAGE_RESOURCES[$key]["current"];
if (!file_exists($file)) {
    http_response_code(404);
    exit("Image not found");
}

$mime = false;
if (function_exists('mime_content_type')) {
    $mime = @mime_content_type($file);
}
if ($mime === false) {
    // Fallback
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimeMap = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'ico'  => 'image/x-icon',
    ];
    $mime = $mimeMap[$ext] ?? 'application/octet-stream';
}
header('Content-Type: ' . $mime);
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($file);
exit;
