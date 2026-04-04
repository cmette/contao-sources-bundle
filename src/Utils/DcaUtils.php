<?php

namespace Cmette\ContaoSourcesBundle\Utils;

use Contao\DataContainer;

class DcaUtils
{
    public static function buildPublishedField(): array
    {
        return [
            'flag'      => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType' => 'checkbox',
            'eval'      => [
                'doNotCopy' => true
            ],
            'sql'   => [
                'type' => 'boolean',
                'default' => false
            ],
        ];
    }

    public static function bildAddField(): array
    {
        return [
            'inputType' => 'checkbox',
            'eval'      => [
                'submitOnChange' => true
            ],
            'sql'       => [
                'type' => 'boolean',
                'default' => false
            ]
        ];
    }
}