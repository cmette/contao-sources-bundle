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

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\System;
use Twig\Environment;

class SourcesEntityListener
{
    private const STR_TABLE = 'tl_sources_entity';

    private const STR_BACKEND = '@Contao_ContaoSourcesBundle/backend';

    public  Environment $twig;

    public function __construct()
    {
        $this->twig = System::getContainer()->get('twig');
    }

    #[AsCallback(table: self::STR_TABLE, target: 'list.label.group')]
    public function ListLabelGroupCallback(string $group, string|null $mode, string $field, array $row, DataContainer $dc): string
    {
        // references the plural
        $result = $GLOBALS['TL_LANG'][self::STR_TABLE]['type_options'][$row['type']][1];
        #dump($group, $mode, $field, $row, $dc);

        return $result;
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
        $sourceEntity = SourcesEntityModel::findById($row['id']);

        if($sourceEntity) {
            $sourceEntity->_initiale = ucfirst($GLOBALS['TL_LANG'][self::STR_TABLE]['type_options'][$row['type']][0][0]);
            $html = $this->twig->render(self::STR_BACKEND.'/sources_entity_list_label.html.twig', ['row' => $sourceEntity,]);
        }

        return $html;
    }

    #[AsCallback(table: self::STR_TABLE, target: 'fields.authors.options')]
    public function typeIdOptions(DataContainer $dc): array
    {
        $options = [];

        $authors = SourcesAuthorModel::findAll();

        if ($authors) {
            foreach ($authors as $author) {
                /** @var SourcesAuthorModel $author */
                $options[$author->id] = "$author->family_name, $author->first_name".($author->isPublisher ? ' [Hrsg.]' : '');
            }
        }

        asort($options);

        return $options;
    }
}
