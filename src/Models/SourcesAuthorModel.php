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

use Contao\Model;
use Contao\Model\Collection;
use phpDocumentor\Reflection\Types\True_;

/**
 * Reads and writes source entities. This refers to abstract sources such as
 * literary sources, maps, manuscripts, photos, etc.
 *
 * @property int $id
 * @property int $tstamp
 *
 * @method static SourcesAuthorModel|null                                      findById($id, array $opt=array())
 * @method static Collection|array<SourcesAuthorModel>|SourcesAuthorModel|null findByPid($val, array $opt = [])
 */
class SourcesAuthorModel extends Model
{
    public const ROLES = [
        'isPublisher', // ist HerausgeberIn
        'isCoAuthor', // ist MitautorIn
        'isContributor', // ist MitarbeiterIn
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_author';

    public static function getAllUniqueAuthors(bool $withCount = true): array
    {
        $options = [];

        // "try" is required here because of the calls from the DCA
        try {
            $authors = self::findAll();

            if (null !== $authors) {
                foreach ($authors as $author) {
                    $options[$author->id] = $author->getAuthorsAsString($withCount);
                }
            }

            natcasesort($options);
        } catch (\Exception $e) {

        }

        return $options;
    }

    /**
     * @param bool $withCount
     * @return string
     */
    public function getAuthorsAsString(bool $withCount = true): string
    {
        $first_names = $this->getFirstNames();

        return $this->family_name.(!empty($this->first_name) ? ", $first_names" : '').($withCount ? " ({$this->countUsage()})" : '');
    }

    public function getFirstNames(): string
    {
        $matches = [];
        // extract all intials
        $m = preg_match_all("/((?<=^)|(\s)|(-))\p{L}/", $this->first_name??'', $matches);
        // compress and cleanup matches
        $u = array_map(
            function($initials) { return trim($initials); },
            array_filter($matches[0],function($initials) { if(!empty(trim($initials))) return true; else return false;})
        );
        // correct special hyphen
        return str_replace(',-', '-', implode('.,', $u??[])).'.';
    }

    public function countUsage(): int
    {
        $id = $this->id;
        $len = \strlen((string) $id);

        $entities = SourcesEntityModel::findBy(["authors LIKE '%\"author\";s:$len:\"$id\"%'"], []);

        return null !== $entities ? $entities->count() : 0;
    }
}
