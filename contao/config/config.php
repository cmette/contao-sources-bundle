<?php

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;

use Contao\ArrayUtil;
use Contao\System;

use Symfony\Component\HttpFoundation\Request;

$currentRequest     = System::getContainer()->get('request_stack')->getCurrentRequest();
$scopeMatcher       = System::getContainer()->get('contao.routing.scope_matcher');
$isBackendRequest   = $scopeMatcher->isBackendRequest($currentRequest ?? Request::create(''));
$isFrontendRequest  = $scopeMatcher->isFrontendRequest($currentRequest ?? Request::create(''));

$assetsDir          = "bundles/contaosources";

$moduleSources = [
    'sources' => [
        'sources_entities'    => [
            'tables'        => ['tl_sources_entity'],
            'stylesheet'    => ["$assetsDir/scss/sources.css"],
            //'javascript'    => ["$assetsDir/js/resumable/resumable.js", "$assetsDir/js/SupervisorResumableWidget.js.twig"],

            // permission checks are always executed
            //'disablePermissionChecks' => false
            // module is always shown in the navigation.
        ],
        'sources_authors' => [
            'tables'        => ['tl_sources_author'],
            //'stylesheet'    => ["$assetsDir/scss/sources.css|static"],
            //'javascript'    => ["$assetsDir/js/resumable/resumable.js", "$assetsDir/js/SupervisorResumableWidget.js.twig"],
        ],
        'sources_series' => [
            'tables'        => ['tl_sources_serie'],
            //'stylesheet'    => ["$assetsDir/scss/sources.css|static"],
            //'javascript'    => ["$assetsDir/js/resumable/resumable.js", "$assetsDir/js/SupervisorResumableWidget.js.twig"],
        ],
        'sources_publishers' => [
            'tables'        => ['tl_sources_publisher'],
            //'stylesheet'    => ["$assetsDir/scss/sources.css|static"],
            //'javascript'    => ["$assetsDir/js/resumable/resumable.js", "$assetsDir/js/SupervisorResumableWidget.js.twig"],
        ],
        #'sources_types' => [
        #    'tables'        => ['tl_sources_type'],
        #    'stylesheet'    => ["$assetsDir/scss/sources.css|static"],
        #    'javascript'    => ["$assetsDir/js/resumable/resumable.js", "$assetsDir/js/SupervisorResumableWidget.js.twig"],
        #],
    ],
];

ArrayUtil::arrayInsert($GLOBALS['BE_MOD'],0, $moduleSources);

// Front end modules
#$GLOBALS['FE_MOD']['sources'] = array
#(
#	'pedigree_module'   => SupervisorFrontendModuleController::class
#);

// Add permissions
$GLOBALS['TL_PERMISSIONS'][] = 'ped_conf';
$GLOBALS['TL_PERMISSIONS'][] = 'ped_tree';

// register backend widgets
//$GLOBALS['BE_FFL']['supervisorImageWidget']             = SupervisorImageWidget::class;

// register model classes
$GLOBALS['TL_MODELS']['tl_sources_entity']  = SourcesEntityModel::class;
#$GLOBALS['TL_MODELS']['tl_sources_type']    = SourcesTypeModel::class;
$GLOBALS['TL_MODELS']['tl_sources_author']  = SourcesAuthorModel::class;

// Style sheet
if ($isBackendRequest) {
    #$GLOBALS['TL_CSS'][] = "$assetsDir/scss/supervisor.scss|static";
}