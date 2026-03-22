<?php

use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;

use Contao\ArrayUtil;
use Contao\System;

use Symfony\Component\HttpFoundation\Request;

$currentRequest     = System::getContainer()->get('request_stack')->getCurrentRequest();
$scopeMatcher       = System::getContainer()->get('contao.routing.scope_matcher');
$isBackendRequest   = $scopeMatcher->isBackendRequest($currentRequest ?? Request::create(''));
$isFrontendRequest  = $scopeMatcher->isFrontendRequest($currentRequest ?? Request::create(''));

$assetsDir          = "bundles/ContaoSourcesBundle";

$moduleSources = [
    'sources' => [
        'sources_entities'    => [
            'tables'        => ['tl_source_entity'],
            'stylesheet'    => ["$assetsDir/scss/sources.css|static"],
            //'javascript'    => ["$assetsDir/js/resumable/resumable.js", "$assetsDir/js/SupervisorResumableWidget.js.twig"],

            // permission checks are always executed
            //'disablePermissionChecks' => false
            // module is always shown in the navigation.
        ],
    ],
];

#ArrayUtil::arrayInsert($GLOBALS['BE_MOD'],count($GLOBALS['BE_MOD']['system']), $moduleMaintenance);
ArrayUtil::arrayInsert($GLOBALS['BE_MOD'],0, $moduleSources);

// Front end modules
#$GLOBALS['FE_MOD']['pedigree'] = array
#(
#	'pedigree_module'   => SupervisorFrontendModuleController::class
#);

// Add permissions
$GLOBALS['TL_PERMISSIONS'][] = 'ped_conf';
$GLOBALS['TL_PERMISSIONS'][] = 'ped_tree';

// register backend widgets
//$GLOBALS['BE_FFL']['supervisorImageWidget']             = SupervisorImageWidget::class;
//$GLOBALS['BE_FFL']['supervisorTaskStackWidget']         = SupervisorTaskStackWidget::class;
//$GLOBALS['BE_FFL']['supervisorDomainStateWidget']       = SupervisorDomainStateWidget::class;
//$GLOBALS['BE_FFL']['supervisorFtpWidget']               = SupervisorFtpWidget::class;
//$GLOBALS['BE_FFL']['supervisorIntervalWidget']          = SupervisorIntervalWidget::class;
//$GLOBALS['BE_FFL']['supervisorForeignTableWidget']      = SupervisorForeignTableWidget::class;
//
// register model classes
$GLOBALS['TL_MODELS']['tl_sources_entity'] = SourcesEntityModel::class;
//// instances / Web-Instanzen
//$GLOBALS['TL_MODELS']['tl_supervisor_instance']         = SupervisorInstanceModel::class;
//// instances->tasks / aktive Tasks pro Instanz
//$GLOBALS['TL_MODELS']['tl_supervisor_instance_task']    = SupervisorInstanceTaskModel::class;
//// instance->tasks_log / alle bisher ausgeführten Tasks pro Instanz
//$GLOBALS['TL_MODELS']['tl_supervisor_instance_task_log']= SupervisorInstanceTaskLogModel::class;
//
//$GLOBALS['TL_MODELS']['tl_supervisor_task_prototype']   = SupervisorTaskPrototypeModel::class;
//$GLOBALS['TL_MODELS']['tl_supervisor_task_interval']    = SupervisorTaskIntervalModel::class;
//$GLOBALS['TL_MODELS']['tl_supervisor_instance_diary']   = SupervisorInstanceDiaryModel::class;
//
//$GLOBALS['TL_MODELS']['tl_supervisor_config']           = SupervisorConfigModel::class;

// Style sheet
if ($isBackendRequest) {
    #$GLOBALS['TL_CSS'][] = "$assetsDir/scss/supervisor.scss|static";
}