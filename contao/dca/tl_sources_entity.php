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
            'mode'                  => DataContainer::MODE_SORTED,
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
            '{name_legend},name,base_sku;' .
            '{variant_legend},variants;' .
            '{image_legend},multiSRC;',
	],

	// Subpalettes
	'subpalettes' =>  [
        #'addImage'          => 'fullsize,size,floating,overwriteMeta',
        #'interval_daily'    => 'startTime',
        #'interval_weekly'   => 'startWeekday,startTime',
    ],

	// Fields
	'fields' => [
        /**********************************************************************
         * test_legend
         **********************************************************************/
        'id'        => ['sql' => "int(10) unsigned NOT NULL auto_increment"],
        'tstamp'    => ['sql' => "int(10) unsigned NOT NULL default 0",],
        'published' => ['toggle' => true,'inputType' => 'checkbox','sql' => ['type' => 'boolean', 'default' => false],],
        # requires special bundle oneup/contao-backend-sortable-list-views
        #'sorting'=> ['sql' => "int(10) unsigned NOT NULL default 0",],

        /**********************************************************************
         * name_legend
         **********************************************************************/
        'name' => [
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => false,
                'unique'    => true,
                'tl_class'  =>'w25'
            ],
            'sql'       => [
                'type'      => 'string',
                'length'    => 255,
                'fixed'     => true,
                'default'   => '',
            ]
        ],
        /**********************************************************************
         * source_page_legend
         **********************************************************************/
        'sourcePage' => [
            'inputType'     => 'pageTree',
            'foreignKey'    => 'tl_page.title',
            'eval'          => [
                'mandatory' => true,
                'fieldType' => 'radio'
            ],
            'relation'      => ['type'=>'belongsTo', 'load'=>'lazy'],
            'sql'           => [
                'type'      => 'integer',
                'unsigned'  => true,
                'notnull'   => true,
                'default'   => 0,
            ],
        ],
        /**********************************************************************
         * actions_legend
         **********************************************************************/
        'actions' => [
        ],
        /**********************************************************************
         * code_legend
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
	],
];