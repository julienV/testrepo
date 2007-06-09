<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * EventList Component Updatecheck Model
 *
 * @package Joomla
 * @subpackage EventList
 * @since		0.9
 */
class EventListModelUpdatecheck extends JModel
{
	/**
	 * Events data in array
	 *
	 * @var array
	 */
	var $_updatedata = null;

	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Logic for the Update Check
	 *
	 * @access public
	 * @return object
	 * @since 0.9
	 */
	function getUpdatedata()
	{
		global $mainframe;

		$elsettings = ELAdmin::config();

		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'Snoopy.class.php');

		$snoopy = new Snoopy();

		//set the source file
		$file = 'http://www.schlu.net/elupdate.php';

		$snoopy->read_timeout 	= 30;
		$snoopy->referer 		= $mainframe->getCfg('live_site');
		$snoopy->agent 			= "Mozilla/5.0 (compatible; Konqueror/3.2; Linux 2.6.2) (KHTML, like Gecko)";

		$snoopy->fetch($file);

		$_updatedata = null;

		if ($snoopy->status != 200 || $snoopy->error) {

			$_updatedata->failed = 1;

		} else {

			$data = explode('|', $snoopy->results);

			$_updatedata->version 		= $data[0];
			$_updatedata->versiondetail	= $data[1];
			$_updatedata->date			= strftime( $elsettings->formatdate, strtotime( $data[2] ) );
			$_updatedata->info 			= $data[3];
			$_updatedata->download 		= $data[4];
			$_updatedata->notes			= $data[5];
			$_updatedata->changes 		= explode(';', $data[6]);
			$_updatedata->failed 		= 0;

			$_updatedata->current = version_compare( '0.9.0.0.alpha', $_updatedata->version );

		}

		return $_updatedata;
	}

}
?>