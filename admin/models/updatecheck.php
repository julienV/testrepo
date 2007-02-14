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
		jimport('joomla.utilities.simplexml');

		$elsettings = & ELAdmin::config();

		//set the source file
		$file = 'http://www.schlu.net/updatecheck.xml';

		//Create a JSimpleXML object
		$xml = new JSimpleXML();

		//Load the xml file
		$_updatedata = null;
		if (!@$xml->loadFile($file))	{

			$_updatedata->current = '';
			$_updatedata->versiondetail = '';
			$_updatedata->date = '';
			$_updatedata->changes = '';
			$_updatedata->info = '';
			$_updatedata->download = '';
			$_updatedata->notes = '';
			$_updatedata->failed = 1;

		} else {

			$_updatedata->version 		= $xml->document->versions[0]->version[0]->data();
			$_updatedata->versiondetail	= $xml->document->versions[0]->versiondetail[0]->data();
			$_updatedata->date			= strftime( $elsettings->formatdate, strtotime( $xml->document->versions[0]->date[0]->data() ));
			$_updatedata->changes 		= $xml->document->versions[0]->changes[0]->children();
			$_updatedata->info 			= $xml->document->versions[0]->information[0]->data();
			$_updatedata->download 		= $xml->document->versions[0]->download[0]->data();
			$_updatedata->notes			= $xml->document->versions[0]->plot[0]->data();
			$_updatedata->failed 		= 0;

			$_updatedata->current = version_compare( "0.9.0.0.alpha", $_updatedata->version );
		}

		return $_updatedata;
	}

}
?>