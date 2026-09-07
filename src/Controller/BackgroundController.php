<?php

namespace GlpiPlugin\Mod\Controller;

use Glpi\Controller\AbstractController;
use Glpi\Http\Firewall;
use Glpi\Security\Attribute\SecurityStrategy;
use GlpiPlugin\Mod\BrandManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class BackgroundController extends AbstractController
{
    #[Route('/UIBranding/Background', name: 'mod_background', methods: ['GET'])]
    #[SecurityStrategy(Firewall::STRATEGY_NO_CHECK)]
    public function __invoke(): BinaryFileResponse
    {
        $file = BrandManager::getCurrentResourceFile('background');
        if ($file === null || !is_file($file) || !is_readable($file)) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($file);
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setSharedMaxAge(31536000);
        $response->mustRevalidate();
        $response->setEtag((string) md5_file($file));
        $response->setAutoLastModified();
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }
}
