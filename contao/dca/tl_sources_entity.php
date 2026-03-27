<?php

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;

$strTable = 'tl_sources_entity';

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
            'mode'                  => DataContainer::MODE_SORTABLE,
			'fields'                => ['type','title'],
			'headerFields'          => ['type','title'],
			'panelLayout'           => 'filter;sort,search,limit',
            'defaultSearchField'    => 'title',
            #'renderAsGrid'  => true,
			#'limitHeight'   => 160

            # requires special bundle oneup/contao-backend-sortable-list-views
            'sortableListView' => true,
		],
		'label' =>  [
			'fields' =>  ['type','title'],
            // If true Contao will generate a table header with column names (e.g. back end member list)
            // If the DCA uses showColumns then the return value of the list.label.label-Callback
            // must be an array of strings. Otherwise just the label as a string.
            'showColumns' => false,
			'format' => '%s %s',
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
		'__selector__'  =>  ['type','addDigitalCopy'],
        'default' =>
            '{type_legend},type;' .
            '{author_legend},authors;' .
            '{title_legend},title;' .
            '{publisher_legend},publisher,edition,year;' .
            '{data_legend},signature,signature_alt;' .
            '{catalogs_legend},catalogs;' .
            #'{occurrences_legend},occurrences;' .
            ''
        ,
        'series' =>
            '{type_legend},type;' .
            '{author_legend},authors;' .
            '{title_legend},title;' .
            '{volume_legend},series,volume_title,volume,issue;' .
            '{publisher_legend},publisher,edition,year;' .
            '{data_legend},signature,signature_alt;' .
            '{catalogs_legend},catalogs;' .
            #'{occurrences_legend},occurrences;' .
            ''
	],

	// Subpalettes
	'subpalettes' =>  [
        'addDigitalCopy'    => 'url_digitalcopy,extent_digitalcopy',
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
        'type' => [
            'inputType' => 'select',
            'flag'      => DataContainer::SORT_ASC,
            'sorting'   => true,
            'filter'    => true,
            #'foreignKey'=> 'tl_sources_type.type_sgl',
            #'relation'      => ['type'=>'belongsTo', 'load'=>'lazy'],
            'options'   => SourcesEntityModel::SOURCE_TYPES,
            'reference' => &$GLOBALS['TL_LANG'][$strTable]['type_options'],
            'eval'      => [
                'helpwizard'    =>true,
                'chosen'        =>true,
                'submitOnChange'=>true,
                'tl_class'      =>'w25'
            ],
            'sql'           => [
                'type'      => 'text',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ],
        ],
        /**********************************************************************
         * author_legend
         **********************************************************************/
        'authors' => [
            'inputType' => 'select',
            'search'    => true,
            'filter'    => true,
            #'sorting' => true,
            'foreignKey' => 'tl_sources_author.family_name',
            'relation'  => [
                'type'  => 'hasMany',
                'load'  => 'lazy'
            ],
            'eval'      => [
                'includeBlankOption'=> true,
                'blankOptionLabel'  => 'kein/unbekannt',
                'tl_class' => '',
                'multiple' => true,
                'chosen' => true
            ],
            //'sql' => "longblob NULL default NULL",
            'sql' => [
                'type'    => 'blob',
                'notnull' => false,
                'default' => 'NULL',
            ]
        ],
        /**********************************************************************
         * title_legend
         **********************************************************************/
        'title' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => true,
                'unique'    => false,
                //'tl_class'  =>'w25'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 4096,
                //'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * periodika_legend
         **********************************************************************/
        # allg. Periodikum aus dem Reihen-Register
        'series' => [
            'inputType' => 'select',
            'search'    => true,
            'filter'    => true,
            #'sorting' => true,
            'foreignKey' => 'tl_sources_serie.title',
            'relation'  => [
                'type'  => 'hasMany',
                'load'  => 'lazy'
            ],
            'eval'      => [
                'mandatory' => true,
                'includeBlankOption'=> false,
                #'blankOptionLabel'  => 'kein/unbekannt',
                'tl_class' => 'w50',
                'multiple' => false,
                'chosen' => true
            ],
            'sql' => [
                'type'      => 'integer',
                'unsigned'  => true,
                'notnull'   => true,
                'default'   => 0,            ]
        ],
        # Band-Titel, z.b. bei Lexika die Angabe "M bis P" oder "Maulbeere bis Pankow"
        'volume_title' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w50 clr'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        # Band
        'volume' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w16'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 50,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        # Heft/Ausgabe
        'issue' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w16'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * publisher_legend
         **********************************************************************/
        'editor' => [

        ],
        #
        'publisher' => [
            'search'    => true,
            'filter'    => true,
            #'sorting' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_sources_publisher.string',
            'relation'  => [
                'type'  => 'hasMany',
                'load'  => 'lazy'
            ],
            'eval'      => [
                'mandatory' => false,
                'includeBlankOption' => false,
                'tl_class' => 'w66',
                'multiple' => false,
                'chosen' => true
            ],
            'sql'           => [
                'type'      => 'integer',
                'unsigned'  => true,
                'notnull'   => true,
                'default'   => 0,
            ],
        ],
        'year' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w16'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 256,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        'edition' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w16'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 5,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * references_legend
         **********************************************************************/
        # list of links to catalogs
        'catalogs' => [
            'inputType' => 'rowWizard',
            'fields' => [
                'provider' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['provider'],
                    'inputType' => 'select',
                    'options'   => ['dnb', 'doi', 'lasa'],
                    'eval'          => [
                        'mandatory' => true,
                        'tl_class'  => 'w25'
                    ],
                ],
                'url' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['url'],
                    'inputType'     => 'text',
                    'search'        => true,
                    'eval'          => [
                        'mandatory' => false,
                        'rgxp'      => 'url',
                        'decodeEntities'=>true,
                        'maxlength' => 2048,
                        'dcaPicker' => true,
                        'tl_class'  => 'w25'
                    ],
                ],
                'date' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['date'],
                    'inputType'     => 'text',
                    'search'        => true,
                    'eval'          => [
                        'rgxp'      =>  'datim',
                        'mandatory' =>  false,
                        'doNotCopy' => true,
                        'datepicker'=> true,
                        'tl_class'  => 'w16 wizard'
                    ]
                ],
            ],
            'eval' => [
                'tl_class' => 'clr',
                'actions' => [
                    'copy',
                    'delete',
                    'enable',
                ],
                'min' => 1, // minimum rows
                'max' => 5, // maximum rows
                'sortable' => false, // disable the sorting, defaults to true
            ],
            'sql' => [
                'type' => 'text',
                'length' => MySQLPlatform::LENGTH_LIMIT_BLOB,
                'notnull' => false
            ],
        ],
        /**********************************************************************
         * occurrences_legend
         **********************************************************************/
        'occurrences' => [
            #'search'    => true,
            #'filter'    => true,
            #'sorting' => true,
            'inputType' => 'text',
            'eval'      => [
                'tl_class' => '',
            ],
            'sql' => [
                'type'    => 'text',
                'notnull' => false,
                'default' => NULL,
            ]
        ],
	],
];