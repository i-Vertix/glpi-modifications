<?php

namespace GlpiPlugin\Mod\Controller;

use Glpi\Controller\AbstractController;
use GlpiPlugin\Mod\BrandManager;
use Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ResourceController extends AbstractController
{
    #[Route('/UIBranding/Resource', name: 'mod_resource', methods: ['GET'])]
    public function __invoke(Request $request): BinaryFileResponse
    {
        if (!Session::haveRight('config', UPDATE)) {
            throw new AccessDeniedHttpException();
        }


        $key = $request->query->getString('resource');
        if (!BrandManager::isValidResourceKey($key)) {
            throw new AccessDeniedHttpException();
        }

        $file = BrandManager::getCurrentResourceFile($key);
        if ($file === null || !is_file($file) || !is_readable($file)) {
            throw new AccessDeniedHttpException();
        }

        $response = new BinaryFileResponse($file);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            basename($file),
        );

        return $response;
    }
}
