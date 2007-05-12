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

	if(isset($query['returnid']))
	{
		$segments[] = $query['returnid'];
		unset($query['returnid']);
	};

	if(isset($query['returnview']))
	{
		$segments[] = $query['returnview'];
		unset($query['returnview']);
	};

	return $segments;
}

function EventListParseRoute($segments)
{

//	print_r($segments);

	//Handle View and Identifier
	switch($segments[0])
	{
		case 'categoryevents':
		{
			$categid = explode(':', $segments[1]);
			$vars['categid'] = $categid[0];
			$vars['view'] = 'categoryevents';

		} break;

		case 'details':
		{
			$did = explode(':', $segments[1]);
			$vars['did'] = $did[0];
			$vars['view'] = 'details';

		} break;

		case 'venueevents':
		{
			$locatid = explode(':', $segments[1]);
			$vars['locatid'] = $locatid[0];
			$vars['view'] = 'venueevents';

		} break;

		case 'editevent':
		{
			$vars['id'] = $segments[1];
			$vars['view'] = 'editevent';
			$vars['returnid'] = $segments[2];

		} break;

		case 'editvenue':
		{
			$vars['id'] = $segments[1];
			$vars['view'] = 'editvenue';
			$vars['returnid'] = $segments[2];

		} break;

		case 'eventlist':
		{
			$vars['view'] = 'eventlist';

		} break;

		case 'categoriesdetailed':
		{
			$vars['view'] = 'categoriesdetailed';

		} break;

		case 'categoriesview':
		{
			$vars['view'] = 'categoriesview';

		} break;

		case 'venuesview':
		{
			$vars['view'] = 'venuesview';

		} break;

	}

	return $vars;
}
?>