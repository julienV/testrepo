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
 * EventList Component Imageupload Controller
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListControllerImageupload extends EventListController
{
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask( 'eventimgup', 	'uploadimage' );
		$this->registerTask( 'venueimgup', 	'uploadimage' );
	}

	/**
	 * logic for uploading an image
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function uploadimage()
	{
		global $mainframe, $option;

		$elsettings = ELAdmin::config();
		jimport('joomla.filesystem.file');

		$file 		= JRequest::getVar( 'userfile', '', 'files', 'array' );
		$task 		= JRequest::getVar( 'task' );
		$sizelimit 	= $elsettings->sizelimit*1024; //size limit in kb
		$imagesize 	= $file['size'];

			if ($task == 'venueimgup') {
				$base_Dir = JPATH_SITE.DS.'images'.DS.'eventlist'.DS.'venues'.DS;
			} else {
				$base_Dir = JPATH_SITE.DS.'images'.DS.'eventlist'.DS.'events'.DS;
			}

			if (file_exists($base_Dir.strtolower($file['name']))) {
				echo "<script> alert('".JText::_( 'UPLOAD FAILED' )."'); window.history.go(-1); </script>\n";
				$mainframe->close();
			}

			if (empty($file)) {
				echo "<script> alert('".JText::_( 'IMAGE EMPTY' )."'); window.history.go(-1); </script>\n";
				$mainframe->close();
			}

			if ($imagesize > $sizelimit) {
				echo "<script> alert('".JText::_( 'IMAGE FILE SIZE' )."'); window.history.go(-1); </script>\n";
				$mainframe->close();
			}

			$format 	= JFile::getExt($file['name']);

			$allowable 	= array ('gif', 'jpg', 'png');
			if (in_array($format, $allowable)) {
				$noMatch = true;
			} else {
				$noMatch = false;
			}

			if (!$noMatch) {
				echo "<script> alert('".JText::_( 'WRONG IMAGE FILE TYPE' )."'); window.history.go(-1); </script>\n";
				$mainframe->close();
			}

			if (!JFile::upload($file['tmp_name'], $base_Dir.strtolower($file['name']))) {
				echo "<script> alert('".JText::_( 'UPLOAD FAILED' )."'); window.history.go(-1); </script>\n";
				$mainframe->close();

			} else {
				$imagename = $file['name'];
				echo "<script> alert('".JText::_( 'UPLOAD COMPLETE' )."'); window.history.go(-1); window.parent.elSelectImage('$imagename', '$imagename'); </script>\n";
				$mainframe->close();
			}

	} //function uploadimage end

} // Class end
?>