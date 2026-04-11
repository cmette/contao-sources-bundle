<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle\Models;

use Contao\System;
use Symfony\Component\HttpFoundation\Request;

trait RequestCheckerTrait
{
    public function isFrontendRequest(): bool
    {
        $currentRequest = System::getContainer()->get('request_stack')->getCurrentRequest();
        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');

        return $scopeMatcher->isFrontendRequest($currentRequest ?? Request::create(''));
    }

    public function isBackendRequest(): bool
    {
        $currentRequest = System::getContainer()->get('request_stack')->getCurrentRequest();
        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');

        return $scopeMatcher->isBackendRequest($currentRequest ?? Request::create(''));
    }
}
