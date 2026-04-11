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
use Cmette\ContaoSourcesBundle\Models\SourcesSettingModel;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\DataContainer;
use Contao\FrontendTemplate;
use Twig\Environment as TwigEnvironment;

class SourcesEntityListener
{
    private const STR_TABLE = 'tl_sources_entity';

    private const STR_BACKEND = '@Contao_ContaoSourcesBundle/backend';

    public function __construct(
        private readonly Studio $imageStudio,
        private readonly TwigEnvironment $twig,
    ) {
    }

    #[AsCallback(table: self::STR_TABLE, target: 'list.label.group')]
    public function ListLabelGroupCallback(string $group, string|null $mode, string $field, array $row, DataContainer $dc): string
    {
        // references the plural
        return $GLOBALS['TL_LANG'][self::STR_TABLE]['type_options'][$row['type']][1];
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
        $source     = SourcesEntityModel::findById($row['id']);
        $settings   = SourcesSettingModel::findOneBy("published = '1'", [1]);

        if ($source) {
            $source->_initiale = ucfirst($GLOBALS['TL_LANG'][self::STR_TABLE]['type_options'][$row['type']][0][0]);
            $source->_imageHtml = $this->buildImage($source->singleSRC)->image;
            $html = $this->twig->render(
                self::STR_BACKEND.'/sources_entity_list_label.html.twig',
                ['source' => $source, 'settings' => $settings],
            );
        }

        return $html;
    }

    // #[AsCallback(table: self::STR_TABLE, target: 'fields.authors_hint.input_field')]
    public function authorsHintInputFieldCallback(DataContainer $dc, string $extendedLabel): string
    {
        return "<div class='widget'><p class='tl_help tl_source_tip' data-contao--tooltips-target='tooltip'>{$GLOBALS['TL_LANG'][self::STR_TABLE]['authors_hint']}</p></div>";
    }

    #[AsCallback(table: self::STR_TABLE, target: 'fields.authors1.options')]
    public function typeIdOptions(DataContainer $dc): array
    {
        $options = [];

        $authors = SourcesAuthorModel::findAll();

        if (null !== $authors) {
            foreach ($authors as $author) {
                $options[$author->id] = $author->getUniqueAuthor(false);
            }
        }

        // asort($options);

        return $options;
    }

    #[AsCallback(table: self::STR_TABLE, target: 'fields.singleSRC.load')]
    public function singleSRCOnLoad(mixed $currentValue, DataContainer $dc): mixed
    {
        if ($dc->activeRecord) {
            switch ($dc->activeRecord->type) {
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

    public function buildImage(string|null $singleSrc, array $size = [160, 160, 'proportional']): \stdClass
    {
        $objResult = new \stdClass();

        if ($singleSrc) {
            // get a figure builder instance
            $figureBuilder = $this->imageStudio->createFigureBuilder();

            try {
                $figure = $figureBuilder->fromUUID($singleSrc)->setSize($size)->enableLightbox()->build();
                // attention: this is our own template, see:
                // contao/templates/backend/ResumableWidget/image.html5
                $template = new FrontendTemplate('image');
                // get the template data and change it
                $templateData = $figure->getLegacyTemplateData();
                // we add an image ID and use the uuid for it, this is necessary for the
                // magnifier glass javascript
                $templateData['picture']['img']['id'] = $templateData['uuid'];
                // set the data
                $template->setData($templateData);
                // parse template
                $imageHtml = $template->parse();
            } catch (\Exception $e) {
                $imageHtml = $e->getMessage();
                $figure = null;
            }
        } else {
            $imageHtml = 'kein Scan';
            $figure = null;
        }

        $objResult->image = $imageHtml;
        $objResult->figure = $figure;

        return $objResult;
    }
}
