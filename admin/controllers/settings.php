<?php
/**
* @version 0.9 $Id$
* @package Joomla
* @subpackage EventList
* @copyright (C) 2005 - 2007 Christoph Lukes
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * EventList Component Settings Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerSettings extends EventListController
{
	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'apply', 		'save' );
	}

	/**
	 * logic for cancel an action
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function cancel()
	{
		global $option;

		$model = $this->getModel('settings');

		$model->checkin();

		$this->setRedirect( 'index.php?option='.$option.'&view=eventlist' );
	}

	/**
	 * logic to create the edit venue view
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function edit( )
	{
		JRequest::setVar( 'view', 'settings' );

		parent::display();

		$model = $this->getModel('settings');

		$model->checkout();
	}

	/**
	 * saves the venue in the database
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function save()
	{
		global $option;

		// Sanitize
		$task	= JRequest::getVar('task');
		$post 	= JRequest::get( 'post' );

		//get model
		$model 	= $this->getModel('settings');

		$model->store($post);

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option='.$option.'&controller=settings&task=edit';
				break;

			default:
				$link = 'index.php?option='.$option.'&view=eventlist';
				break;
		}
		$msg	= JText::_( 'SETTINGS SAVE');

		$model->checkin();

		$this->setRedirect( $link, $msg );
	}
}
?>