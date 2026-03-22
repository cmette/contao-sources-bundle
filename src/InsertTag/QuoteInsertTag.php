<?php

namespace Cmette\ContaoSourcesBundle\InsertTag;

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
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

        if (is_integer((int)$sourceId))
        {
            $source = SourcesEntityModel::findById($sourceId);

            if (null === $source) {
                $result = "<span style='color:red;'>keine Quelle mit ID:$sourceId</span>";
            } else {
                // get all authors
                $arrAuthorIds = StringUtil::deserialize($source->authors, true);

                $authors = SourcesAuthorModel::findMultipleByIds($arrAuthorIds);

                if(null !== $authors)
                {
                    // build authors list
                    $arrFamilyNames = $authors->fetchEach('family_name');
                    // test for page parameter 'pXX'
                    $pageParameter = $insertTag->getParameters()->get(1);

                    if(null !== $pageParameter && $pageParameter[0] === 'p') {
                        //
                        $pages =  (strlen($pageParameter) > 1) ? ', S. ' . substr($pageParameter, 1) : '';
                    } else {
                        $pages = '';
                    }
                    $result =  implode('/', $arrFamilyNames) . $pages;
                } else {
                    $result = "<span style='color:red;'>keine Autoren für Quelle mit ID:$sourceId</span>";;
                }
            }
        } else {
            $result = "<span style='color:red;'>keine Quelle mit ID:$sourceId für Zitat | muss eine Ganzzahl sein!</span>";
        }

        return new InsertTagResult("[$result]", OutputType::text);
    }
}