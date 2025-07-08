<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    maintenanceatm/admin/setup.php
 * \ingroup maintenanceatm
 * \brief   MaintenanceATM setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/filefunc.inc.php";
require_once '../lib/maintenanceatm.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
//require_once "../class/myclass.class.php";

// Translations
$langs->loadLangs(array("admin", "maintenanceatm@maintenanceatm"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('maintenanceatmsetup', 'globalsetup'));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';


$error = 0;
$setupnotempty = 0;

// Set this to 1 to use the factory to manage constants. Warning, the generated module will be compatible with version v15+ only
$useFormSetup = 1;

if (!class_exists('FormSetup')) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formsetup.class.php';
}
$formSetup = new FormSetup($db);


// Enter here all parameters in your setup page
//
//// Setup conf for selection of an URL
$item = $formSetup->newItem('MAINTENANCEATM_REDIRECT_NEW_URL');
$item->cssClass = 'minwidth500';
//
//// Setup conf for selection of a simple string input
//$item = $formSetup->newItem('MAINTENANCEATM_MYPARAM2');
//$item->defaultFieldValue = 'default value';
//
//// Setup conf for selection of a simple textarea input but we replace the text of field title
//$item = $formSetup->newItem('MAINTENANCEATM_MYPARAM3');
//$item->nameText = $item->getNameText().' more html text ';
//
//// Setup conf for a selection of a thirdparty
//$item = $formSetup->newItem('MAINTENANCEATM_MYPARAM4');
//$item->setAsThirdpartyType();
//
//// Setup conf for a selection of a boolean
//$formSetup->newItem('MAINTENANCEATM_MYPARAM5')->setAsYesNo();
//
//// Setup conf for a selection of an email template of type thirdparty
//$formSetup->newItem('MAINTENANCEATM_MYPARAM6')->setAsEmailTemplate('thirdparty');
//
//// Setup conf for a selection of a secured key
////$formSetup->newItem('MAINTENANCEATM_MYPARAM7')->setAsSecureKey();
//
//// Setup conf for a selection of a product
//$formSetup->newItem('MAINTENANCEATM_MYPARAM8')->setAsProduct();
//
//// Add a title for a new section
//$formSetup->newItem('NewSection')->setAsTitle();
//
//$TField = array(
//	'test01' => $langs->trans('test01'),
//	'test02' => $langs->trans('test02'),
//	'test03' => $langs->trans('test03'),
//	'test04' => $langs->trans('test04'),
//	'test05' => $langs->trans('test05'),
//	'test06' => $langs->trans('test06'),
//);
//
//// Setup conf for a simple combo list
//$formSetup->newItem('MAINTENANCEATM_MYPARAM9')->setAsSelect($TField);
//
//// Setup conf for a multiselect combo list
//$item = $formSetup->newItem('MAINTENANCEATM_MYPARAM10');
//$item->setAsMultiSelect($TField);
//$item->helpText = $langs->transnoentities('MAINTENANCEATM_MYPARAM10');
//
//
//
//// Setup conf MAINTENANCEATM_MYPARAM10
//$item = $formSetup->newItem('MAINTENANCEATM_MYPARAM10');
//$item->setAsColor();
//$item->defaultFieldValue = '#FF0000';
//$item->nameText = $item->getNameText().' more html text ';
//$item->fieldInputOverride = '';
//$item->helpText = $langs->transnoentities('AnHelpMessage');
////$item->fieldValue = '';
////$item->fieldAttr = array() ; // fields attribute only for compatible fields like input text
////$item->fieldOverride = false; // set this var to override field output will override $fieldInputOverride and $fieldOutputOverride too
////$item->fieldInputOverride = false; // set this var to override field input
////$item->fieldOutputOverride = false; // set this var to override field output


$setupnotempty += count($formSetup->items);




/*
 * Actions
 */

if($action == 'setMaintenanceModeOn'){
	dolibarr_del_const($db, 'MAIN_ONLY_LOGIN_ALLOWED', 0);
	dolibarr_del_const($db, 'MAIN_ONLY_LOGIN_ALLOWED', $conf->entity);
	dolibarr_set_const($db, 'MAIN_ONLY_LOGIN_ALLOWED', $user->login, 'chaine', 1, 'Maintenance mode add by '.$user->login, 0);
}

if($action == 'setMaintenanceModeOff'){
	dolibarr_del_const($db, 'MAIN_ONLY_LOGIN_ALLOWED', 0);
	dolibarr_del_const($db, 'MAIN_ONLY_LOGIN_ALLOWED', $conf->entity);
}


include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';


/*
 * View
 */

$form = new Form($db);

$help_url = '';
$page_name = "MaintenanceATMSetup";

llxHeader('', $langs->trans($page_name), $help_url, '', 0, 0, array(), '', '', 'mod-maintenanceatm page-admin');

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = maintenanceatmAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, "maintenanceatm@maintenanceatm");

// Setup page goes here
echo '<span class="opacitymedium">'.$langs->trans("MaintenanceATMSetupPage").'</span><br><br>';

// Récupération de la date de mise en maintenance
$maintTime = 0;
$maintObj = $db->getRow('SELECT MAX(tms) tms FROM '.$db->prefix().'const WHERE name = \'MAIN_ONLY_LOGIN_ALLOWED\' ');
if($maintObj){
	$maintTime = $db->jdate($maintObj->tms);
}

$mainOnlyLoginAllowed = getDolGlobalString('MAIN_ONLY_LOGIN_ALLOWED');

