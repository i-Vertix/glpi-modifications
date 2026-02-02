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

namespace GlpiPlugin\Mod;

use Glpi\Application\View\TemplateRenderer;
use Plugin;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class UIBranding
{

    /**
     * @param array $data
     * @param array $files
     * @return void
     */
    public function save(array $data, array $files): void
    {
        $brandManager = new BrandManager();
        $backgroundChanged = false;
        $logosChanged = false;
        $faviconChanged = false;

        if (isset($files['background']['name']) && $files['background']['name'] !== '') {
            $backgroundChanged = $brandManager->uploadResource("background", $files['background']);
        }
        if (isset($files['logo_s']['name']) && $files['logo_s']['name'] !== '') {
            $logosChanged = $brandManager->uploadResource("logo_s", $files['logo_s']);
        }
        if (isset($files['logo_m']['name']) && $files['logo_m']['name'] !== '') {
            $logosChanged = $brandManager->uploadResource("logo_m", $files['logo_m']);
        }
        if (isset($files['logo_l']['name']) && $files['logo_l']['name'] !== '') {
            $logosChanged = $brandManager->uploadResource("logo_l", $files['logo_l']);
        }

        if (isset($files['favicon']['name']) && $files['favicon']['name'] !== '') {
            $faviconChanged = $brandManager->uploadResource("favicon", $files['favicon']);
        }

        if (isset($data['show_background'])) {
            if ($data['show_background'] === '1') {
                // overwrite background if changed or not overwritten yet
                if ($backgroundChanged || !$brandManager::isLoginPageModified()) {
                    $brandManager->applyResource("background");
                }
                $brandManager->applyLoginPageModifier();
            } else if ($brandManager::isLoginPageModified()) {
                $brandManager->restoreResource("background");
                $brandManager->disableLoginPageModifier();
            }
        } else if ($backgroundChanged && $brandManager::isLoginPageModified()) {
            $brandManager->applyResource("background");
        }

        if (isset($data['show_custom_logos'])) {
            if ($data['show_custom_logos'] === '1') {
                // overwrite background if changed or not overwritten yet
                if (
                    $logosChanged
                    || !$brandManager::isActiveResourceModified("logo_s")
                    || !$brandManager::isActiveResourceModified("logo_m")
                    || !$brandManager::isActiveResourceModified("logo_l")
                ) {
                    $brandManager->applyResource("logo_s");
                    $brandManager->applyResource("logo_m");
                    $brandManager->applyResource("logo_l");
                }
            } else if (
                $brandManager::isActiveResourceModified("logo_s")
                || $brandManager::isActiveResourceModified("logo_m")
                || $brandManager::isActiveResourceModified("logo_l")
            ) {
                $brandManager->restoreResource("logo_s");
                $brandManager->restoreResource("logo_m");
                $brandManager->restoreResource("logo_l");
            }
        } else if ($logosChanged) {
            $brandManager->applyResource("logo_s");
            $brandManager->applyResource("logo_m");
            $brandManager->applyResource("logo_l");
        }

        if (isset($data['show_custom_favicon'])) {
            if ($data['show_custom_favicon'] === '1') {
                // overwrite background if changed or not overwritten yet
                if ($faviconChanged || !$brandManager::isActiveResourceModified("favicon")) {
                    $brandManager->applyResource("favicon");
                }
            } else if ($brandManager::isActiveResourceModified("favicon")) {
                $brandManager->restoreResource("favicon");
            }
        } else if ($faviconChanged && $brandManager::isActiveResourceModified("favicon")) {
            $brandManager->applyResource("favicon");
        }

        if (isset($data['title'])) {
            $brandManager->changeTitle($data["title"]);
        }
    }

    /**
     * @return bool
     */
    public function display(): bool
    {
        TemplateRenderer::getInstance()->display('@mod/uibranding.html.twig', [
            "url" => Plugin::getPhpDir("mod", false) . "/front/uibranding.php",
            "preview_url" => Plugin::getPhpDir("mod", false) . "/front/resource.send.php",
            "show_background" => BrandManager::isLoginPageModified(),
            "show_custom_logos" => BrandManager::isActiveResourceModified("logo_s")
                || BrandManager::isActiveResourceModified("logo_m")
                || BrandManager::isActiveResourceModified("logo_l"),
            "show_custom_favicon" => BrandManager::isActiveResourceModified("favicon"),
            "title" => BrandManager::getCurrentTitle(),
        ]);
        return true;
    }

}