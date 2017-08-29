<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement PayZen
#						Version : 1.2a (révision 25942)
#									########################
#					Développé pour TomatoCart
#						Version : 1.1.3
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						01/06/2011
#						Contact : support@payzen.eu
#
#####################################################################################################

/**
 * The administration side of the vads payment module
 */
class osC_Payment_vads extends osC_Payment_Admin {

	/**
	 * The administrative title of the payment module
	 *
	 * @var string
	 * @access private
	 */
	var $_title;

	/**
	 * The code of the payment module
	 *
	 * @var string
	 * @access private
	 */
	var $_code = 'vads';

	/**
	 * The developers name
	 *
	 * @var string
	 * @access private
	 */
	var $_author_name = 'Lyra network';

	/**
	 * The developers address
	 *
	 * @var string
	 * @access private
	 */

	var $_author_www = 'http://www.lyra-network.com';

	/**
	 * The status of the module
	 *
	 * @var boolean
	 * @access private
	 */
	var $_status = false;

	/**
	 * Constructor
	 */
	function osC_Payment_vads() {
		global $osC_Language;

		$this->_title = $osC_Language->get('payment_vads_title');
		$this->_description = $osC_Language->get('payment_vads_description');
		$this->_method_title = $osC_Language->get('payment_vads_method_title');
		$this->_status = defined('MODULE_PAYMENT_VADS_STATUS')
				&& (MODULE_PAYMENT_VADS_STATUS == '1');
		$this->_sort_order = defined('MODULE_PAYMENT_VADS_SORT_ORDER')
				? MODULE_PAYMENT_VADS_SORT_ORDER
				: null;
	}

	/**
	 * Returns an array representing config parameters. Avoid duplicated code/data in install and getKeys...
	 * config_key => (default_value, use_function, set_function)
	 * @return array
	 */
	function _getConfig() {
		return array(
				'MODULE_PAYMENT_VADS_STATUS' => array(
						-1,
						null,
						"osc_cfg_set_boolean_value(array(1, -1))"),
				'MODULE_PAYMENT_VADS_SORT_ORDER' => array(0, null, null),
				'MODULE_PAYMENT_VADS_ZONE' => array(
						0,
						'osc_cfg_use_get_zone_class_title',
						'osc_cfg_set_zone_classes_pull_down_menu'),
				'MODULE_PAYMENT_VADS_SITE_ID' => array("12345678", null, null),
				'MODULE_PAYMENT_VADS_KEY_TEST' => array("1111111111111111", null, null),
				'MODULE_PAYMENT_VADS_KEY_PROD' => array("2222222222222222", null, null),
				'MODULE_PAYMENT_VADS_CTX_MODE' => array(
						"TEST",
						null,
						"osc_cfg_set_boolean_value(array(\'TEST\', \'PRODUCTION\'))"),
				'MODULE_PAYMENT_VADS_PLATFORM_URL' => array("https://secure.payzen.eu/vads-payment/", null, null),
				'MODULE_PAYMENT_VADS_CAPTURE_DELAY' => array("", null, null),
				'MODULE_PAYMENT_VADS_VALIDATION_MODE' => array(
						"",
						null,
						"osc_cfg_set_boolean_value(array(\'\', \'0\', \'1\'))"),
				'MODULE_PAYMENT_VADS_PAYMENT_CARDS' => array("", null, null),
				'MODULE_PAYMENT_VADS_LANGUAGE' => array("fr", null, null),
				'MODULE_PAYMENT_VADS_AVAILABLE_LANGUAGES' => array("", null, null),
				'MODULE_PAYMENT_VADS_SHOP_NAME' => array(STORE_NAME, null, null),
				'MODULE_PAYMENT_VADS_SHOP_URL' => array(
						osc_href_link(FILENAME_DEFAULT, null, 'NONSSL', false, true, true),
						null,
						null),
				'MODULE_PAYMENT_VADS_REDIRECT_ENABLED' => array(
						'false',
						null,
						"osc_cfg_set_boolean_value(array(\'true\', \'false\'))"),
				'MODULE_PAYMENT_VADS_REDIRECT_SUCCESS_TIMEOUT' => array("5", null, null),
				'MODULE_PAYMENT_VADS_REDIRECT_SUCCESS_MESSAGE' => array(
						"Redirection vers la boutique dans quelques instants",
						null,
						null),
				'MODULE_PAYMENT_VADS_REDIRECT_ERROR_TIMEOUT' => array("5", null, null),
				'MODULE_PAYMENT_VADS_REDIRECT_ERROR_MESSAGE' => array(
						"Redirection vers la boutique dans quelques instants",
						null,
						null),
				'MODULE_PAYMENT_VADS_RETURN_MODE' => array(
						"GET",
						null,
						"osc_cfg_set_boolean_value(array(\'POST\', \'GET\', \'NONE\'))"),
				'MODULE_PAYMENT_VADS_RETURN_GET_PARAMS' => array("", null, null),
				'MODULE_PAYMENT_VADS_RETURN_POST_PARAMS' => array("", null, null),
				'MODULE_PAYMENT_VADS_URL_RETURN' => array(
						str_ireplace('&amp;', '&',
								osc_href_link(FILENAME_CHECKOUT, 'process&module=vads', 'SSL',
										null, null, true)),
						null,
						null),
				'MODULE_PAYMENT_VADS_URL_SUCCESS' => array(
						str_ireplace('&amp;', '&',
								osc_href_link(FILENAME_CHECKOUT, 'process&module=vads', 'SSL',
										null, null, true)),
						null,
						null),
				'MODULE_PAYMENT_VADS_ORDER_STATUS_ID' => array(
						ORDERS_STATUS_PAID,
						'osc_cfg_set_order_statuses_pull_down_menu',
						'osc_cfg_use_get_order_status_title'),
				'MODULE_PAYMENT_VADS_URL_CHECK' => array(
						str_ireplace('&amp;', '&',
								osc_href_link(FILENAME_CHECKOUT, 'callback&module=vads', 'SSL',
										null, null, true)),
						null,
						null));
	}

