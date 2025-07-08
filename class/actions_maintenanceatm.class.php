<?php
/* Copyright (C) 2024 SuperAdmin
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
 * \file    maintenanceatm/class/actions_maintenanceatm.class.php
 * \ingroup maintenanceatm
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

require_once __DIR__ . '/../backport/v19/core/class/commonhookactions.class.php';


/**
 * Class ActionsMaintenanceATM
 */
class ActionsMaintenanceATM extends maintenanceatm\RetroCompatCommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $this->results for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array			$parameters		Array of parameters
	 * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string			$action      	'add', 'update', 'view'
	 * @return	int         					Return integer <0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *                            				>0 if OK and we want to replace standard actions.
	 */
	public function getLoginPageOptions($parameters, &$object, &$action)
	{
		global $langs;


		$mainOnlyLoginAllowed = getDolGlobalString('MAIN_ONLY_LOGIN_ALLOWED');
		$newUrl = getDolGlobalString('MAINTENANCEATM_REDIRECT_NEW_URL');

		if(!empty($mainOnlyLoginAllowed)){
			$langs->load('maintenanceatm@maintenanceatm');

			$this->resprints = '
					<style>
						.maintenance-bloc{
							margin : 2em 0 ;
							border-top: 1px dashed #b0b0b0;
							border-bottom: 1px dashed #b0b0b0;
							background-color: #76cfff;
							padding: 2em;
						}
					</style>
				';

			// Ajout de la redirection JS
			if(filter_var($newUrl, FILTER_VALIDATE_URL)){

				$redirectSeconds = 9;
				$this->resprints.= '
					<div class="maintenance-bloc">
						<h1>'.$langs->trans('ModeMaintenanceActivatedNewUrlTitle').'</h1>
						<p>
							'.$langs->trans('ModeMaintenanceActivatedNewUrl', $newUrl).' : <a href="'.$newUrl.'">'.$newUrl.'</a><br/><br/>
							
							'.$langs->trans('ModeMaintenanceActivatedNewUrlDescPart1', $newUrl).'
							 <strong id="maint-redirect-timer">'.$redirectSeconds.'</strong> 
							'.$langs->trans('ModeMaintenanceActivatedNewUrlDescPart2').'
							 <button id="maint-redirect-timer-stop" title="'.dol_htmlentities($langs->trans('ModeMaintenanceStopTimer')).'">x</button> 
						</p>
					</div>
					<script>
						var seconds = '.$redirectSeconds.'; // seconds for HTML
						var maintRedirectClearInterval; // variable for clearInterval() function
						
						function redirect() {
							document.location.href = '.json_encode($newUrl).';
						}
				
						function updateSecs() {
							document.getElementById("maint-redirect-timer").innerHTML = seconds;
							seconds--;
							if (seconds == -1) {
								clearInterval(maintRedirectClearInterval);
								redirect();
							}
						}
				
						function countdownTimer() {
							maintRedirectClearInterval = setInterval(function () {
									updateSecs()
							}, 1000);
						}

						countdownTimer();
					
						
						document.getElementById("maint-redirect-timer-stop").addEventListener("click", function (e){
							e.preventDefault();
							clearInterval(maintRedirectClearInterval);
						});
						
						
					</script>
				';

			}else{
				$this->resprints.= '
					<div class="maintenance-bloc">
						<h1>'.$langs->trans('ModeMaintenanceActivatedLoginTitle').'</h1>
						<p>
							'.$langs->trans('ModeMaintenanceActivatedLoginDescPart1').'<br/>
							'.$langs->trans('ModeMaintenanceActivatedLoginDescPart2').'<br/>
							'.$langs->trans('ModeMaintenanceActivatedLoginDescPart3').'
						</p>
						<p>
							'.$langs->trans('ModeMaintenanceActivatedLoginDescPart4').'<br/>
							<span style="font-style: italic;" >'.$langs->trans('ModeMaintenanceActivatedLoginDescPart5').'</span>
						</p>
					</div>
				';
			}





			return 0;
		}
		$this->resprints = '';
		return 0;
	}

}
