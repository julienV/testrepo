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
	$segments = array();

	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}
	
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
	
	if(isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	};

	return $segments;
}

function EventListParseRoute($segments)
{
	//Handle View and Identifier
	switch($segments[0])
	{
		case 'categoryevents':
		{
			JRequest::setVar('categid'  , substr( $segments[1], 0, 1 ), 'get');
			$view = 'categoryevents';

		} break;

		case 'details':
		{
			JRequest::setVar('did'  	, substr( $segments[1], 0, 1 ), 'get');
			$view = 'details';

		} break;
		
		case 'venueevents':
		{
			JRequest::setVar('locatid'  	, substr( $segments[1], 0, 1 ), 'get');
			$view = 'venueevents';

		} break;
		
		case 'editevent':
		{
			JRequest::setVar('id'  		, substr( $segments[1], 0, 1 ), 'get');
			$view = 'editevent';

		} break;
		
		case 'editvenue':
		{
			JRequest::setVar('id'  		, substr( $segments[1], 0, 1 ), 'get');
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