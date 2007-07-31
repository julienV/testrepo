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

	if(isset($query['layout']))
	{
		$segments[] = $query['layout'];
		unset($query['layout']);
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

	if(isset($query['cid']))
	{
		$segments[] = $query['cid'];
		unset($query['cid']);
	};

	//Deprecated
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

	if(isset($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
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
/*
	if(isset($query['pop']))
	{
		$segments[] = $query['pop'];
		unset($query['pop']);
	};

	if(isset($query['tmpl']))
	{
		$segments[] = $query['tmpl'];
		unset($query['tmpl']);
	};
*/

	return $segments;
}

function EventListParseRoute($segments)
{
	$vars = array();

	//Handle View and Identifier
	switch($segments[0])
	{
		case 'categoryevents':
		{
			$categid = explode(':', $segments[1]);
			$vars['categid'] = $categid[0];
			$vars['view'] = 'categoryevents';

			$count = count($segments);
			if($count > 2) {
				$vars['task'] = $segments[2];
			}

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
			$count = count($segments);

			if($count == 3) {
				$vars['view'] = 'editevent';
				$vars['id'] = $segments[1];
				$vars['returnid'] = $segments[2];
			} else {
				$vars['view'] = 'editevent';
				$vars['returnview'] = $segments[1];
			}

		} break;

		case 'editvenue':
		{
			$count = count($segments);

			if($count == 3) {

				$vars['view'] = 'editvenue';
				$vars['id'] = $segments[1];
				$vars['returnid'] = $segments[2];

			} else {
				$vars['view'] = 'editvenue';
				$vars['returnview'] = $segments[1];
			}

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

			$count = count($segments);
			if($count == 2) {
				$vars['task'] = $segments[1];
			}

		} break;

		case 'venuesview':
		{
			$vars['view'] = 'venuesview';

		} break;

	}

	return $vars;
}
?>