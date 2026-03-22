<?php

namespace Cmette\ContaoSourcesBundle\InsertTag;

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;

#[AsInsertTag('source')]
class SourceInsertTag
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
                $result = "<span style='color:red;'>keine Quelle mit ID $sourceId</span>";
            } else {
                $result = $source->title;
            }
        } else {
            $result = "<span style='color:red;'>fehlerhafte Source-ID $sourceId | muss eine Ganzzahl sein!</span>";
        }

        return new InsertTagResult($result, OutputType::text);
    }
}