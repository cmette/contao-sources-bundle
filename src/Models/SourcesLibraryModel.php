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
 * @method static SourcesLibraryModel|null                                       findById($id, array $opt=array())
 * @method static Collection|array<SourcesLibraryModel>|SourcesLibraryModel|null findByPid($val, array $opt = [])
 */
class SourcesLibraryModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_library';

    /**
     * provides an array of libraries and data providers to use in select options.
     */
    public static function getLibrariesOptions(bool $asCatalog = true, bool $asProvider = true): array
    {
        $options = [];

        try {
            if ($libraries = self::findBy(['asCatalog = ? OR asProvider = ?'], [$asCatalog, $asProvider])) {
                foreach ($libraries as $library) {
                    $options[$library->id] = "$library->name ($library->abbreviation)";
                }
            }

            asort($options);
        } catch (\Exception $e) {

        }

        return $options;
    }

    /**
     * count all usages inside a rowWizard field.
     */
    public function countUsage(): int
    {
        $id = $this->id;
        $len = \strlen((string) $id);

        $entities = SourcesEntityModel::findBy(["catalogs LIKE '%\"provider\";s:$len:\"$id\"%'"], []);

        return null !== $entities ? $entities->count() : 0;
    }
}
