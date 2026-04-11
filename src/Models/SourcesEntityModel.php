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

use Contao\DataContainer;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use Symfony\Component\VarDumper\Caster\ScalarStub;
use Symfony\Component\VarDumper\VarDumper;

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
    use Model\MetadataTrait;
    use RequestCheckerTrait;

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

    private const dbg = false;

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_entity';

    /**
     * returns an array of data from the authors rowWizard.
     */
    public function getAuthorsAsArray(): array
    {
        // Achtung! $this->>authors enthält keine reinen Autoren, es ist ein
        // serialisiertes Array mit Autoren und anderen Daten, so wie sie im
        // entsprechenden rowWizard im DCA codiert wurden
        $arrAuthors = [];

        foreach (StringUtil::deserialize($this->authors, true) as $author) {
            // bei einem FrontendRequest werden nur publizierte Autoren berücksichtigt
            $arrColumns = $this->isBackendRequest() ? ['id = ?'] : ['id = ?', "published = '1'"];
            // extract the author key
            $arrValues = [$author['author']];
            // find all authors
            $modelAuthor = SourcesAuthorModel::findBy($arrColumns, $arrValues);

            if (null !== $modelAuthor) {
                // author found
                $row = $modelAuthor->row();
                $row['role'] = $author['role'];
                $row['enabled'] = (bool) $author['enable'];

                if ($this->isBackendRequest()) {
                    $arrAuthors[] = $row;
                } else {
                    if ($row['enabled']) {
                        $arrAuthors[] = $row;
                    }
                }
            }
            // author deleted? do nothin at this time
        }

        return count($arrAuthors) === 0 ? // ToDo: internationalization
            [['family_name' => 'ohne Autor', 'first_name' => '', 'role' => '', 'published' => true, 'enabled' => true]] :
            $arrAuthors;
    }

    /**
     * returns a collection of SourcesAuthorModels.
     */
    public function getAuthorsAsCollection(): Collection|null
    {
        // get only authors by special destructuring
        $arrAuthorIds = array_map(static fn ($v) => $v['author'], StringUtil::deserialize($this->authors, true));

        return SourcesAuthorModel::findMultipleByIds($arrAuthorIds);
    }

    /**
     * @param bool $inlineQuote if true, generate authors for inline quote,
     *                          if false, generate authors for bibliography
     * @param int  $count       number of max authors -> APA=2
     */
    public function getAuthorsAsAPAString(bool $inlineQuote = true, int $count = 2): \stdClass
    {
        $arrAuthors = [];
        $strAuthors = 'ohne Autor';

        $authorsCollection = $this->getAuthorsAsCollection();

        if ($authorsCollection)
        {
            $arrAuthors = array_map(fn($author) => $author['family_name'] . (!$inlineQuote ? (!empty($author['first_name']) ? ", {$author['first_name'][0]}." : '') : ''), $authorsCollection->fetchAll());

            if (\count($authorsCollection) > $count) {
                $last = $arrAuthors[\count($arrAuthors) - 1];
                $arrAuthors = \array_slice($arrAuthors, 0, \count($arrAuthors) - 1);
                $strAuthors = ($inlineQuote ? "{$arrAuthors[0]} et al." : implode(', ', $arrAuthors) . " & $last");
            } elseif (\count($authorsCollection) === $count) {
                $arrAuthors = \array_slice($arrAuthors, 0, 2);
                $strAuthors = implode(' & ', $arrAuthors);
            } else {
                $strAuthors = implode('', $arrAuthors);
            }
        }

        $withTitle = !empty($this->title) ? ", $this->title" : '';
        $year = !empty($this->year) ? ", $this->year" : '';

        $result = new \stdClass();
        $result->authors = $strAuthors.$year;
        $result->title = $strAuthors.$withTitle.$year;

        return $result;
    }

    /**
     * provides the series inside twig templates.
     */
    public function getSerie(): SourcesSerieModel|null
    {
        if (!$this->addSeries) {
            return null;
        }

        $serie = SourcesSerieModel::findById($this->series);

        if ($this->isFrontendRequest()) {
            $serie = $serie->published ? $serie : null;
        }

        return $serie;
    }

    /**
     * provides the publisher inside twig templates.
     */
    public function getPublisher(): SourcesPublisherModel|null
    {
        $publisher = SourcesPublisherModel::findById($this->publisher);

        if (null !== $publisher) {
            if ($this->isFrontendRequest()) {
                $publisher = $publisher->published ? $publisher : null;
            }
        }

        return $publisher;
    }

    public function getCatalogs(): array|null
    {
        if (!$this->addCatalogs) {
            return null;
        }

        // Achtung! $this->>catalogs enthält keine reinen Bibliotheken, es ist ein
        // serialisiertes Array mit Bibliotheken und anderen Daten, so wie sie im
        // entsprechenden rowWizard im DCA codiert wurden
        $arrCatalogs = [];

        foreach (StringUtil::deserialize($this->catalogs, true) as $catalog) {
            // bei einem FrontendRequest werden nur publizierte Kataloge berücksichtigt
            $arrColumns = $this->isBackendRequest() ? ['id = ?'] : ['id = ?', "published = '1'"];
            $arrValues = [$catalog['provider']];

            $modelLibrary = SourcesLibraryModel::findBy($arrColumns, $arrValues);

            if (null !== $modelLibrary) {
                $row['provider'] = $modelLibrary->row();

                $row['signature'] = $catalog['signature'];
                $row['url'] = $catalog['url'];
                $row['date'] = $catalog['date'];

                $row['enabled'] = (bool) $catalog['enable'];

                if ($this->isBackendRequest()) {
                    $arrCatalogs[] = $row;
                } else {
                    if ($row['enabled']) {
                        $arrCatalogs[] = $row;
                    }
                }
            }
        }

        return $arrCatalogs;
    }

    public function getDigitalcopies(): array|null
    {
        if (!$this->addDigitalCopies) {
            return null;
        }

        // Achtung! $this->>dataprovider enthält keine reinen Datengeber, es ist ein
        // serialisiertes Array mit Datengebern und anderen Daten, so wie sie im
        // entsprechenden rowWizard im DCA codiert wurden
        $arrDigitalcopies = [];

        foreach (StringUtil::deserialize($this->digitalcopies, true) as $catalog) {
            // bei einem FrontendRequest werden nur publizierte Kataloge berücksichtigt
            $arrColumns = $this->isBackendRequest() ? ['id = ?'] : ['id = ?', "published = '1'"];
            $arrValues = [$catalog['provider']];

            $modelLibrary = SourcesLibraryModel::findBy($arrColumns, $arrValues);

            if (null !== $modelLibrary) {
                $row['provider'] = $modelLibrary->row();

                $row['url'] = $catalog['url'];
                $row['date'] = $catalog['date'];

                $row['enabled'] = (bool) $catalog['enable'];

                if ($this->isBackendRequest()) {
                    $arrDigitalcopies[] = $row;
                } else {
                    if ($row['enabled']) {
                        $arrDigitalcopies[] = $row;
                    }
                }
            }
        }

        return $arrDigitalcopies;
    }

    public static function syncQuotes(mixed $varValue, DataContainer $dc): \StdClass
    {
        $result = new \StdClass();

        $result->quotesCount = 0;       //
        $result->additions = 0;         // additions
        $result->deletions = 0;         // deletions
        $result->unknownSources = 0;    // unknown sources

        if ($dc->getCurrentRecord()) {
            self::debug("Es gibt einen \$dc->currendRecord()");
            // array of quotes
            $quotes = [];
            // data row of the content element
            $content = $dc->getCurrentRecord();
            // search for quotes
            $match = preg_match_all('/{{quote::[0-9]+/', $varValue, $quotes);

            if ($match > 0) {
                self::debug("Es gibt $match InsertTag-Matches");
                // some quotes found get associated source ids
                $sourceIds = array_map(
                    static function ($v) {
                        $arr = explode('::', $v);
                        if (\count($arr) > 1) {
                            return $arr[1];
                        }
                    },
                    $quotes[0],
                );
                self::debug("Es wurden folgebnde soirceIDs gefunden [" . implode(', ', $sourceIds) . "]");
                // result message
                $result->quotesCount = count($sourceIds);
                // remove duplicate entries
                $sourceIds2 = array_unique($sourceIds);
                // build a result array for storing in occurrences
                $arrResult1 = [$content['id'] => $sourceIds2];
                //
                self::debug("$result->quotesCount Zitate im CTE {$content['id']} gefunden: QuellenIDs:[" . implode(', ', $sourceIds2) . "]");
                // iterate over all sources
                foreach ($sourceIds2 as $sourceId) {
                    self::debug("Synchonisiere mit Quelle $sourceId");
                    // is the source available?
                    if ($source = self::findById($sourceId)) {
                        // yes, get occurrences
                        $arrOcc1 = StringUtil::deserialize($source->occurrences, true);
                        // any occurrence stored?
                        if (\count($arrOcc1) > 0) {
                            // yes,
                            self::debug("  Die Liste der registrierten Zitate enthält schon folgende Einträge [".implode(',', StringUtil::deserialize($source->occurrences.true)).']');

                            if(in_array($content['id'], $arrOcc1)) {
                                self::debug("  Das Zitat in CTE {$content['id']} ist bereits registriert");
                            } else {
                                self::debug("  Das Zitat in CTE {$content['id']} ist noch nicht registriert");
                                $arrOcc1[] = $content['id'];
                                $source->occurrences = serialize($arrOcc1);
                                $source->save();
                                $result->additions += 1;
                                self::debug("  occurrences nach der Registrierung [".implode(',', StringUtil::deserialize($source->occurrences.true)).']');
                            }

                        } else {
                            // die Liste der Zitate ist noch leer das Zitat befindet sich in $content['id']
                            self::debug("  Die Liste der registrierten Zitate ist noch leer [".implode(',', StringUtil::deserialize($source->occurrences,true)).']');
                            self::debug("  Neues Zitat im CTE {$content['id']} wird zur Quelle Id: $source->id hinzugefügt");
                            $source->occurrences = serialize([$content['id']]);
                            $source->save();
                            $result->additions += 1;
                        }
                    } else {
                        // the quoted source isn't abaliable anymore
                        self::debug("  Die Quelle mit der ID: $sourceId ist nicht vorhanden!");
                        $result->unknownSources += 1;
                    }
                }
            } else {
                self::debug("Es gibt keine InsertTag-Matches");
            }

            self::debug("Verwaiste Einträge für CTE {$content['id']} werden in den Quellen gesucht.");
            self::debug("  suche nach occurrences LIKE '%i:{$content['id']};%'");

            // Gegenprobe, ist dieses CTE  noch in anderen Quellen registriert?
            if ($entities = self::findBy(["occurrences LIKE '%i:{$content['id']};%'"], [])) {
                self::debug($entities);
                $sourceIds3 = $entities->fetchEach('id');
                self::debug("  Quellen, in denen die SourceIds: [" . implode(',',$sourceIds3) . "] referenziert werden.");
                //
                self::debug("  SourceIds2: [".implode(',',$sourceIds2??[])."]");
                $sourcesToCleanup = array_diff($sourceIds3, $sourceIds2??[]);
                self::debug("  diff ergibt cleanUpIds: [" . implode(',',$sourcesToCleanup) . "]");

                if(count($sourcesToCleanup) > 0) {
                    self::debug("Es wurden " . count($sourcesToCleanup) . " Einträge gefunden.");
                } else {
                    self::debug("Es wurden keine Verwaiste Einträge gefunden.");
                }

                foreach ($sourcesToCleanup as $sourceToCleanup) {
                    $source = self::findById($sourceToCleanup);
                    $arrOcc2 = StringUtil::deserialize($source->occurrences, true);
                    $key = array_search((int) $content['id'], $arrOcc2, true);
                    if (false !== $key) {
                        unset($arrOcc2[$key]);
                        $result->deletions += 0;
                    }
                    $source->occurrences = serialize($arrOcc2);
                    $source->save();
                }
            } else {
                self::debug("Das CTE wird in keiner Quelle referenziert.");
            }
        }

        return $result;
    }

    private static function debug(mixed ...$vars): mixed
    {
        if(!self::dbg) return null;

        if (!$vars) {
            VarDumper::dump(new ScalarStub('🐛'));

            return null;
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            VarDumper::dump($vars[0]);
            $k = 0;
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        if (1 < count($vars)) {
            return $vars;
        }

        return $vars[$k];
    }
}
