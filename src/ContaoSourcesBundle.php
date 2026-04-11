<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoSourcesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}