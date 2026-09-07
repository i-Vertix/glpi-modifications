<?php

namespace GlpiPlugin\Mod\Controller;

use Glpi\Cache\CacheManager;
use Glpi\Controller\AbstractController;
use GlpiPlugin\Mod\UIBranding;
use Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class UIBrandingController extends AbstractController
{
    #[Route('/UIBranding', name: 'mod_uibranding', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        if (!Session::haveRight('config', UPDATE)) {
            throw new AccessDeniedHttpException();
        }


        $uiBranding = new UIBranding();

        if ($request->isMethod('POST')) {
            global $CFG_GLPI;
            $redirectUrl = rtrim((string) ($CFG_GLPI['root_doc'] ?? ''), '/') . '/plugins/mod/UIBranding';

            if ($request->request->has('clear_cache')) {
                $cacheManager = new CacheManager();
                if ($cacheManager->resetAllCaches()) {
                    Session::addMessageAfterRedirect(
                        __('GLPI cache cleared successfully.', 'mod')
                    );
                } else {
                    Session::addMessageAfterRedirect(
                        __('Unable to clear the GLPI cache completely.', 'mod'),
                        true,
                        WARNING
                    );
                }

                return new RedirectResponse($redirectUrl);
            }

            $uiBranding->save(
                $request->request->all(),
                $request->files->all(),
            );

            Session::addMessageAfterRedirect(
                __('UI Branding settings saved successfully. It is recommended to clear the GLPI cache to apply all changes.', 'mod')
            );

            return new RedirectResponse($redirectUrl);
        }

        global $CFG_GLPI;
        $rootDoc = rtrim((string) ($CFG_GLPI['root_doc'] ?? ''), '/');

        return $this->render('@mod/uibranding.html.twig', $uiBranding->getViewData(
            $rootDoc . '/plugins/mod/UIBranding',
            $rootDoc . '/plugins/mod/UIBranding/Resource',
        ));
    }
}
