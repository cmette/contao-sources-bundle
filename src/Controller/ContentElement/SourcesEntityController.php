<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle\Controller\ContentElement;

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Cmette\ContaoSourcesBundle\Models\SourcesSettingModel;
use Contao\Config;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: 'sources_entity', category: 'sources')]
class SourcesEntityController extends AbstractContentElementController
{
    // this code comes from:
    // vendor/contao/core-bundle/src/Controller/ContentElement/TextController.php

    public function __construct(private readonly Studio $studio)
    {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $source     = SourcesEntityModel::findById($model->sources_entity);
        $settings   = SourcesSettingModel::findOneBy("published = '1'", [1]);

        $template->set('settings',  $settings);
        $template->set('source',    $source);

        if($source) {
            // source found

            $figure = !$source->addImage ? null : $this->studio
                ->createFigureBuilder()
                ->fromUuid($source->singleSRC ?: '')
                ->setSize($source->size)
                ->setOverwriteMetadata($source->getOverwriteMetadata())
                ->enableLightbox($source->fullsize)
                ->buildIfResourceExists();

            $template->set('layout', $source->floating);
        } else {
            // source not available
            $figure = null;
            $template->set('layout', 'above');
        }

        $template->set('image', $figure);

        // handle Backend Request
        if ($this->isBackendScope($request)) {
            return $template->getResponse();
        }

        return $source ? $template->getResponse() : new Response();
    }
}
