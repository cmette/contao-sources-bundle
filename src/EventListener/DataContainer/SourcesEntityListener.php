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
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\FrontendTemplate;
use Contao\Image;
use Contao\StringUtil;
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
        $source = SourcesEntityModel::findById($row['id']);

        if($source) {
            $source->_initiale    = ucfirst($GLOBALS['TL_LANG'][self::STR_TABLE]['type_options'][$row['type']][0][0]);
            $source->_imageHtml   = self::buildImage($source->singleSRC)->image;
            $html = $this->twig->render(
                self::STR_BACKEND.'/sources_entity_list_label.html.twig',
                ['source' => $source,]
            );
        }

        return $html;
    }

    #[AsCallback(table: self::STR_TABLE, target: 'fields.authors1.options')]
    public function typeIdOptions(DataContainer $dc): array
    {
        $options = [];

        $authors = SourcesAuthorModel::findAll();

        if ($authors !== null) foreach ($authors as $author) $options[$author->id] = $author->getUniqueAuthor(false);

        #asort($options);

        return $options;
    }

    #[AsCallback(table: self::STR_TABLE, target: 'fields.singleSRC.load')]
    public function singleSRCOnLoad(mixed $currentValue, DataContainer $dc): mixed
    {
        if ($dc->activeRecord)
        {
            switch ($dc->activeRecord->type)
            {
                case 'text':
                case 'hyperlink':
                case 'image':
                case 'accordionSingle':
                case 'youtube':
                case 'vimeo':
                    $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['extensions'] = '%contao.image.valid_extensions%';
                    break;

                case 'download':
                    $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['extensions'] = Config::get('allowedDownload');
                    break;

                case 'markdown':
                    $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['extensions'] = 'md';
                    break;
            }
        }
        return $currentValue;
    }

    public static function buildImage(?string $singleSrc, array $size = [160, 160, 'proportional']): \stdClass
    {
        $objResult = new \stdClass();

        if ($singleSrc) {
            // get a figure builder instance
            $figureBuilder  = System::getContainer()->get('contao.image.studio')->createFigureBuilder();

            try {
                $figure = $figureBuilder->fromUUID($singleSrc)->setSize($size)->enableLightbox()->build();
                // attention: this is our own template, see: contao/templates/backend/ResumableWidget/image.html5
                $template = new FrontendTemplate('image');
                // get the template data and change it
                $templateData = $figure->getLegacyTemplateData();
                // we add an image ID and use the uuid for it, this is necessary for the magnifier glass javascript
                $templateData['picture']['img']['id'] = $templateData['uuid'];
                // set the data
                $template->setData($templateData);
                // parse template
                $imageHtml = $template->parse();
            } catch (\Exception $e) {
                $imageHtml = $e->getMessage();
                $figure    = null;
            }
        } else {
            $imageHtml = 'kein Scan';
            $figure    = null;
        }

        $objResult->image  = $imageHtml;
        $objResult->figure = $figure;

        return $objResult;
    }

}
