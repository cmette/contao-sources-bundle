<?php

use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

$strTable = 'tl_sources_serie';

System::loadLanguageFile($strTable);

$GLOBALS['TL_DCA'][$strTable] = [
	'config' =>  [
		'dataContainer'     => DC_Table::class,
        'switchToEdit'      => true,
		'enableVersioning'  => true,
        'sql' => [
            'keys' => [
                'id'        => 'primary',
                'tstamp'    => 'index',
            ],
        ],
	],
	'list' => [
		'sorting' =>  [
            'mode'                  => DataContainer::MODE_SORTED,
			'fields'                => ['title'],
			'headerFields'          => ['title'],
			'panelLayout'           => 'filter;sort,search,limit',
            'defaultSearchField'    => 'type_sgl',
            #'renderAsGrid'  => true,
			#'limitHeight'   => 160

            # requires special bundle oneup/contao-backend-sortable-list-views
            'sortableListView' => true,
		],
		'label' =>  [
			'fields' =>  ['title'],
            // If true Contao will generate a table header with column names (e.g. back end member list)
            // If the DCA uses showColumns then the return value of the list.label.label-Callback
            // must be an array of strings. Otherwise just the label as a string.
            'showColumns' => false,
			#'format' => '%s',
		],
		'operations' =>  [
            'edit',
            'activate',
            '!delete',
            #'toggle',
        ],
	],

	// Palettes
	'palettes' =>  [
		'__selector__'  =>  [],
		'default'       =>
            '{type_legend},title;' .
            '',
	],

	// Subpalettes
	'subpalettes' =>  [
        //'addDigitalCopy'    => 'link_digitalcopy,extent_digitalcopy',
    ],

	// Fields
	'fields' => [
        /**********************************************************************
         * without legend
         **********************************************************************/
        'id'        => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'tstamp'    => ['sql' => "int(10) unsigned NOT NULL default 0",],
        'published' => ['toggle' => true,'inputType' => 'checkbox','sql' => ['type' => 'boolean', 'default' => false],],
        # requires special bundle oneup/contao-backend-sortable-list-views
        #'sorting'=> ['sql' => "int(10) unsigned NOT NULL default 0",],
        /**********************************************************************
         * type_legend
         **********************************************************************/
        'title' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => true,
                'unique'    => true,
                'tl_class'  =>'w50'
            ],
            'sql'       => [
                'type'      => 'string',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * **_legend
         **********************************************************************/
	],
];