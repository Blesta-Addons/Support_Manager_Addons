<?php
/**
 * Support Manager Addons Plugin
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerAddonsPlugin extends Plugin {
	
	public function __construct() {
		Language::loadLang("support_manager_addons", null, dirname(__FILE__) . DS . "language" . DS);
		
		// Load components required by this plugin
		Loader::loadComponents($this, array("Input", "Record"));
		
		$this->loadConfig(dirname(__FILE__) . DS . "config.json");
	}
	
	/**
	 * Performs any necessary bootstraping actions
	 *
	 * @param int $plugin_id The ID of the plugin being installed
	 */
	public function install($plugin_id) {
		
		// Add all support tables, *IFF* not already added
		try {
		
			if (!isset($this->PluginManager))
				Loader::loadModels($this, array("PluginManager"));
				
			// Get the company ID
			$company_id = Configure::get("Blesta.company_id");
			// Check if Cpanel is installed if not installation fail.
			if (!$this->PluginManager->isInstalled("support_manager",$company_id)) {
				$this->Input->setErrors(array(
					'support_manager' => array(
						'invalid' => Language::_("SupportManagerAddonsPlugin.!error.install", true)
					)
				));
				return;
			}
		
		}
		catch (Exception $e) {
			// Error adding... no permission?
			$this->Input->setErrors(array('db'=> array('create'=>$e->getMessage())));
			return;
		}

	}
	
	/**
	 * Performs migration of data from $current_version (the current installed version)
	 * to the given file set version
	 *
	 * @param string $current_version The current installed version of this plugin
	 * @param int $plugin_id The ID of the plugin being upgraded
	 */
	public function upgrade($current_version, $plugin_id) {		
		// Upgrade if possible
		if (version_compare($this->getVersion(), $current_version, ">")) {
		}
	}
	
	/**
	 * Performs any necessary cleanup actions
	 *
	 * @param int $plugin_id The ID of the plugin being uninstalled
	 * @param boolean $last_instance True if $plugin_id is the last instance across all companies for this plugin, false otherwise
	 */
	public function uninstall($plugin_id, $last_instance) {

	}
	
	/**
	 * Returns all actions to be configured for this widget (invoked after install() or upgrade(), overwrites all existing actions)
	 *
	 * @return array A numerically indexed array containing:
	 * 	- action The action to register for
	 * 	- uri The URI to be invoked for the given action
	 * 	- name The name to represent the action (can be language definition)
	 * 	- options An array of key/value pair options for the given action
	 */
	public function getActions() {	
		// Widget
		return array(
			array(
				'action' => "widget_client_home",
				'uri' => "plugin/support_manager_addons/client_widget/index/",
				'name' => Language::_("SupportManagerAddonsPlugin.widget_client_home.main", true)
			)			
		);
	}
	
	/**
	 * Returns all events to be registered for this plugin (invoked after install() or upgrade(), overwrites all existing events)
	 *
	 * @return array A numerically indexed array containing:
	 * 	- event The event to register for
	 * 	- callback A string or array representing a callback function or class/method. If a user (e.g. non-native PHP) function or class/method, the plugin must automatically define it when the plugin is loaded. To invoke an instance methods pass "this" instead of the class name as the 1st callback element.
	 */	
	public function getEvents() {
		return array(
            array(
                'event' => "Appcontroller.structure",
                'callback' => array("this", "SupportBadgeCount")
            )
		);
	}

	
	
	/**
	 * On Appcontroller.structure run this
	 */
    public function SupportBadgeCount($event) {
        // Fetch current return val
        $result = $event->getReturnVal();

        $params = $event->getParams();

        // Set return val if not set
        if (!isset($result['body_end']))
                $result['body_end'] = null;

        // Set return val if not set
        if (!isset($result['head']))
                $result['head'] = null;

            $result['head']["supportmanageraddons"] = '
				<style type="text/css">
				<!--
				.spro_badge {
					top: -8px;
					font-size: 10px;
					font-weight: 700;
					float: none !important; position: relative;
					padding: 2px 5px 3px 5px;color: #fff;
					background-image: linear-gradient(#fa3c45, #dc0d17);
					background-image: -webkit-gradient(linear, center top, center bottom, from(#fa3c45), to(#dc0d17));
					background-image: -webkit-linear-gradient(#fa3c45, #dc0d17);
					-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .7);
					box-shadow: 0px 1px 1px rgba(0,0,0,0.7);
					text-shadow: 0px -1px 0px rgba(0,0,0,0.4);
					-webkit-border-radius: 10px;
					-moz-border-radius: 10px;border-radius: 10px;
				}
				-->
				</style>
			';

        // Update return val -- ONLY set if admin portal
        if ($params['portal'] == "admin")
            $result['body_end']["supportmanageraddons"] = "
				<!-- display admin ticket count menu badge-->
				<script>
				jQuery(function($){
				if( $(\"a[href='".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager/admin_main/']\").length )
				{
				  $( document ).ready(function() {
					$.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager_addons/admin_tickets_count/', function(newRowCount){
					  $(\"a[href='".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager/admin_main/']\").html( newRowCount.trim() );
					});
				  });
				  setInterval(function(){
					$.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager_addons/admin_tickets_count/', function(newRowCount){
					  $(\"a[href='".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager/admin_main/']\").html( newRowCount.trim() );
					});
				  // },25000);
				}
				});
				</script>
				<!-- end display admin ticket count menu badge-->
			";

        // Update return val -- ONLY set if client portal
        if ($params['portal'] == "client")
            $result['body_end']["supportmanageraddons"] = "
				<!-- display admin ticket count menu badge-->
				<script>
				jQuery(function($){
				if( $(\"a[href='".WEBDIR.Configure::get("Route.client")."/plugin/support_manager/client_main/']\").length )
				{
				  $( document ).ready(function() {
					$.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager_addons/client_tickets_count/', function(newRowCount){
					  $(\"a[href='".WEBDIR.Configure::get("Route.client")."/plugin/support_manager/client_main/']\").html( newRowCount.trim() );
					});
				  });
				  setInterval(function(){
					$.get( '".WEBDIR.Configure::get("Route.admin")."/plugin/support_manager_addons/client_tickets_count/', function(newRowCount){
					  $(\"a[href='".WEBDIR.Configure::get("Route.client")."/plugin/support_manager/client_main/']\").html( newRowCount.trim() );
					});
				  },35000);
				}
				});
				</script>
				<!-- end display admin ticket count menu badge-->
			";


        // Update return val
        $event->setReturnVal($result);
    }
}
?>