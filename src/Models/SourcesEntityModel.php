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
use Contao\System;

use Symfony\Component\HttpFoundation\Request;

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
    use RequestCheckerTrait;
    use Model\MetadataTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_entity';

    /**
     * vordefinierte Quellentypen.
     */
    public const SOURCE_TYPES = [
        'monograph',    // Monographie
        'anthology',    // Sammelband
        'series',       // Reihe
        'essay',        // Aufsatz
        'periodicals',  // Periodikum
        'onlinesource', // URL, URN, DOI etc
        'map',          // Landkarte, Riss, Skizze
        'handwriting',  // Handschrift
        'photography',  // Fotografie
    ];

    /**
     * returns an array of data from the authors rowWizard
     *
     * @return array
     */
    public function getAuthorsAsArray(): array
    {
        // Achtung! $this->>authors enthält keine reinen Autoren, es ist ein serialisiertes Array mit Autoren und anderen Daten,
        // so wie sie im entsprechenden rowWizard im DCA codiert wurden
        $arrAuthors = [];

        foreach (StringUtil::deserialize($this->authors, true) as $author)
        {
            // bei einem FrontendRequest werden nur publizierte Autoren berücksichtigt
            $arrColumns = $this->isBackendRequest() ? ['id = ?'] : ['id = ?', "published = '1'"];
            $arrValues  = [$author['author']];

            $modelAuthor = SourcesAuthorModel::findBy($arrColumns, $arrValues);

            if($modelAuthor !== null) {
                // author found
                $row = $modelAuthor->row();
                $row['role'] = $author['role'];
                $row['enabled'] = (bool)$author['enable'];

                if($this->isBackendRequest()) {
                    $arrAuthors[] = $row;
                } else {
                    if($row['enabled']) $arrAuthors[] = $row;
                }
            } else {
                // author deleted?
                // do nothin at this time
            }
        }

        return $arrAuthors;
    }
    /**
     * returns a collection of SourcesAuthorModels
     *
     * @return Collection|null
     */
    public function getAuthorsAsCollection(): Collection|null
    {
        // get only authors by special destructuring
        $arrAuthorIds = array_map(fn($v) => $v['author'], StringUtil::deserialize($this->authors, true));

        return SourcesAuthorModel::findMultipleByIds($arrAuthorIds);
    }

    /**
     * @param bool $inlineQuote if true, generate authors for inline quote,
     *                          if false, generate authors for bibliography
     * @param int $count        number of max authors -> APA=2
     *
     * @return \StdClass
     */
    public function getAuthorsAsAPAString(bool $inlineQuote = true, int $count = 2): \StdClass
    {
        $arrAuthors = [];
        $strAuthors = '';

        $authorsCollection = $this->getAuthorsAsCollection();

        if($authorsCollection)
            foreach ($authorsCollection as $author)
                $arrAuthors[] = "$author->family_name" .
                    (!$inlineQuote ? (!empty($author->first_name) ? ", {$author->first_name[0]}." : '') : '');

        if(count($authorsCollection) > $count) {
            $last       = $arrAuthors[count($arrAuthors) -1];
            $arrAuthors = array_slice($arrAuthors, 0, count($arrAuthors) -1);
            $strAuthors = ($inlineQuote ? "{$arrAuthors[0]} et al." : implode(', ',$arrAuthors) . " & $last");
        } elseif (count($authorsCollection) == $count) {
            $arrAuthors = array_slice($arrAuthors, 0, 2);
            $strAuthors = implode(' & ',$arrAuthors);
        } else {
            $strAuthors = implode('',$arrAuthors);
        }

        $withTitle = !empty($this->title) ? ", $this->title" : '';
        $year = !empty($this->year) ? ", $this->year" : '';

        $result = new \StdClass();
        $result->authors = $strAuthors.$year;
        $result->title   = $strAuthors.$withTitle.$year;

        return $result;
    }



    /**
     * provides the series inside twig templates
     *
     * @return SourcesSerieModel|null
     */
    public function getSerie(): SourcesSerieModel|null
    {
        if(!$this->addSeries) return null;

        $serie = SourcesSerieModel::findByPk($this->series);

        if($this->isFrontendRequest()) {
            $serie = $serie->published ? $serie : null;
        };

        return $serie;
    }

    /**
     * provides the publisher inside twig templates
     *
     * @return SourcesPublisherModel|null
     */
    public function getPublisher(): SourcesPublisherModel|null
    {
        $publisher = SourcesPublisherModel::findByPk($this->publisher);

        if($publisher !== null)
            if($this->isFrontendRequest()) $publisher = $publisher->published ? $publisher : null;

        return $publisher;
    }

    /**
     * @return array|null
     */
    public function getCatalogs(): array|null
    {
        if(!$this->addCatalogs) return null;

        // Achtung! $this->>catalogs enthält keine reinen Bibliotheken, es ist ein serialisiertes Array mit Bibliotheken
        // und anderen Daten, so wie sie im entsprechenden rowWizard im DCA codiert wurden
        $arrCatalogs = [];

        foreach (StringUtil::deserialize($this->catalogs, true) as $catalog) {
            // bei einem FrontendRequest werden nur publizierte Kataloge berücksichtigt
            $arrColumns = $this->isBackendRequest() ? ['id = ?'] : ['id = ?', "published = '1'"];
            $arrValues = [$catalog['provider']];

            $modelLibrary = SourcesLibraryModel::findBy($arrColumns, $arrValues);

            if ($modelLibrary !== null) {
                $row['provider'] = $modelLibrary->row();

                $row['signature'] = $catalog['signature'];
                $row['url'] = $catalog['url'];
                $row['date'] = $catalog['date'];

                $row['enabled'] = (bool)$catalog['enable'];

                if($this->isBackendRequest()) {
                    $arrCatalogs[] = $row;
                } else {
                    if($row['enabled']) $arrCatalogs[] = $row;
                }
            }
        }
        return $arrCatalogs;
    }

    public function getDigitalcopies(): array|null
    {
        if(!$this->addDigitalCopies) return null;

        // Achtung! $this->>dataprovider enthält keine reinen Datengeber, es ist ein serialisiertes Array mit Datengebern
        // und anderen Daten, so wie sie im entsprechenden rowWizard im DCA codiert wurden
        $arrDigitalcopies = [];

        foreach (StringUtil::deserialize($this->digitalcopies, true) as $catalog) {
            // bei einem FrontendRequest werden nur publizierte Kataloge berücksichtigt
            $arrColumns = $this->isBackendRequest() ? ['id = ?'] : ['id = ?', "published = '1'"];
            $arrValues = [$catalog['provider']];

            $modelLibrary = SourcesLibraryModel::findBy($arrColumns, $arrValues);

            if ($modelLibrary !== null) {
                $row['provider'] = $modelLibrary->row();

                $row['url'] = $catalog['url'];
                $row['date'] = $catalog['date'];

                $row['enabled'] = (bool)$catalog['enable'];

                if($this->isBackendRequest()) {
                    $arrDigitalcopies[] = $row;
                } else {
                    if($row['enabled']) $arrDigitalcopies[] = $row;
                }
            }
        }
        return $arrDigitalcopies;
    }






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
