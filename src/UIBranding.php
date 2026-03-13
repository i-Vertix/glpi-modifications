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

        // Theme specific logo variants (black/grey/white)
        $useThemeLogos = isset($data['use_theme_logos']) && $data['use_theme_logos'] === '1';
        $brandManager->setThemeLogosEnabled($useThemeLogos);

        $themeLogoResources = [
            'logo_s_black',
            'logo_s_grey',
            'logo_s_white',
            'logo_m_black',
            'logo_m_grey',
            'logo_m_white',
            'logo_l_black',
            'logo_l_grey',
            'logo_l_white',
        ];

        if (isset($files['background']['name']) && $files['background']['name'] !== '') {
            $backgroundChanged = $brandManager->uploadResource("background", $files['background']);
        }
        if (!$useThemeLogos) {
            if (isset($files['logo_s']['name']) && $files['logo_s']['name'] !== '') {
                $logosChanged = $brandManager->uploadResource("logo_s", $files['logo_s']);
            }
            if (isset($files['logo_m']['name']) && $files['logo_m']['name'] !== '') {
                $logosChanged = $brandManager->uploadResource("logo_m", $files['logo_m']);
            }
            if (isset($files['logo_l']['name']) && $files['logo_l']['name'] !== '') {
                $logosChanged = $brandManager->uploadResource("logo_l", $files['logo_l']);
            }
        }

        foreach ($themeLogoResources as $resourceName) {
            if (isset($files[$resourceName]['name']) && $files[$resourceName]['name'] !== '') {
                $logosChanged = $brandManager->uploadResource($resourceName, $files[$resourceName]) || $logosChanged;
            }
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
                // overwrite logos if changed or if custom logos are not yet applied
                $needsApply = $logosChanged;
                $resourcesToCheck = ["logo_s", "logo_m", "logo_l"];
                if ($useThemeLogos) {
                    $resourcesToCheck = array_merge($resourcesToCheck, $themeLogoResources);
                }
                foreach ($resourcesToCheck as $resource) {
                    if (!$brandManager::isActiveResourceModified($resource)) {
                        $needsApply = true;
                        break;
                    }
                }

                if ($needsApply) {
                    if (!$useThemeLogos) {
                    // apply base logos
                    $brandManager->applyResource("logo_s");
                    $brandManager->applyResource("logo_m");
                    $brandManager->applyResource("logo_l");
                } else {
                    // apply only theme variants
                    foreach ($themeLogoResources as $resourceName) {
                        $brandManager->applyResource($resourceName);
                    }
                }
                }
            } else if (BrandManager::isAnyLogoModified(true)) {
                $brandManager->restoreResource("logo_s");
                $brandManager->restoreResource("logo_m");
                $brandManager->restoreResource("logo_l");
                foreach ($themeLogoResources as $resourceName) {
                    $brandManager->restoreResource($resourceName);
                }
            }
        } else if ($logosChanged) {
            if (!$useThemeLogos) {
                $brandManager->applyResource("logo_s");
                $brandManager->applyResource("logo_m");
                $brandManager->applyResource("logo_l");
            } else {
                foreach ($themeLogoResources as $resourceName) {
                    $brandManager->applyResource($resourceName);
                }
            }
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
        global $CFG_GLPI;
        TemplateRenderer::getInstance()->display('@mod/uibranding.html.twig', [
            "url" => $CFG_GLPI['root_doc'] . "/plugins/mod/front/uibranding.php",
            "preview_url" => $CFG_GLPI['root_doc'] . "/plugins/mod/front/resource.send.php",
            "show_background" => BrandManager::isLoginPageModified(),
            "show_custom_logos" => BrandManager::isAnyLogoModified(true),
            "use_theme_logos" => BrandManager::isThemeLogosEnabled(),
            "show_custom_favicon" => BrandManager::isActiveResourceModified("favicon"),
            "title" => BrandManager::getCurrentTitle(),
        ]);
        return true;
    }

}
