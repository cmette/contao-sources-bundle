<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Sources Bundle.
 *
 * (c) Christian Mette
 *
 * @license LGPL-3.0-or-later
 */

namespace Cmette\ContaoSourcesBundle\Models;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;

/**
 * Reads and writes source entities. This refers to abstract sources such as
 * literary sources, maps, manuscripts, photos, etc.
 *
 * @property int $id
 * @property int $tstamp
 *
 * @method static SourcesEntityModel|null                                      findById($id, array $opt=array())
 * @method static Collection|array<SourcesEntityModel>|SourcesEntityModel|null findByPid($val, array $opt = [])
 */
class SourcesEntityModel extends Model
{
    /**
     * vordefinierte Quellentypen.
     */
    public const SOURCE_TYPES = [
        'monograph', // Monographie
        'anthology', // Sammelband
        'series', // Reihe
        'essay', // Aufsatz
        'periodicals', // Periodikum
        'onlinesource', // URL, URN, DOI etc
        'map', // Landkarte, Riss, Skizze
        'handwriting', // Handschrift
        'photography', // Fotografie
    ];

    /**
     * @return Collection
     */
    public function getAuthors(): array
    {
        // Achtung! das sind keine Autoren, es ist ein serialisiertes Array mit Autoren und anderen Daten,
        // so wie sie im entsprechenden rowWizard im DCA codiert wurden
        $arrAuthors = [];

        foreach (StringUtil::deserialize($this->authors, true) as $author) {
            $modelAuthor = SourcesAuthorModel::findById($author['author']);
            if($modelAuthor === null) continue;

            $row = $modelAuthor->row();
            $row['role'] = $author['role'];
            $arrAuthors[] = $row;
        }

        return $arrAuthors;
    }

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_entity';

    public function registerOccurrence(ResolvedInsertTag $insertTag, string $pages, bool $published = true): mixed
    {
        global $objPage;

        // ToDo: hier könnte published noch in die Konfigurations-Optionen mit
        // aufgenommen werden
        $arrArticleIds = ArticleModel::findBy(['pid = ?', 'published = ?'], [$objPage->id, $published])->fetchEach('id');

        $contentElements = ContentModel::findBy(['pid IN (?)', 'text IS NOT NULL', "text LIKE '%{{quote%'"], [implode(',', $arrArticleIds)]);

        foreach ($contentElements as $contentElement) {
            $cteId = $contentElement->id;

            // $pageUrl = System::getContainer()->get('contao.routing.content_url_generator')->generate($objPage, [], UrlGeneratorInterface::ABSOLUTE_URL);

            // zähle vorkommen
            $count = substr_count($contentElement->text, '{{quote');
            $occurrences = StringUtil::deserialize($this->occurrences, true);

            // dump("gefunden in CTE.ID: $cteId\nVorkommen: $count\nurl: $pageUrl");

            $occurrences[$cteId] = ['count' => $count, 'pageId' => $objPage->id, 'pages' => $pages];

            $this->occurrences = serialize($occurrences);
            // dump($this->occurrences);
            $this->save();
        }

        return \count($contentElements);
    }
}
