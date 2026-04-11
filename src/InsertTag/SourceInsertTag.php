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

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsInsertTag('source')]
class SourceInsertTag
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
                // no source found
                $result = "<span style='color:red;'>keine Quelle mit ID $sourceId</span>";
            } else {
                // source available
                $subcommand = $insertTag->getParameters()->get(1);

                switch ($subcommand) {
                    case 'occurrences':
                        $occurrences = StringUtil::deserialize($source->occurrences, true);
                        $result = '';

                        foreach ($occurrences as $cteId => $occurrence) {
                            #$objPage = PageModel::findById($occurrence['pageId']);
                            #$pageUrl = System::getContainer()->get('contao.routing.content_url_generator')->generate($objPage, [], UrlGeneratorInterface::ABSOLUTE_URL);
                            #$result .= "<p></p><a href='$pageUrl'>$objPage->title $cteId {$occurrence['count']}mal</a></p>";
                        }
                        break;
                    default:
                        $result = 'subcommand?';
                }
            }
        } else {
            $result = "<span style='color:red;'>fehlerhafte Source-ID $sourceId | muss eine Ganzzahl sein!</span>";
        }

        return new InsertTagResult($result, OutputType::text);
    }
}
