<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['sources_entity'] =
    '{type_legend},type,headline,title;' .
    '{source_legend},sources_entity;' .
    # '{image_legend},addImage;' .
    '{template_legend:hide},customTpl;' .
    '{protected_legend:hide},protected;' .
    '{expert_legend:hide},cssID;' .
    '{invisible_legend:hide},invisible,start,stop;'
;

$GLOBALS['TL_DCA']['tl_content']['fields']['sources_entity'] =
[
    'inputType' => 'select',
    'search'    => true,
    'filter'    => true,
    'sorting' => true,
    #'foreignKey' => 'tl_sources_entity.title',
    #'relation'  => ['type'  => 'hasOne','load'  => 'lazy'],
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
        'default'   => 0,
    ]
];

#PaletteManipulator::create()
#    ->addLegend('source_legend', 'type_legend', PaletteManipulator::POSITION_AFTER)
#    ->addField('sources_entity', 'source_legend', PaletteManipulator::POSITION_APPEND)
#    ->applyToPalette('default', 'tl_content')
#;