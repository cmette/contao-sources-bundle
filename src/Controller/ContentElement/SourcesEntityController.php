<?php

namespace Cmette\ContaoSourcesBundle\Controller\ContentElement;

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: 'sources_entity',category: 'sources')]
class SourcesEntityController extends AbstractContentElementController
{
    public function __construct(private readonly Studio $studio) {}
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $source = SourcesEntityModel::findById($model->sources_entity);

        $template->set('source',    $source);

        $template->set('text', $source->text ?: '');

        $figure = !$source->addImage ? null : $this->studio
            ->createFigureBuilder()
            ->fromUuid($source->singleSRC ?: '')
            ->setSize($source->size)
            ->setOverwriteMetadata($source->getOverwriteMetadata())
            ->enableLightbox($source->fullsize)
            ->buildIfResourceExists()
        ;

        $template->set('image', $figure);
        $template->set('layout', $source->floating);

        // handle Backend Request
        if ($this->isBackendScope($request)) {
            return $template->getResponse();
        }

        $response = (bool)$source->published ? $template->getResponse() : new Response();

        return $response;
    }
}