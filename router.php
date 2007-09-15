<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

function EventListBuildRoute(&$query)
{
	$segments = array();

	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}
/*
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
*/
	if(isset($query['cid']))
	{
		$segments[] = $query['cid'];
		unset($query['cid']);
	};
/*
	if(isset($query['categid']))
	{
		$segments[] = $query['categid'];
		unset($query['categid']);
	};
*/
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
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
			$vars['view'] = 'categoryevents';

			$count = count($segments);
			if($count > 2) {
				$vars['task'] = $segments[2];
			}

		} break;

		case 'details':
		{
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
			$vars['view'] = 'details';

		} break;

		case 'venueevents':
		{
			$id = explode(':', $segments[1]);
			$vars['id'] = $id[0];
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