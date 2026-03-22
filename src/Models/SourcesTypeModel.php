<?php

declare(strict_types=1);

/*
 * Contao Supervisor Bundle for Contao Open Source CMS
 *
 * Copyright (c) 2024 C. Mette
 *
 * @package    contao-supervisor-bundle
 * @link       https://github.com/cmette/contao-supervisor-bundle
 * @license    LGPL-3.0-or-later
 * @author     Christian Mette
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cmette\ContaoSourcesBundle\Models;

use Contao\Model;
use Contao\Model\Collection;

/**
 * Reads and writes source entities. This refers to abstract
 * sources such as literary sources, maps, manuscripts, photos, etc.
 *
 * @property int $id
 * @property int $tstamp
 *
 * @method static SourcesTypeModel|null findById($id, array $opt=array())
 * @method static Collection|array<SourcesTypeModel>|SourcesTypeModel|null findByPid($val, array $opt = [])
 */
class SourcesTypeModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_sources_type';
}
