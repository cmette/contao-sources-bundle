<?php

declare(strict_types=1);

/*
 * Contao Pedigree Bundle for Contao Open Source CMS
 *
 * Copyright (c) 2023 C. Mette
 *
 * @package    contao-pedigree-bundle
 * @link       https://github.com/cmette/contao-pedigree-bundle
 * @license    LGPL-3.0-or-later
 * @author     Christian Mette
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cmette\ContaoSourcesBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

class SourcesAuthorListener
{
    private const STR_TABLE = 'tl_sources_author';

    public function __construct()
    {
    }

    #[AsCallback(table: self::STR_TABLE, target: 'list.label.group')]
    public function ListLabelGroupCallback(string $group, string|null $mode, string $field, array $row, DataContainer $dc): string
    {
        return $row['family_name'];
    }

    /**
     * @param array  $row   record data
     * @param string $label current label
     *
     * @return array
     */
    #[AsCallback(table: self::STR_TABLE, target: 'list.label.label')]
    public function ListLabelLabelCallback(array $row, string $label, DataContainer $dc, array $labels): array|string
    {
        return $row['first_name'].($row['isPublisher'] ? ' [Hrsg.]' : '');
    }
}
