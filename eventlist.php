<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

//Require helperfile
require_once (JPATH_COMPONENT_SITE.DS.'eventlist.helper.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'user.class.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'image.class.php');
require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'output.class.php');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

// Require the controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$classname  = 'EventListController';
$controller = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>