<?php
/**
 * Support Manager Admin Tickets controller
 *
 * @package blesta
 * @subpackage blesta.plugins.support_managerpro
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ClientTicketsCount extends SupportManagerAddonsController {

	public function index() {
	
		$this->requireLogin();
		Language::loadLang("support_manager_addons", null, PLUGINDIR . "support_manager_addons" . DS . "language" . DS);

		$this->client_id = $this->Session->read("blesta_client_id");

		$this->uses(array("SupportManager.SupportManagerTickets"));

		// Set the number of clients of each type
		$status_count = array(
			'open' => $this->SupportManagerTickets->getStatusCount("not_closed", null, $this->client_id)
		);			

		$this->set("status_count", $status_count);

	}
}
?>