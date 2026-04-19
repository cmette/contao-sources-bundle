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
use Cmette\ContaoSourcesBundle\Models\SourcesSettingModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsInsertTag('quote')]
class QuoteInsertTag
{
    use InsertTagHelperTrait;

    public ?SourcesSettingModel $settings = null;

    /**
     * using the HelperTrait requires a constructor.
     */
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
        $this->settings = SourcesSettingModel::findOneBy("published = '1'", [1]);
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        global $objPage;

        if (null === $insertTag->getParameters()->get(0)) {
            throw new InvalidInsertTagException('Missing parameters for insert tag.');
        }

        $sourceId = $insertTag->getParameters()->get(0);

        $linktext = 'Quelle?';
        $title = "Die Quelle mit der ID:$sourceId konnte nicht gefunden werden!";

        if (\is_int((int) $sourceId)) {
            if ($source = SourcesEntityModel::findById($sourceId)) {
                // test for page parameter 'pXX'
                $pageParameter = $insertTag->getParameters()->get(1);
                $pageCondiition = (null !== $pageParameter) && ('p' === $pageParameter[0] || 'f' === $pageParameter[0]) && (\strlen($pageParameter) > 1);
                // page number or folio given?
                if($pageCondiition) {
                    switch ($pageParameter[0]) {
                        case 'p':
                            $pages =  ', S.'.substr($pageParameter, 1);
                            break;
                        case 'f':
                            $pages =  ', folio '.substr($pageParameter, 1);
                            break;
                        default:
                            $pages = '';
                    }
                    $pages = '';
                } else {
                    $pages = '';
                }
                // build the replacement
                $authors = $source->getAuthorsAsString();
                // check empty link
                if(!empty($authors)) $linktext = $authors.$pages; else $source = null;
                //
                $title = $linktext;
            } else {
                // source not found mybe deleted?
            }
        } else {
            // sourceId is not a number
            $title = "Die ID der Quelle [ID:$sourceId] muss eine Ganzzahl sein!";
        }

        return new InsertTagResult($this->link($source, htmlspecialchars($linktext), $title), OutputType::html);
    }
}
