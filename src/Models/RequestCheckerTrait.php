<?php

namespace Cmette\ContaoSourcesBundle\Models;

use Contao\System;
use Symfony\Component\HttpFoundation\Request;

trait RequestCheckerTrait
{
    public function isFrontendRequest(): bool
    {
        $currentRequest = System::getContainer()->get('request_stack')->getCurrentRequest();
        $scopeMatcher   = System::getContainer()->get('contao.routing.scope_matcher');

        return $scopeMatcher->isFrontendRequest($currentRequest ?? Request::create(''));
    }

    public function isBackendRequest(): bool
    {
        $currentRequest = System::getContainer()->get('request_stack')->getCurrentRequest();
        $scopeMatcher   = System::getContainer()->get('contao.routing.scope_matcher');

        return $scopeMatcher->isBackendRequest($currentRequest ?? Request::create(''));
    }
}