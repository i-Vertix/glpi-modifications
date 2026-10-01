<?php

namespace GlpiPlugin\Mod;

use Glpi\Plugin\Hooks;
use Glpi\UI\ThemeManager;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

final class BrandingHooks
{
    public static function init(): void
    {
        global $PLUGIN_HOOKS, $CFG_GLPI;

        // The branding stylesheet is served dynamically by a public Controller.
        // The URL is content-versioned so browsers can keep the stylesheet in
        // cache across normal GLPI navigation and fetch it again only when the
        // branding configuration, resources, or plugin version changes.
        $cssUrl = Paths::getBrandingCssUrl(BrandManager::getBrandingCssFingerprint());

        $headerTags = [];

        // Preload only the logo assets that can be used by the current page.
        // Place these hints before the stylesheet so the browser can start
        // fetching the image as early as possible.
        if (BrandManager::isAnyLogoModified()) {
            $headerTags = self::getLogoPreloadTags();
        }

        $headerTags[] = [
            'tag' => 'link',
            'properties' => [
                'rel'  => 'stylesheet',
                'type' => 'text/css',
                'href' => $cssUrl,
            ],
        ];

        if (BrandManager::isFaviconEnabled()) {
            $faviconUrl = Paths::getAssetUrl(
                'favicon',
                BrandManager::getResourceFingerprint('favicon'),
            );
            $headerTags[] = [
                'tag' => 'link',
                'properties' => [
                    'rel'      => 'icon',
                    'type'     => 'image/x-icon',
                    'sizes'    => '16x16',
                    'href'     => $faviconUrl,
                    'data-mod-favicon' => '1',
                ],
            ];
        }

        $PLUGIN_HOOKS[Hooks::ADD_HEADER_TAG]['mod'] = $headerTags;
        $PLUGIN_HOOKS[Hooks::ADD_HEADER_TAG_ANONYMOUS_PAGE]['mod'] = $headerTags;

        // Live previews are required on the configuration page regardless of
        // whether the custom favicon feature is enabled.
        $javascript = ['uibranding.js'];
        if (BrandManager::isFaviconEnabled()) {
            $javascript[] = 'favicon.js';
        }
        $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['mod'] = $javascript;
        $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT_ANONYMOUS_PAGE]['mod'] = $javascript;

        $title = BrandManager::getCurrentTitle();
        if ($title !== '') {
            $CFG_GLPI['app_name'] = $title;
        }
    }


    /**
     * Preload only the branding logos relevant to the current interface state.
     *
     * @return array<int, array{tag: string, properties: array<string, string>}>
     */
    private static function getLogoPreloadTags(): array
    {
        $themeLogos = BrandManager::isThemeLogosEnabled();
        $isAnonymous = !isset($_SESSION['glpiactiveprofile']);
        $themeIsDark = false;

        if ($themeLogos) {
            $themeIsDark = ThemeManager::getInstance()->getCurrentTheme()->isDarkTheme();
        }

        if ($isAnonymous) {
            $resourceName = $themeLogos
                ? ($themeIsDark ? 'logo_l_black' : 'logo_l_white')
                : 'logo_l';

            return [self::createPreloadTag($resourceName)];
        }

        $medium = $themeLogos
            ? ($themeIsDark ? 'logo_m_black' : 'logo_m_white')
            : 'logo_m';
        $small = $themeLogos
            ? ($themeIsDark ? 'logo_s_black' : 'logo_s_white')
            : 'logo_s';

        return [
            self::createPreloadTag($medium),
            self::createPreloadTag($small),
        ];
    }

    /**
     * @return array{tag: string, properties: array<string, string>}
     */
    private static function createPreloadTag(string $resourceName): array
    {
        $url = Paths::getAssetUrl(
            $resourceName,
            BrandManager::getResourceFingerprint($resourceName),
        );

        return [
            'tag' => 'link',
            'properties' => [
                'rel'  => 'preload',
                'as'   => 'image',
                'href' => $url,
            ],
        ];
    }


}
