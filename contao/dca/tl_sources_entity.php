<?php

use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

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
		'default'       =>
            '{type_legend},type;' .
            '{author_legend},authors;' .
            '{title_legend},title;' .
            '{data_legend},signature,signature_alt;' .
            '{online_legend},link_catalog,addDigitalCopy;' .
            ''
        ,
	],

	// Subpalettes
	'subpalettes' =>  [
        'addDigitalCopy'    => 'link_digitalcopy,extent_digitalcopy',
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
            'foreignKey'=> 'tl_sources_type.type_sgl',
            'relation'      => ['type'=>'belongsTo', 'load'=>'lazy'],
            //'reference' => &$GLOBALS['TL_LANG']['CTE'],
            'eval'      => [
                'helpwizard'    =>true,
                'chosen'        =>true,
                'submitOnChange'=>true,
                'tl_class'      =>'w25'
            ],
            'sql'           => [
                'type'      => 'integer',
                'unsigned'  => true,
                'notnull'   => true,
                'default'   => 0,
            ],
        ],
        /**********************************************************************
         * author_legend
         **********************************************************************/
        'authors' => [
            'search'    => true,
            'filter'    => true,
            #'sorting' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_sources_author.family_name',
            'relation'  => [
                'type'  => 'hasMany',
                'load'  => 'lazy'
            ],
            'eval'      => [
                'includeBlankOption' => false,
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
         * data_legend
         **********************************************************************/
        'html' => [
            'inputType'     => 'textarea',
            'eval'          => [
                'mandatory' => false,
                'unique'    => false,
                'preserveTags'  => true,
                'class'     => 'monospace',
                'rte'       => 'ace|html',
                'tl_class'  => 'w50',
            ],
            'sql'       => [
                'type'      => 'text',
                'fixed'     => true,
                'length'    => AbstractMySQLPlatform::LENGTH_LIMIT_MEDIUMTEXT,
                'notnull'   => false
            ],
        ],
        'iframe' => [
            'inputType'     => 'textarea',
            'eval'          => [
            #    'mandatory' => false,
            #    'unique'    => false,
            #    'preserveTags'  => true,
            #    'class'     => 'monospace',
            #    'rte'       => 'ace|html',
                'tl_class'  => 'w50',
            ],
            'sql'       => [
                'type'      => 'blob',
                #'length'    => 16384,
                #'notnull'   => false,
                #'type'      => 'string',
                #'length'    => 10,
                #'fixed'     => true,
                'length'    => AbstractMySQLPlatform::LENGTH_LIMIT_BLOB,
                'notnull'   => false
            ],
        ],
        /*
         * zeigt an, ob diese Seite die aktive Wartungsseite ist
         * unique!
         */
        'active' => [
            'toggle'    => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'unique' => true,
            ],
            'sql' => [
                'type' => 'boolean',
                'default' => false
            ],
        ],
        /**********************************************************************
         * online_legend
         **********************************************************************/
	],
];