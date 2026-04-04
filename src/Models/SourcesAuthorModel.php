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
        'isPublisher',      # ist HerausgeberIn
        'isCoAuthor',       # ist MitautorIn
        'isContributor',    # ist MitarbeiterIn
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

        $authors = self::findAll();

        if($authors !== null)
            foreach ($authors as $author) {
                $options[$author->id] = $author->getUniqueAuthor($withCount);
            }

        natcasesort($options);

        return $options;
    }

    public function getUniqueAuthor(bool $withCount = true): string
    {
        return $this->family_name.(!empty($this->first_name)?", $this->first_name":'') . ($withCount ? " ({$this->countUsage()})" : '');
    }

    public function countUsage(): int
    {
        $id = $this->id;
        $len= strlen((string)$id);

        $entities = SourcesEntityModel::findBy(["authors LIKE '%\"author\";s:$len:\"$id\"%'"],[]);

        return !is_null($entities) ? $entities->count() : 0;
    }
}
