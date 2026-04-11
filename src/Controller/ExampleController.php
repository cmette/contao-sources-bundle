<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle\Controller;

use Contao\CoreBundle\Controller\AbstractBackendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route(
        '/%contao.backend.route_prefix%/example',
        name: ExampleController::class,
        defaults: [
            '_scope' => 'backend',
            '_token_check' => false,
        ],
    )
]
class ExampleController extends AbstractBackendController
{
    public string $templateRoot = '@Contao_CmetteContaoPedigreeBundle/backend';

    protected string $assetsDir = 'bundles/cmettecontaopedigree';

    // path to the widgets icons
    protected string $resImg;

    private Request $request;

    // holds all POST parameters
    private array $postParams = [];

    public function __construct()
    {
        // System::loadLanguageFile('tl_pedigree_image');
        // System::loadLanguageFile('default'); $this->resImg   =
        // Environment::get('base')."$this->assetsDir/img/resumable"; $this->request  =
        // System::getContainer()->get('request_stack')->getCurrentRequest();
        // parse_str($this->request->getContent(), $this->postParams);
    }

    #[Route('/scan', defaults: ['_scope' => 'backend'])]
    public function scan(): Response
    {
        return new Response('OK', Response::HTTP_OK);
    }
}
