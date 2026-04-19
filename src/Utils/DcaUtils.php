<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle\Utils;

use Contao\DataContainer;

class DcaUtils
{
    public static function buildPublishedField(): array
    {
        return [
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType' => 'checkbox',
            'toggle'    => true,
            'eval' => [
                'doNotCopy' => true,
            ],
            'sql' => [
                'type' => 'boolean',
                'default' => false,
            ],
        ];
    }

    public static function buildAddField(): array
    {
        return [
            'inputType' => 'checkbox',
            'search'    => false,
            'filter'    => true,
            'sorting'   => true,
            'eval' => [
                'submitOnChange' => true,
            ],
            'sql' => [
                'type' => 'boolean',
                'default' => false,
            ],
        ];
    }
}
