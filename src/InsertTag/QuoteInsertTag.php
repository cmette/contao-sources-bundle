<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle\InsertTag;

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\StringUtil;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsInsertTag('quote')]
class QuoteInsertTag
{
    use InsertTagHelperTrait;

    /**
     * The HelperTrait requires a constructor
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        global $objPage;

        if (null === $insertTag->getParameters()->get(0)) {
            throw new InvalidInsertTagException('Missing parameters for insert tag.');
        }

        $sourceId = $insertTag->getParameters()->get(0);

        $linktext   = "Quelle?";
        $title      = "Die Quelle mit der ID:$sourceId konnte nicht gefunden werden!";

        if (\is_int((int) $sourceId))
        {
            if ($source = SourcesEntityModel::findById($sourceId))
            {
                if ($authorsCollection = $source->getAuthorsAsCollection())
                {
                    // test for page parameter 'pXX'
                    $pageParameter = $insertTag->getParameters()->get(1);
                    $pageCondiition = (null !== $pageParameter) && ('p' === $pageParameter[0]) && (\strlen($pageParameter) > 1);
                    // page number given?
                    $pages = $pageCondiition ? ', S. '.substr($pageParameter, 1) : '';
                    // build the replacement
                    $a = $source->getAuthorsAsAPAString();
                    $linktext   = $a->authors.$pages;
                    $title      = $a->title.$pages;;
                } else {
                    // no authors available
                }
            } else {
                // source not found mybe deleted?
            }
        } else {
            // sourceId is not a number
            $title = "Die ID der Quelle [ID:$sourceId] muss eine Ganzzahl sein!";
        }

        return new InsertTagResult($this->link($source, $linktext, $title), OutputType::text);
    }
}
