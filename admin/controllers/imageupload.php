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

		//set the target directory
		if ($task == 'venueimgup') {
			$base_Dir = JPATH_SITE.DS.'images'.DS.'eventlist'.DS.'venues'.DS;
		} else {
			$base_Dir = JPATH_SITE.DS.'images'.DS.'eventlist'.DS.'events'.DS;
		}

		//do we have an upload?
		if (empty($file['name'])) {
			echo "<script> alert('".JText::_( 'IMAGE EMPTY' )."'); window.history.go(-1); </script>\n";
			$mainframe->close();
		}

		//check if the upload is an image...getimagesize will return false if not
		if (!@getimagesize($file['tmp_name'])) {
			echo "<script> alert('".JText::_( 'UPLOAD FAILED NOT AN IMAGE' )."'); window.history.go(-1); </script>\n";
			$mainframe->close();
		}

		//check if the imagefiletype is valid
		$fileext 	= JFile::getExt($file['name']);

		$allowable 	= array ('gif', 'jpg', 'png');
		if (in_array($fileext, $allowable)) {
			$noMatch = true;
		} else {
			$noMatch = false;
		}

		if (!$noMatch) {
			echo "<script> alert('".JText::_( 'WRONG IMAGE FILE TYPE' )."'); window.history.go(-1); </script>\n";
			$mainframe->close();
		}

		//Check filesize
		if ($imagesize > $sizelimit) {
			echo "<script> alert('".JText::_( 'IMAGE FILE SIZE' )."'); window.history.go(-1); </script>\n";
			$mainframe->close();
		}

		//sanitize the image filename
		$filename = ELImage::sanitize($base_Dir, $file['name']);
		$filepath = $base_Dir . $filename;

		//upload the image
		if (!JFile::upload($file['tmp_name'], $filepath)) {
			echo "<script> alert('".JText::_( 'UPLOAD FAILED' )."'); window.history.go(-1); </script>\n";
			$mainframe->close();

		} else {
			echo "<script> alert('".JText::_( 'UPLOAD COMPLETE' )."'); window.history.go(-1); window.parent.elSelectImage('$filename', '$filename'); </script>\n";
			$mainframe->close();
		}

	} //function uploadimage end

} // Class end
?>