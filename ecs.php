<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    // Adjust the configuration according to your needs.
    ->withPaths([
        __DIR__.'/src',
    ])
    ->withConfiguredRule(
        HeaderCommentFixer::class,
        [
            'header' => "This file is part of the Contao Sources Bundle.\n\n(c) Christian Mette\n\n@license LGPL-3.0-or-later"
        ]
    )
    ;