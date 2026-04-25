<?php

use Cmette\ContaoSourcesBundle\Models\SourcesSettingModel;
use Cmette\ContaoSourcesBundle\Utils\DcaUtils;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

$strTable = 'tl_sources_setting';

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
            'mode'                  => DataContainer::SORT_ASC,
            'flag'                  => 1,
            'disableGrouping'       => false,
			'fields'                => ['name'],
			'headerFields'          => ['name'],
			'panelLayout'           => 'filter;sort,search,limit',
            'defaultSearchField'    => 'name',
            #'renderAsGrid'  => true,
			#'limitHeight'   => 160

            # requires special bundle oneup/contao-backend-sortable-list-views
            'sortableListView' => true,
		],
		'label' =>  [
			'fields' =>  ['name'],
            // If true Contao will generate a table header with column names (e.g. back end member list)
            // If the DCA uses showColumns then the return value of the list.label.label-Callback
            // must be an array of strings. Otherwise just the label as a string.
            'showColumns' => false,
			#'format' => '%s',
		],
        'operations' =>  ['edit','!toggle','!delete',],
	],

	// Palettes
	'palettes' =>  [
		'__selector__'  =>  [],
		'default'       =>
            '{name_legend},name;' .
            '{mode_legend},mode;' .
            '{bibliography_legend},sourcesPage;' .
            '{authors_legend};' .
            '{titles_legend};' .
            '{series_legend};' .
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
        'published' => [
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType' => 'checkbox',
            'toggle'    => true,
            'eval' => [
                'unique'    => true,
                'doNotCopy' => true,
            ],
            'sql' => [
                'type' => 'boolean',
                'default' => false,
            ],
        ],

        /**********************************************************************
         * name_legend
         **********************************************************************/
        'name' => [
            'inputType' => 'text',
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'          => [
                'mandatory' => true,
                'unique'    => false,
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
         * mode_legend
         **********************************************************************/
        'mode' => [
            'inputType' => 'radio',
            'options' => SourcesSettingModel::MODES,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w25'
            ],
            'sql'       => [
                'type'      => 'string',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        # die Seite, auf die alle Zitate verweisen
        'sourcesPage' => [
            'exclude' => true,
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => [
                'fieldType'  => 'radio',
            ],
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'default' => 0,
            ],
            'relation' => [
                'type' => 'hasOne',
                'load' => 'lazy',
            ],
        ],
        /**********************************************************************
         * **_legend
         **********************************************************************/
	],
];