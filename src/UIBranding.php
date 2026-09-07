<?php

/**
 * UI Branding plugin for GLPI 12
 * Copyright (C) 2026 by i-Vertix/PGUM.
 * License GPLv3
 */

namespace GlpiPlugin\Mod;

use Glpi\Application\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class UIBranding
{
    private const THEME_LOGO_RESOURCES = [
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

    private const BASE_LOGO_RESOURCES = [
        'logo_s',
        'logo_m',
        'logo_l',
    ];

    public function save(array $data, array $files): void
    {
        $manager = new BrandManager();

        $previousThemeLogos = BrandManager::isThemeLogosEnabled();
        $useThemeLogos = isset($data['use_theme_logos'])
            ? (string) $data['use_theme_logos'] === '1'
            : $previousThemeLogos;

        $backgroundChanged = false;
        $faviconChanged = false;
        $logosChanged = $previousThemeLogos !== $useThemeLogos;

        if ($this->hasUpload($files, 'background')) {
            $backgroundChanged = $manager->uploadResource('background', $files['background']);
        }

        if ($useThemeLogos) {
            foreach (self::THEME_LOGO_RESOURCES as $resource) {
                if ($this->hasUpload($files, $resource)) {
                    $logosChanged = $manager->uploadResource($resource, $files[$resource]) || $logosChanged;
                }
            }
        } else {
            foreach (self::BASE_LOGO_RESOURCES as $resource) {
                if ($this->hasUpload($files, $resource)) {
                    $logosChanged = $manager->uploadResource($resource, $files[$resource]) || $logosChanged;
                }
            }
        }

        if ($this->hasUpload($files, 'favicon')) {
            $faviconChanged = $manager->uploadResource('favicon', $files['favicon']);
        }

        $showBackground = isset($data['show_background']) && (string) $data['show_background'] === '1';
        if ($showBackground) {
            if ($backgroundChanged || !BrandManager::isLoginPageModified()) {
                $manager->applyResource('background');
            }
            $manager->applyLoginPageModifier();
        } elseif (BrandManager::isLoginPageModified()) {
            $manager->restoreResource('background');
            $manager->disableLoginPageModifier();
        }

        $showLogos = isset($data['show_custom_logos']) && (string) $data['show_custom_logos'] === '1';

        if (!$showLogos) {
            if (BrandManager::isAnyLogoModified()) {
                foreach (array_merge(self::BASE_LOGO_RESOURCES, self::THEME_LOGO_RESOURCES) as $resource) {
                    $manager->restoreResource($resource);
                }
            }
        } else {
            $manager->setThemeLogosEnabled($useThemeLogos);

            if ($useThemeLogos) {
                $needsApply = $logosChanged;
                if (!$needsApply) {
                    $needsApply = !$this->anyResourceModified(self::THEME_LOGO_RESOURCES);
                }

                if ($needsApply) {
                    foreach (self::THEME_LOGO_RESOURCES as $resource) {
                        $manager->applyResource($resource);
                    }
                }
            } else {
                $needsApply = $logosChanged;
                if (!$needsApply) {
                    $needsApply = !$this->anyResourceModified(self::BASE_LOGO_RESOURCES);
                }

                if ($needsApply) {
                    foreach (self::BASE_LOGO_RESOURCES as $resource) {
                        $manager->applyResource($resource);
                    }
                }
            }
        }

        $manager->setThemeLogosEnabled($useThemeLogos);

        $showFavicon = isset($data['show_custom_favicon']) && (string) $data['show_custom_favicon'] === '1';
        if ($showFavicon) {
            if ($faviconChanged || !BrandManager::isActiveResourceModified('favicon')) {
                $manager->applyResource('favicon');
            }
        } elseif (BrandManager::isActiveResourceModified('favicon')) {
            $manager->restoreResource('favicon');
        }

        if (array_key_exists('title', $data)) {
            $manager->changeTitle((string) $data['title']);
        }
    }

    private function hasUpload(array $files, string $name): bool
    {
        if (!isset($files[$name])) {
            return false;
        }

        $file = $files[$name];
        if ($file instanceof UploadedFile) {
            return $file->getError() !== UPLOAD_ERR_NO_FILE;
        }

        return isset($file['name'], $file['tmp_name']) && (string) $file['name'] !== '';
    }

    private function anyResourceModified(array $resources): bool
    {
        foreach ($resources as $resource) {
            if (BrandManager::isActiveResourceModified($resource)) {
                return true;
            }
        }
        return false;
    }

    public function getViewData(string $url, string $previewUrl): array
    {
        return [
            'url'                 => $url,
            'preview_url'         => $previewUrl,
            'show_background'     => BrandManager::isLoginPageModified(),
            'show_custom_logos'   => BrandManager::isAnyLogoModified(),
            'use_theme_logos'     => BrandManager::isThemeLogosEnabled(),
            'show_custom_favicon' => BrandManager::isActiveResourceModified('favicon'),
            'page_title'          => BrandManager::getCurrentTitle(),
            'base_logos'          => [
                ['name' => 'logo_s', 'label' => __('Small Logo', 'mod'), 'size' => '53x53'],
                ['name' => 'logo_m', 'label' => __('Medium Logo', 'mod'), 'size' => '100x55'],
                ['name' => 'logo_l', 'label' => __('Large Logo', 'mod'), 'size' => '250x138'],
            ],
            'theme_logos'         => [
                ['name' => 'logo_s_black', 'label' => __('Small Logo (black)', 'mod'), 'size' => '53x53'],
                ['name' => 'logo_s_grey', 'label' => __('Small Logo (grey)', 'mod'), 'size' => '53x53'],
                ['name' => 'logo_s_white', 'label' => __('Small Logo (white)', 'mod'), 'size' => '53x53'],
                ['name' => 'logo_m_black', 'label' => __('Medium Logo (black)', 'mod'), 'size' => '100x55'],
                ['name' => 'logo_m_grey', 'label' => __('Medium Logo (grey)', 'mod'), 'size' => '100x55'],
                ['name' => 'logo_m_white', 'label' => __('Medium Logo (white)', 'mod'), 'size' => '100x55'],
                ['name' => 'logo_l_black', 'label' => __('Large Logo (black)', 'mod'), 'size' => '250x138'],
                ['name' => 'logo_l_grey', 'label' => __('Large Logo (grey)', 'mod'), 'size' => '250x138'],
                ['name' => 'logo_l_white', 'label' => __('Large Logo (white)', 'mod'), 'size' => '250x138'],
            ],
        ];
    }

}
