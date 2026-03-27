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

#[AsInsertTag('quote')]
class QuoteInsertTag
{
    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        if (null === $insertTag->getParameters()->get(0)) {
            throw new InvalidInsertTagException('Missing parameters for insert tag.');
        }

        $sourceId = $insertTag->getParameters()->get(0);

        if (\is_int((int) $sourceId)) {
            $source = SourcesEntityModel::findById($sourceId);

            if (null === $source) {
                $result = "<span style='color:red;'>keine Quelle mit ID:$sourceId</span>";
            } else {
                // get all authors
                $arrAuthorIds = StringUtil::deserialize($source->authors, true);

                $authors = SourcesAuthorModel::findMultipleByIds($arrAuthorIds);

                if (null !== $authors) {
                    // authors available -> build authors list
                    $arrFamilyNames = $authors->fetchEach('family_name');
                    // test for page parameter 'pXX'
                    $pageParameter = $insertTag->getParameters()->get(1);

                    if (
                        (null !== $pageParameter)
                       && ('p' === $pageParameter[0])
                        && (\strlen($pageParameter) > 1)
                    ) {
                        // page number given
                        $pages = ', S. '.substr($pageParameter, 1);
                    } else {
                        // no page number -> mistake?
                        $pages = '';
                    }
                    // build the replacement
                    $result = ''.implode('/', $arrFamilyNames).$pages;
                    // register the occurrence
                    $source->registerOccurrence($insertTag, $pages);
                } else {
                    // no authors available
                    $result = "<span style='color:red;'>keine Autoren für Quelle mit ID:$sourceId</span>";
                }
            }
        } else {
            $result = "<span style='color:red;'>keine Quelle mit ID:$sourceId für Zitat | muss eine Ganzzahl sein!</span>";
        }

        return new InsertTagResult("[$result]", OutputType::text);
    }
}