print '<fieldset>';
print '<legend>'.$langs->trans('MaintenanceStatus') . ' ';
if(!empty($mainOnlyLoginAllowed)){
	print dolGetBadge($langs->trans('MaintenanceOn'), '', 'success');

	print dolGetButtonAction(
		$langs->trans('DisableMaintenanceMode'),
		'',
		'danger',
		dol_buildpath('maintenanceatm/admin/setup.php', 1).'?action=setMaintenanceModeOff',
		'',
		1,
		[
			'confirm' => [
				//'url' => 'http://', // Overide Url to go when user click on action btn, if empty default url is $url.?confirm=yes, for no js compatibility use $url for fallback confirm.
				'title' => '', // Overide title of modal,  if empty default title use "ConfirmBtnCommonTitle" lang key
				'action-btn-label' => $langs->trans('DisableMaintenanceMode'), // Overide label of action button,  if empty default label use "Confirm" lang key
				'cancel-btn-label' => '', // Overide label of cancel button,  if empty default label use "CloseDialog" lang key
				'content' => $langs->trans('ActivateMaintenanceModeOffConfirm'), // Overide text of content,  if empty default content use "ConfirmBtnCommonContent" lang key
			],
		]
	);
}else{
	print dolGetBadge($langs->trans('MaintenanceOff'), '', 'info');

	print dolGetButtonAction(
		$langs->trans('ActivateMaintenanceMode'),
		'',
		'danger',
		dol_buildpath('maintenanceatm/admin/setup.php', 1).'?action=setMaintenanceModeOn',
		'',
		1,
		[
			'confirm' => [
				//'url' => 'http://', // Overide Url to go when user click on action btn, if empty default url is $url.?confirm=yes, for no js compatibility use $url for fallback confirm.
				'title' => '', // Overide title of modal,  if empty default title use "ConfirmBtnCommonTitle" lang key
				'action-btn-label' => $langs->trans('ActivateMaintenanceMode'), // Overide label of action button,  if empty default label use "Confirm" lang key
				'cancel-btn-label' => '', // Overide label of cancel button,  if empty default label use "CloseDialog" lang key
				'content' => $langs->trans('ActivateMaintenanceModeConfirm', $user->login), // Overide text of content,  if empty default content use "ConfirmBtnCommonContent" lang key
			],
		]
	);
}


print '</legend>';

print '<div id="maintenance-check-table">';


// Security warning if install.lock file is missing or if conf file is writable
if (getDolGlobalString('MAIN_REMOVE_INSTALL_WARNING') || true) {

	// TODO : check for maint.lock (je me rappel plus du nom de fichier

	// Check if install lock file is present
	$lockfile = DOL_DATA_ROOT.'/install.lock';
	if (!empty($lockfile) && !file_exists($lockfile) && is_dir(DOL_DOCUMENT_ROOT."/install")) {
		$langs->load("errors");
		print info_admin($langs->trans("WarningLockFileDoesNotExists", DOL_DATA_ROOT).' '.$langs->trans("WarningUntilDirRemoved", DOL_DOCUMENT_ROOT."/install"), 0, 0, '1', 'clearboth');
	}

	// Conf files must be in read only mode
	if (is_writable(DOL_DOCUMENT_ROOT.'/'.$conffile)) {	// $conffile is defined into filefunc.inc.php
		$langs->load("errors");
		print info_admin($langs->transnoentities("WarningConfFileMustBeReadOnly").' '.$langs->trans("WarningUntilDirRemoved", DOL_DOCUMENT_ROOT."/install"), 0, 0, '1', 'clearboth');
	}
}

print '<table class="tagtable nobottomiftotal liste" >';

print '<tr class="oddeven" >';

print '<td>'.$langs->trans('LastBackupSql').' <a target="_blank" href="'.dol_buildpath('admin/tools/dolibarr_export.php',1).'" title="'.$langs->trans('NewBackupBdd').'" ><span class="fa fa-link"></span></a></td>';
print '<td>';

$fileArray = dol_dir_list($conf->admin->dir_output.'/backup', 'files', 0, '', '', "date", SORT_DESC, 1);
if(!empty($fileArray) && is_array($fileArray)){

	$fileArray = reset($fileArray);
	$class = $maintTime > (int) $fileArray['date'] ? 'warning' : 'success';
	if($maintTime < (int) $fileArray['date']){
		print dol_print_date($fileArray['date'], '%d/%m/%Y %H:%M:%S');
	}else{
		print dolGetBadge(dol_print_date($fileArray['date'], '%d/%m/%Y %H:%M:%S'), '' , 'warning');
	}

	if(0 >= (int) $fileArray['size']){
		print ' '.dolGetBadge(dol_print_size($fileArray['size'], 0, 0), '' , 'danger');
	}

	print ' '.$fileArray['name'];


}else{
	dolGetBadge('NoBackupFound', '', 'danger');
}

print '</td>';
print '</tr>';


if(empty($dolibarr_main_prod)){
	print '<tr class="oddeven" >';
	print '<td>'.$langs->trans('ModeMainProd').'</td>';
	print '<td>'.dolGetBadge($langs->trans('Disabled'), '', 'danger').'</td>';
	print '</tr>';
}


print '</table>';
print '</div>';

print '
	<script>
	 $(function() {
		 setInterval(function(){
			 $("#maintenance-check-table").load("'.dol_buildpath('maintenanceatm/admin/setup.php',1).' #maintenance-check-table");
		 }, 5000);
	});
	</script>
';


print '</fieldset>';


if ($action == 'edit') {
	print $formSetup->generateOutput(true);
	print '<br>';
} elseif (!empty($formSetup->items)) {
	print $formSetup->generateOutput();
	print '<div class="tabsAction">';
	print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&token='.newToken().'">'.$langs->trans("Modify").'</a>';
	print '</div>';
} else {
	print '<br>'.$langs->trans("NothingToSetup");
}



// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();