	/**
	 * Checks to see if the module has been installed
	 *
	 * @access public
	 * @return boolean
	 */
	function isInstalled() {
		return (bool) defined('MODULE_PAYMENT_VADS_STATUS');
	}

	/**
	 * Installs the module
	 *
	 * @access public
	 * @see osC_Payment_Admin::install()
	 */
	function install() {
		global $osC_Database, $osC_Language;

		parent::install();

		foreach ($this->_getConfig() as $key => $params) {
			$this->_insert_conf($key, $params[0], $params[1], $params[2]);
		}
	}

	/**
	 * Shortcut method to avoid lines that are 500 characters long when inserting configuration options
	 * @param string $key
	 * @param string $value
	 * @param string $use_function
	 * @param string $set_function
	 * @access private
	 */
	function _insert_conf($key, $value, $use_function = null,
			$set_function = null) {
		global $osC_Database, $osC_Language;

		// Prepare variables
		static $sort_order = 0; // increase counter at each function call
		$sort_order++;
		$group_id = 6; // all config variables are in the same group
		$title = $osC_Database->escapeString(
						$osC_Language->get(strtolower($key . '_TITLE'))); // automatically find translations
		$description = $osC_Database->escapeString(
						$osC_Language->get(strtolower($key . '_DESC')));
		$value = $osC_Database->escapeString($value);

		// Build query
		$query = 'INSERT INTO ' . TABLE_CONFIGURATION;
		$query .= ' (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added';
		$query .= $use_function ? ', use_function' : '';
		$query .= $set_function ? ', set_function' : '';
		$query .= ") VALUES (";
		$query .= "'$title', '$key', '$value', '$description', '$group_id', '$sort_order', now()";
		$query .= $use_function ? ", '$use_function'" : '';
		$query .= $set_function ? ", '$set_function'" : '';
		$query .= ');';

		// Insert into db
		$osC_Database->simpleQuery($query);
	}

	/**
	 * Return the configuration parameter keys in an array
	 *
	 * @return array
	 */
	function getKeys() {
		if (!isset($this->_keys)) {
			$this->_keys = array_keys($this->_getConfig());
		}

		return $this->_keys;
	}
}
?>