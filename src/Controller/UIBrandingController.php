<?php

namespace GlpiPlugin\Mod\Controller;

use Glpi\Cache\CacheManager;
use Glpi\Controller\AbstractController;
use GlpiPlugin\Mod\Paths;
use GlpiPlugin\Mod\UIBranding;
use Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class UIBrandingController extends AbstractController
{
    #[Route('/UIBranding', name: 'mod_uibranding', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        if (!Session::haveRight('config', UPDATE)) {
            throw new AccessDeniedHttpException();
        }

        $baseUrl = Paths::getWebPrefix();

        if ($request->isMethod('POST')) {
            if ($request->request->has('clear_cache')) {
                $cacheManager = new CacheManager();
                if ($cacheManager->resetAllCaches()) {
                    Session::addMessageAfterRedirect(__('GLPI cache cleared successfully.', 'mod'));
                } else {
                    Session::addMessageAfterRedirect(
                        __('Unable to clear the GLPI cache completely.', 'mod'),
                        true,
                        WARNING,
                    );
                }

                return new RedirectResponse($baseUrl . '/UIBranding');
            }

            try {
                (new UIBranding())->save(
                    $request->request->all(),
                    $request->files->all(),
                );

                Session::addMessageAfterRedirect(
                    __('UI Branding settings saved successfully. It is recommended to clear both the GLPI cache and your browser cache to apply all changes.', 'mod')
                );
            } catch (Throwable $e) {
                global $GLPI;
                if (isset($GLPI) && method_exists($GLPI, 'getErrorHandler')) {
                    $GLPI->getErrorHandler()->handleException($e, false);
                }

                Session::addMessageAfterRedirect(
                    __('Unable to save UI Branding settings. Check the GLPI logs for more information.', 'mod'),
                    true,
                    ERROR,
                );
            }

            return new RedirectResponse($baseUrl . '/UIBranding');
        }

        $branding = new UIBranding();

        return $this->render('@mod/uibranding.html.twig', $branding->getViewData(
                $baseUrl . '/UIBranding',
                $baseUrl . '/UIBranding/Asset',
            ));
    }


}
