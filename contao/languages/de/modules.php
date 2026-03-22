<?php

declare(strict_types=1);

/*
 * Backend modules
 */

//use Cmette\ContaoPedigreeBundle\Controller\FrontendModule\PedigreeFrontendModuleController;

$GLOBALS['TL_LANG']['MOD']['sources']           = 'Quellenregister';

$GLOBALS['TL_LANG']['MOD']['sources_entities']  = ['Quellen', 'Register alle Quellen'];
$GLOBALS['TL_LANG']['MOD']['sources_types']     = ['Quellen-Typen', 'Typen von Quellen, wie Monografie, Aufsatz, Landkarte etc.'];
$GLOBALS['TL_LANG']['MOD']['sources_authors']   = ['Autoren', 'Autorenregister'];

/*
 * Frontend Modules
 */
$GLOBALS['TL_LANG']['FMD']['sources'] = [ 'Quellenverwaltung', ''];
//$GLOBALS['TL_LANG']['FMD'][PedigreeFrontendModuleController::TYPE] = ['Stammbaum', 'Stammbaum-Visualisierung'];
