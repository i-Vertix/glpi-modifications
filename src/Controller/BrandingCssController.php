<?php

/**
 * UI Branding plugin for GLPI 12.
 *
 * @copyright Copyright (C) 2026 by i-Vertix/PGUM.
 * @license GPLv3
 */

namespace GlpiPlugin\Mod\Controller;

use Glpi\Controller\AbstractController;
use Glpi\Http\Firewall;
use Glpi\Security\Attribute\SecurityStrategy;
use GlpiPlugin\Mod\BrandManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BrandingCssController extends AbstractController
{
    #[Route('/UIBranding/BrandingCss', name: 'mod_branding_css', methods: ['GET'])]
    #[SecurityStrategy(Firewall::STRATEGY_NO_CHECK)]
    public function __invoke(Request $request): Response
    {
        $config = BrandManager::getConfiguration();
        $basePath = rtrim(dirname($request->getPathInfo()), '/');
        $assetBase = $basePath . '/Asset/';

        $assetUrl = static function (string $name) use ($assetBase): string {
            return $assetBase . rawurlencode($name) . '?v='
                . rawurlencode(BrandManager::getResourceFingerprint($name));
        };

        $rules = [];

        if ($config['show_logos'] === '1') {
            if ($config['theme_logos'] === '1') {
                $lightMedium = $assetUrl('logo_m_white');
                $darkMedium = $assetUrl('logo_m_black');
                $lightSmall = $assetUrl('logo_s_white');
                $darkSmall = $assetUrl('logo_s_black');
                $lightLarge = $assetUrl('logo_l_white');
                $darkLarge = $assetUrl('logo_l_black');

                // The plugin stylesheet is injected before GLPI core CSS, so use
                // !important on the runtime branding rules. This keeps the custom
                // assets effective without changing GLPI core files, while still
                // respecting the native collapsed sidebar state.
                $rules[] = '.page .glpi-logo{background-image:url("' . $lightMedium . '") !important;background-repeat:no-repeat !important;background-size:contain !important;}';
                $rules[] = '[data-glpi-theme-dark="1"] .page .glpi-logo{background-image:url("' . $darkMedium . '") !important;}';
                $rules[] = 'body.navbar-collapsed .navbar-vertical .glpi-logo,body.navbar-collapsed .global-menu .glpi-logo{background-image:url("' . $lightSmall . '") !important;background-repeat:no-repeat !important;background-size:contain !important;}';
                $rules[] = '[data-glpi-theme-dark="1"] body.navbar-collapsed .navbar-vertical .glpi-logo,[data-glpi-theme-dark="1"] body.navbar-collapsed .global-menu .glpi-logo{background-image:url("' . $darkSmall . '") !important;}';
                $rules[] = '.page-anonymous .glpi-logo{content:url("' . $lightLarge . '") !important;}';
                $rules[] = '[data-glpi-theme-dark="1"] .page-anonymous .glpi-logo{content:url("' . $darkLarge . '") !important;}';
            } else {
                $medium = $assetUrl('logo_m');
                $small = $assetUrl('logo_s');
                $large = $assetUrl('logo_l');

                $rules[] = '.page .glpi-logo{background-image:url("' . $medium . '") !important;background-repeat:no-repeat !important;background-size:contain !important;}';
                $rules[] = 'body.navbar-collapsed .navbar-vertical .glpi-logo,body.navbar-collapsed .global-menu .glpi-logo{background-image:url("' . $small . '") !important;background-repeat:no-repeat !important;background-size:contain !important;}';
                $rules[] = '.page-anonymous .glpi-logo{content:url("' . $large . '") !important;}';
            }
        }

        if ($config['login'] === '1') {
            $background = $assetUrl('background');
            $rules[] = 'html{min-height:100%;}';
            $rules[] = 'body.welcome-anonymous{min-height:100vh !important;margin:0 !important;background-color:transparent !important;background-image:url("' . $background . '") !important;background-repeat:no-repeat !important;background-attachment:fixed !important;background-position:center center !important;background-size:cover !important;}';
            $rules[] = 'body.welcome-anonymous .page-anonymous{min-height:100vh !important;background:transparent !important;}';
        }

        $content = implode("\n", $rules) . "\n";
        $etag = '"' . hash('sha256', $content) . '"';
        $response = new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => $etag,
        ]);

        return $response;
    }

}
