<?php
/**
 * @name		plgSystemAdminSecure
 * @version		0.9
 * @author		Holger Mey - www.kwantos.de
 * @copyright	www.kwantos.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter' );

class plgSystemAdminSecure extends JPlugin {

	function __construct(&$subject, $config)
	{

		parent::__construct($subject, $config);
	}

	function onAfterDispatch() {

		$redirect	='';
		$session	=& JFactory::getSession();
		$plugin		=& JPluginHelper::getPlugin( 'system', 'adminsecure' );
		$user		=& JFactory::getUser();
		$params		= json_decode($plugin->params);
		$secretkey	= $params->secretkey;
		$checkedKey = $session->get('adminsecure');

		$jinput = JFactory::getApplication()->input;
		$pass = $jinput->get('pass', '', 'STRING');

		if (empty($checkedKey) && $pass != $secretkey && !isset($_GET[$secretkey])) {

			$document = JFactory::getDocument();
			$document->setGenerator(''); // TODO: Einstellbarer Generator

			if((preg_match("/administrator\/*index.?\.php$/i", $_SERVER['SCRIPT_NAME']))) {

				if(!$user->id && $secretkey != $_SERVER['QUERY_STRING'] ) {
					$getapp   =& JFactory::getApplication();
					$redirect = JURI::root();
					$getapp->redirect($redirect);

				}
				else {
					$session->set('AdminSecure', 1);
				}
			}
		}
	}
}