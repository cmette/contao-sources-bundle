<?php

namespace Cmette\ContaoSourcesBundle\Controller\ContentElement;

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: 'sources_entity',category: 'sources')]
class SourcesEntityController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $source = SourcesEntityModel::findById($model->sources_entity);

        $template->set('source', $source);

        return $template->getResponse();
    }
}