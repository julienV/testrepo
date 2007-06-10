<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Holds helpfull administration related stuff
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class ELAdmin {

	/**
	* Writes Administrator submenu.
	*
	* @since 0.9
	*/
	function submenu()
	{
		$user = & JFactory::getUser();

		// Get active sub menu item
		$activeview	= JRequest::getVar('view');

		// Get hidden status
		$hide = JRequest::getVar('hidemainmenu', 0);

		// Create submenu array
		$subMenus = array(
		'EVENTS' => 'view=events',
		'VENUES' => 'view=venues',
		'CATEGORIES' => 'view=categories',
		'ARCHIVE' => 'view=archive',
		'GROUPS' => 'view=groups',
		'HELP' => 'view=help',
		);

		//only admins should be able to see the settings submenuitem
		$onlyadmins = array('SETTINGS' => 'controller=settings&task=edit');

		if ($user->get('gid') > 24) {
			$subMenus = array_merge( $subMenus, $onlyadmins );
		}

		// Create the Home item
		$subMenuItem['title']	= JText::_( 'EVENTLIST' );
		$subMenuItem['link']	= 'index.php?option=com_eventlist';
		$subMenuItem['active']	= (!in_array( $activeview, $subMenus)) && ($hide == 0);
		$subMenuList[] = $subMenuItem;

		//Create all the rest out of the submenu array
		foreach ($subMenus as $name => $itemview)
		{
			$subMenuItem['title']	= JText::_( $name );
			$subMenuItem['link']	= 'index.php?option=com_eventlist&' . $itemview;
			$subMenuItem['active']	= ($itemview == $activeview);
			$subMenuList[] = $subMenuItem;
		}

		/*
		* Create the submenu
		*/
		$txt = "<ul id=\"submenu\">\n";

		/*
		* Iterate through the link items for building the menu items
		*/
		foreach ($subMenuList as $item)
		{
			$txt .= "<li class=\"item\">\n";
			if ($hide)
			{
				if (isset ($item['active']) && $item['active'] == 1)
				{
					$txt .= "<span class=\"nolink active\">".$item['title']."</span>\n";
				}
				else
				{
					$txt .= "<span class=\"nolink\">".$item['title']."</span>\n";
				}
			}
			else
			{
				if (isset ($item['active']) && $item['active'] == 1)
				{
					$txt .= "<a class=\"active\" href=\"".$item['link']."\">".$item['title']."</a>\n";
				}
				else
				{
					$txt .= "<a href=\"".$item['link']."\">".$item['title']."</a>\n";
				}
			}
			$txt .= "</li>\n";
		}

		$txt .= "</ul>\n";

		return $txt;
	}

	/**
	* Writes footer. Do not remove!
	*
	* @since 0.9
	*/
	function footer( )
	{

		echo 'EventList by <a href="http://www.schlu.net" target="_blank">schlu.net</a>';

	}

	function config()
	{
		$db =& JFactory::getDBO();

		$sql = 'SELECT * FROM #__eventlist_settings WHERE id = 1';
		$db->setQuery($sql);
		$config = $db->loadObject();

		return $config;
	}
}

?>