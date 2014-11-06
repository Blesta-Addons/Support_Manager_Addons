<?php
/**
 * Support Managerpro parent controller
 * 
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerAddonsController extends AppController {

	/**
	 * Setup
	 */
	public function preAction() {
		$this->structure->setDefaultView(APPDIR);
		parent::preAction();
		
		// Auto load language for the controller	
		Language::loadLang("global", null, PLUGINDIR . "support_manager" . DS . "language" . DS);
		
		// Override default view directory
		$this->view->view = "default";
		$this->orig_structure_view = $this->structure->view;
		$this->structure->view = "default";
	}
	
	/**
	 * Converts a past date to x days y mins format
	 *
	 * @param string $date_time The date time to convert
	 * @return string The date converted to time
	 */
	protected function timeSince($date_time) {
		$time = $this->Date->toTime(date("c")) - $this->Date->toTime($date_time);
		
		// Only deal with times in the past
		if ($time < 0)
		   return "";
		
		$day = 86400; // seconds in a day
		$hour = 3600; // seconds in an hour
		
		$days_since = floor($time/$day); // Number of days since
		$hours_since = ($time/$hour)%24; // Number of hours since
		$mins_since = ($time/60)%60; // Number of mins since
		
		// Set the time language
		$days_since_lang = ($days_since > 0 ? Language::_("Global.time_since.day", true, $days_since) : "");
		$hours_since_lang = ($hours_since > 0 ? Language::_("Global.time_since.hour", true, $hours_since) : "");
		$time_since = $days_since_lang . " " . $hours_since_lang . " ";
		
		// Include minutes if no other time unit is available, or if greater than 0
		if (empty($days_since_lang) && empty($hours_since_lang))
			$time_since .= Language::_("Global.time_since.minute", true, $mins_since);
		else
			$time_since .= ($mins_since > 0 ? Language::_("Global.time_since.minute", true, $mins_since) : "");
		
		return $time_since;
	}	
	
}
?>