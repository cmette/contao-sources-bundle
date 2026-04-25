<?php

declare(strict_types=1);

namespace Cmette\ContaoSourcesBundle\InsertTag;

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\CoreBundle\Routing\Page\PageRoute;
use Contao\PageModel;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

trait InsertTagHelperTrait
{
    private function link(SourcesEntityModel|null $source, string $text, string $title = '', string $target = '_self'): string
    {
        // build a link
        $page = PageModel::findById($this->settings->sourcesPage);
        $url = $this->urlGenerator->generate(PageRoute::PAGE_BASED_ROUTE_NAME, [RouteObjectInterface::CONTENT_OBJECT => $page]);

        return $source ?
            "(<a href=\"$url#sources_entity-{$source->id}\" title=\"$title\" target=\"$target\" rel=\"noreferrer noopener\">$text</a>)" :
            "(<span title='$title'>$text)</span>";
    }
}
