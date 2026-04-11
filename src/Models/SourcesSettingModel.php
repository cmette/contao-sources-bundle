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
 * @method static SourcesSettingModel|null                                      findById($id, array $opt=array())
 * @method static Collection|array<SourcesSettingModel>|SourcesSettingModel|null findByPid($val, array $opt = [])
 */
class SourcesSettingModel extends Model
{
    public const MODES = [
        'tagged',   // Display source code in the tagged format
        'apa',      // Display source code in the APA format
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_setting';

}
