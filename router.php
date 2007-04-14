<?php 
/**
* @version 0.9 $Id$
* @package Joomla 
* @subpackage EventList
* @copyright (C) 2005 - 2007 Christoph Lukes
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

function EventListBuildRoute(&$query)
{
	
	//print_r($query);
	
	$segments = array();

	if(isset($query['did']))
	{
		$segments[] = $query['did'];
		unset($query['did']);
	};

	if(isset($query['locatid']))
	{
		$segments[] = $query['locatid'];
		unset($query['locatid']);
	};
	
	if(isset($query['categid']))
	{
		$segments[] = $query['categid'];
		unset($query['categid']);
	};

	unset($query['view']);

	return $segments;
}

function EventListParseRoute($segments)
{	
	//print_r($segments);
	
	//Get the active menu item
	$menu =& JMenu::getInstance();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);

	//Handle View and Identifier
	switch($item->query['view'])
	//switch(JRequest::getVar('view'))
	{
		case 'categoryevents':
		{
			JRequest::setVar('categid'  , $segments[2], 'get');
			$view = 'categoryevents';

		} break;

		case 'details':
		{
			JRequest::setVar('did'  	, $segments[2], 'get');
			$view = 'details';

		} break;
		
		case 'editevent':
		{
			JRequest::setVar('id'  		, $segments[2], 'get');
			$view = 'editevent';

		} break;
		
		case 'editvenue':
		{
			JRequest::setVar('id'  		, $segments[2], 'get');
			$view = 'editvenue';

		} break;
		
		case 'eventlist':
		{
			$view = 'eventlist';

		} break;
		
		case 'categoriesdetailed':
		{
			$view = 'categoriesdetailed';

		} break;
		
		case 'categoriesview':
		{
			$view = 'categoriesview';

		} break;
		
		case 'venuesview':
		{
			$view = 'venuesview';

		} break;
		
	}
	
	JRequest::setVar('view', $view, 'get');
}
?>