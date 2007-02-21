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
 * Holds all authentication logic
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class ELUser {

	/**
	 * Checks access permissions of the user regarding on the groupid
	 * 
	 * @author Christoph Lukes
	 * @since 0.9
	 * 
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	function validate_user ( $recurse, $level )
	{
		$user 		= & JFactory::getUser();
		
		//only check when user is logged in
		if ($user->get('id')) {
		
		$acl		= & JFactory::getACL();
		$superuser 	= ELUser::superuser();
		
		$groupid	= $user->get('gid');
		
		if ($recurse) {
			$recursec="RECURSE";
		} else {
			$recursec="NO_RECURSE";
		}
		
		//open for superuser or registered and thats all what is needed
		if ((( $level == -1 ) && ( $groupid > 0 )) || (( $superuser ) && ( $level != -2 ))) {
			return 1;
			
		//if not proceed checking
		} else {
			
			if( $groupid == $level ) {
				//User has the needed groupid->ok
				return 1;
				
			} else {
				
				if ($recursec=='RECURSE') {
					//Child group for this level?
					$group_childs=array();
					$group_childs=$acl->get_group_children( $level, 'ARO', $recursec );

					if ( is_array( $group_childs ) && count( $group_childs ) > 0) {

						//Childgroups exists than check if user belongs to one of it
						if ( in_array($groupid, $group_childs) ) {

							//User belongs to one of it -> ok
							return 1;
						}
					}
				}
			}
		}		
		//end logged in check
		}
		
		//oh oh, user have no permissions
		return 0;
	}

	/**
	 * Checks if the user is allowed to edit an item
	 * 
	 * @author Christoph Lukes
	 * @since 0.9
	 * 
	 * @param int $allowowner
	 * @param int $ownerid
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	function editaccess($allowowner, $ownerid, $recurse, $level)
	{
		$user		= & JFactory::getUser();
		
		$generalaccess = ELUser::validate_user( $recurse, $level );

		if ($allowowner == 1 && ( $user->get('id') == $ownerid && $ownerid != 0 ) ) {
			return 1;
		} elseif ($generalaccess == 1) {
			return 1;
		}
		return 0;
	}
	
	/**
	 * Checks if the user is a superuser
	 * A superuser will allways have access if the feature is activated
	 * 
	 * @since 0.9
	 */
	function superuser()
	{
		$user 		= & JFactory::getUser();
		$superuser 	= (strtolower($user->get('usertype')) == 'administrator' || strtolower($user->get('usertype')) == 'super administrator' );
		
		return $superuser;
	}
	
	/**
	 * Checks if the user has the privileges to use the wysiwyg editor
	 * 
	 * We could use the validate_user method instead of this to allow to set a groupid
	 * Not sure if this is a good idea
	 * 
	 * @since 0.9
	 */
	function editoruser()
	{
		$user 		= & JFactory::getUser();
		$editoruser = (JString::strtolower($user->get('usertype')) == 'editor' || JString::strtolower($user->get('usertype')) == 'publisher' || JString::strtolower($user->get('usertype')) == 'manager' || JString::strtolower($user->get('usertype')) == 'administrator' || JString::strtolower($user->get('usertype')) == 'super administrator' );
		
		return $editoruser;
	}
	
	/**
	 * Checks if the user is the owner of the event/venue
	 * 
	 * @since 0.9
	 */
	function isOwner($id, $table)
	{
		$db = JFactory::getDBO();
		
		$eventid = (int) $id;
		//Check if user can edit events
		$db->SetQuery("SELECT uid"
					. "\n FROM #__eventlist_".$table 
					. "\n WHERE id = ".$eventid
					);
    	return $db->loadResult( );
	}
	
	function ismaintainer()
	{
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= & JFactory::getUser();
		
		$query = "SELECT g.group_id"
		. "\nFROM #__eventlist_groupmembers AS g"
		. "\nWHERE g.member = ".$user->get('id')
		;
		$db->setQuery( $query );
		$maintainer = $db->loadResult();
		
		return $maintainer;
	}
}