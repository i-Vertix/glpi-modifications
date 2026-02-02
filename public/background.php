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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

$file = BrandManager::getImageResources()["background"]["current"];

if (!file_exists($file)) {
    return new Response('Image not found', 404);
}

// remove existing conflicting headers
if (!headers_sent()) {
    header_remove('Pragma');
    header_remove('Expires');
    header_remove('Cache-Control');
}

$response = new BinaryFileResponse($file);
$response->setPublic();
$response->setMaxAge(31536000);
$response->setSharedMaxAge(31536000);
$response->mustRevalidate();
$response->setEtag(md5_file($file));
$response->setAutoLastModified();
$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
$response->isNotModified(Request::createFromGlobals());
return $response;
