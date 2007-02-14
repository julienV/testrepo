<?php 
/**
* @version 0.9 $Id$
* @package Joomla 
* @subpackage EventList
* @copyright (C) 2005 - 2007 Christoph Lukes
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

function EventListBuildURL(&$ARRAY, &$params)
{
	$resolveNames = $params->get('realnames',0);

	// TODO: Resolve category names
	$parts = array();
	if(isset($ARRAY['view'])) {
		$parts[] = $ARRAY['view'];
	};

	if(isset($ARRAY['layout'])) {
		$parts[] = $ARRAY['layout'];
	};

	if(isset($ARRAY['id'])) {
		$parts[] = $ARRAY['id'];
	};
	
	if(isset($ARRAY['did'])) {
		$parts[] = $ARRAY['did'];
	};
	
	if(isset($ARRAY['locatid'])) {
		$parts[] = $ARRAY['locatid'];
	};
	
	if(isset($ARRAY['categid'])) {
		$parts[] = $ARRAY['categid'];
	};

	if (isset( $ARRAY['limit'] ))
	{
		// Do all pages if limit = 0
		if ($ARRAY['limit'] == 0) {
			$parts[] = 'all';
		} else {
			$limit		= (int) $ARRAY['limit'];
			$limitstart	= (int) @$ARRAY['limitstart'];
			$page		= floor( $limitstart / $limit ) + 1;
			$parts[]	= 'page'.$page.':'.$limit;
		}
	}

	//unset the whole array
	$ARRAY = array();

	return $parts;
}

function EventListParseURL($ARRAY, &$params)
{
	// view is always the first element of the array
	$view	= array_shift($ARRAY);
	$nArray	= count($ARRAY);

	JRequest::setVar('view', $view, 'get');

	switch ($view)
	{
		case 'venuesview':
		case 'categoriesdetailed':
		case 'categoriesview':
		case 'simplelist':
		{
			if (count($ARRAY))
			{
 				$variable = array_shift($ARRAY);

				if(is_numeric($variable))
				{
					JRequest::setVar('id', $variable, 'get');
				}
				else
				{
					JRequest::setVar('layout', $variable, 'get');
					$variable = array_shift($ARRAY);
					JRequest::setVar('id', $variable, 'get');
				}
			}			
			
		} break;

		case 'details':
		{
				$variable = array_shift($ARRAY);
				JRequest::setVar('did', $variable, 'get');

		} break;
		
		case 'categoryevents':
		{
 				$variable = array_shift($ARRAY);
				JRequest::setVar('categid', $variable, 'get');
			
		} break;
		
		case 'venueevents':
		{
 				$variable = array_shift($ARRAY);
				JRequest::setVar('locatid', $variable, 'get');
		} break;
	
	
		// Handle Pagination
		$last = array_shift($ARRAY);
		if ($last == 'all')
		{
			array_pop( $ARRAY );
			JRequest::setVar('limitstart', 0, 'get');
			JRequest::setVar('limit', 0, 'get');
			// if you want more than 1e6 on your page then you are nuts!
		}
		elseif (strpos( $last, 'page' ) === 0)
		{
			array_pop( $ARRAY );
			$pts		= explode( ':', $last );
			$limit		= @$pts[1];
			$limitstart	= (max( 1, intval( str_replace( 'page', '', $pts[0] ) ) ) - 1)  * $limit;
			JRequest::setVar('limit',$limit, 'get');
			JRequest::setVar('limitstart', $limitstart, 'get');
		}
	
		default: break;
	}
}
?>