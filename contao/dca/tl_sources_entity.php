<?php

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Cmette\ContaoSourcesBundle\Models\SourcesLibraryModel;
use Cmette\ContaoSourcesBundle\Utils\DcaUtils;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

use Doctrine\DBAL\Platforms\MySQLPlatform;

$strTable = 'tl_sources_entity';

#System::loadLanguageFile($strTable);

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
        #'operations' =>  [
        #    'edit',
        #    'copy',
        #    'new',
        #    '!toggle',
        #    '!delete',
        #],
	],

	// Palettes
	'palettes' =>  [
		'__selector__'  =>  ['type','addTitleAlias','addSeries','addCatalogs','addDigitalCopies','addImage'],
        'default' =>
            '{type_legend},type;' .
            '{author_legend},authors_hint,authors,addTitleAlias;' .
            '{title_legend},title,subtitle,titleAlias;' .
            '{series_legend},addSeries;' .
            '{publisher_legend},publisher,edition,year,firstyear;' .
            '{catalogs_legend},addCatalogs;' .
            '{digitalcopies_legend},addDigitalCopies;' .
            '{image_legend},addImage;' .
            #'{occurrences_legend},occurrences;' .
            ''
        ,
        'essay' =>
            '{type_legend},type;' .
            '{author_legend},authors_hint,authors,addTitleAlias;' .
            '{title_legend},title,subtitle;' .
            '{series_legend},addSeries;' .
            '{publisher_legend},publisher,edition,year;' .
            '{catalogs_legend},addCatalogs;' .
            '{digitalcopies_legend},addDigitalCopies;' .
            '{image_legend},addImage;' .
            #'{occurrences_legend},occurrences;' .
            ''
        ,
        'handwriting' =>
            '{type_legend},type;' .
            '{author_legend},authors_hint,authors;' .
            '{title_legend},title,subtitle,addTitleAlias;' .
            '{publisher_legend},publisher,edition,year;' .
            '{catalogs_legend},addCatalogs;' .
            '{digitalcopies_legend},addDigitalCopies;' .
            '{image_legend},addImage;' .
            #'{occurrences_legend},occurrences;' .
            ''
        ,
        'map' =>
            '{type_legend},type;' .
            '{author_legend},authors_hint,authors,addTitleAlias;' .
            '{title_legend},title,subtitle;' .
            '{series_legend},addSeries;' .
            '{publisher_legend},publisher,edition,year,firstyear;' .
            '{catalogs_legend},addCatalogs;' .
            '{digitalcopies_legend},addDigitalCopies;' .
            '{image_legend},addImage;' .
            #'{occurrences_legend},occurrences;' .
            ''
        ,
        'series' =>
            '{type_legend},type;' .
            '{author_legend},authors_hint,authors,addTitleAlias;' .
            '{title_legend},title,subtitle;' .
            '{series_legend},addSeries;' .
            '{publisher_legend},publisher,edition,year;' .
            '{catalogs_legend},addCatalogs;' .
            '{digitalcopies_legend},addDigitalCopies;' .
            '{image_legend},addImage;' .
            #'{occurrences_legend},occurrences;' .
            ''
        ,
	],

	// Subpalettes
	'subpalettes' =>  [
        'addTitleAlias'     => 'titleAlias',
        'addSeries'         => 'series,volume_title,volume,issue,pages',
        'addCatalogs'       => 'catalogs',
        'addDigitalCopies'  => 'digitalcopies',
        'addImage'          => 'singleSRC,size,floating,fullsize,overwriteMetaFromSource',
    ],

	// Fields
	'fields' => [
        /**********************************************************************
         * without legend
         **********************************************************************/

        'id'        => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'tstamp'    => ['sql' => "int(10) unsigned NOT NULL default 0",],
        'published' => DcaUtils::buildPublishedField(),

        /**********************************************************************
         * type_legend
         **********************************************************************/
        'type' => [
            'inputType' => 'select',
            'flag'      => DataContainer::SORT_ASC,
            'sorting'   => true,
            'filter'    => true,
            #'options'   => SourcesEntityModel::SOURCE_TYPES,
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
        # ein experimentelles Hinweisfeld
        'authors_hint' => [
            'eval' => [
                'tl_class' => 'w50',
                'hideHead' => true
            ],
        ],
        # list of authors
        'authors' => [
            'inputType' => 'rowWizard',
            'fields' => [
                'author' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['authors_fields']['author'],
                    'inputType' => 'select',
                    'options'   => SourcesAuthorModel::getAllUniqueAuthors(false),
                    'relation'  => [
                        'type'  => 'hasMany',
                        'load'  => 'lazy'
                    ],
                    'eval'      => [
                        'includeBlankOption'=> true,
                        'blankOptionLabel'  => 'unbekannt',
                        'tl_class' => 'w75',
                        'multiple' => false,
                        'chosen' => true,
                    ],
                ],
                'role' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['authors_fields']['role'],
                    'search'    => true,
                    'inputType' => 'select',
                    'options'   => SourcesAuthorModel::ROLES,
                    'reference' => &$GLOBALS['TL_LANG'][$strTable]['authors_fields']['role_options'],
                    'eval'      => [
                        'mandatory' => false,
                        'includeBlankOption'=> true,
                        'tl_class' => 'w25'
                    ],
                ],
            ],
            'eval' => [
                'tl_class' => 'clr',
                'actions' => [
                    'copy',
                    'delete',
                    'enable',
                ],
                'min' => 1,         // minimum rows
                'max' => 20,        // maximum rows
                'sortable' => true, // disable the sorting, defaults to true
            ],
            'sql' => [
                'type' => 'text',
                'length' => MySQLPlatform::LENGTH_LIMIT_BLOB,
                'notnull' => false
            ],
        ],
        # bei fehlenden Autoren einen Kurztitel an Stelle des Autors verwenden
        'addTitleAlias' => DcaUtils::buildAddField(),
        # ein Ersatztitel, wenn der Haupttitel zu lang ist
        'titleAlias' => [
            'inputType'     => 'text',
            'eval'          => [
                'tl_class'  =>'w50'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * title_legend
         **********************************************************************/

        # der Haupttitel
        'title' => [
            'inputType'     => 'text',
            'search'    => true,
            'filter'    => false,
            'sorting'   => true,
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
        # ein Untertitel
        'subtitle' => [
            'inputType'     => 'text',
            'eval'          => [
                //'tl_class'  =>'w25'
            ],
            'sql'       => [
                'type'      => 'text',
                'length'    => 1024,
                //'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * periodika_legend
         **********************************************************************/

        # Reihenangaben hinzufügen
        'addSeries' => DcaUtils::buildAddField(),

        # allg. Periodikum aus dem Reihen-Register
        'series' => [
            'inputType' => 'select',
            'search'    => true,
            'filter'    => false,
            'sorting'   => true,
            'foreignKey' => 'tl_sources_serie.title',
            'relation'  => [
                'type'  => 'hasOne',
                'load'  => 'lazy'
            ],
            'eval'      => [
                // wenn addSeries true, dann muss eine Reihe angegeben werden!
                'mandatory' => true,
                'includeBlankOption'=> false,
                #'blankOptionLabel'  => 'kein/unbekannt',
                'tl_class' => 'w25',
                'multiple' => false,
                'chosen' => true
            ],
            'sql' => [
                'type'      => 'integer',
                'unsigned'  => true,
                'notnull'   => true,
                'default'   => 0,
            ]
        ],
        # Band-Titel, z.b. bei Lexika die Angabe "M bis P" oder "Maulbeere bis Pankow"
        'volume_title' => [
            'inputType' => 'text',
            'search'    => true,
            'filter'    => false,
            'sorting'   => true,
            'eval'      => [
                'mandatory' => false,
                'unique'    => false,
                'tl_class'  =>'w25'
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
            'inputType' => 'text',
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => [
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
        # Seiten - nur bei Aufsätzen
        'pages' => [
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
        /**********************************************************************
         * publisher_legend
         **********************************************************************/
        # Herausgeber
        'editor' => [

        ],
        # Verlag
        'publisher' => [
            'inputType' => 'select',
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'foreignKey' => 'tl_sources_publisher.name',
            'relation'  => [
                'type'  => 'hasMany',
                'load'  => 'lazy'
            ],
            'eval'      => [
                'mandatory' => false,
                'includeBlankOption' => true,
                'tl_class' => 'w50',
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
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => [
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
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => [
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
        # Jahr der Erstausgabe
        'firstyear' => [
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => [
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
         * catalogs_legend
         **********************************************************************/

        # switch catalogs
        'addCatalogs'   => DcaUtils::buildAddField(),

        # list of links to catalogs
        'catalogs' => [
            'inputType' => 'rowWizard',
            'search'    => true,
            'filter'    => false,
            'sorting'   => true,
            'fields' => [
                'provider' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['provider'],
                    'inputType' => 'select',
                    'options'   => SourcesLibraryModel::getLibrariesOptions(true, false),
                    #'foreignKey'=> 'tl_sources_library.name',
                    #'relation'      => ['type'=>'belongsTo', 'load'=>'lazy'],
                    #'reference' => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['provider_options'],
                    'eval'          => [
                        'mandatory' => true,
                        'tl_class'  => 'w25'
                    ],
                ],
                'signature' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['signature'],
                    'inputType' => 'text',
                    'eval'      => [
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
                'url' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['url'],
                    'inputType'     => 'text',
                    'search'        => true,
                    'eval'          => [
                        'mandatory' => false,
                        'rgxp'      => 'url',
                        'decodeEntities'=>true,
                        'maxlength' => 2048,
                    ],
                ],
                'date' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['catalog_fields']['date'],
                    'inputType'     => 'text',
                    'search'        => true,
                    'eval'          => [
                        'rgxp'      =>  'date',
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
                'min'       => 0, // minimum rows
                'max'       => 6, // maximum rows
                'sortable'  => true, // disable the sorting, defaults to true
            ],
            'sql' => [
                'type' => 'text',
                'length' => MySQLPlatform::LENGTH_LIMIT_BLOB,
                'notnull' => false
            ],
        ],
        /**********************************************************************
         * dataprovider_legend
         **********************************************************************/

        # switch digitalcopies
        'addDigitalCopies' => DcaUtils::buildAddField(),

        # list of digitalcopies / Digitalisate
        'digitalcopies' => [
            'inputType' => 'rowWizard',
            'search'    => true,
            'filter'    => false,
            'sorting'   => true,
            'fields'    => [
                'provider' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['digitalcopies_fields']['provider'],
                    'inputType' => 'select',
                    'options'   => SourcesLibraryModel::getLibrariesOptions(false, true),
                    'eval'          => [
                        'includeBlankOption' => true,
                        'mandatory' => true,
                        'tl_class'  => 'w25'
                    ],
                ],
                'url' => [
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['digitalcopies_fields']['url'],
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
                    'label'     => &$GLOBALS['TL_LANG'][$strTable]['digitalcopies_fields']['date'],
                    'inputType'     => 'text',
                    'search'        => true,
                    'eval'          => [
                        'rgxp'      =>  'date',
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
                'min'       => 0, // minimum rows
                'max'       => 6, // maximum rows
                'sortable'  => true, // disable the sorting, defaults to true
            ],
            'sql' => [
                'type' => 'text',
                'length' => MySQLPlatform::LENGTH_LIMIT_BLOB,
                'notnull' => false
            ],
        ],
        /**********************************************************************
         * image_legend
         **********************************************************************/
        // hat ein Bild
        'addImage' => DCAUtils::buildAddField(),

        'singleSRC' => [
            'inputType' => 'fileTree',
            'eval'      => [
                'filesOnly' => true,
                'fieldType' =>'radio',
                'mandatory' => true,
                'tl_class'  => 'w16'
            ],
            'sql'   => "binary(16) NULL"
        ],
        'size' => [
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => [
                'rgxp' => 'natural',
                'includeBlankOption' => true,
                'nospace' => true,
                'tl_class' => 'w50 clr',
            ],
            'options_callback' => ['contao.listener.image_size_options', '__invoke'],
            'sql' => [
                'type' => 'string',
                'length' => 128,
                'default' => '',
                'platformOptions' => ['collation' => 'ascii_bin'],
            ],
        ],
        'floating' => [
            'inputType' => 'radioTable',
            'options'   => ['above', 'left', 'right', 'below'],
            'eval'      => [
                'cols' => 4,
                'tl_class' => 'w50'
            ],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'sql'       => "varchar(32) COLLATE ascii_bin NOT NULL default 'above'"
        ],
        # überschreibt die Metadaten mit den Daten der Quelle ToDo: noch nicht implementiert
        'overwriteMetaFromSource' => [
            'inputType' => 'checkbox',
            'search'    => false,
            'filter'    => true,
            'sorting'   => false,
            'eval'      => [
                'submitOnChange' => false,
                'tl_class' => 'w50'
            ],
            'sql'   => [
                'type' => 'boolean',
                'default' => false
            ]
        ],
        'fullsize' => [
            'inputType'     => 'checkbox',
            'eval'          => [
                'tl_class'  => 'w50'
            ],
            'sql'   => [
                'type'  => 'boolean',
                'default' => false
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