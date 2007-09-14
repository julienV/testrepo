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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * View class for the EventList imageselect screen
 * Based on the Joomla! media component
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewImagehandler extends JView  {

	function display($tpl = null)
	{
		if($this->getLayout() == 'uploadimage') {
			$this->_displayuploadimage($tpl);
			return;
		}

		//Load filesystem folder
		jimport('joomla.filesystem.folder');

		//initialise variables
		$document   =& JFactory::getDocument();

		//get vars
		$task = JRequest::getVar( 'task' );

		//add css
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//set variables
		if ($task == 'selecteventimg') {
			$Path 	= '/images/eventlist/events/';
			$task 	= 'eventimg';
		} else {
			$Path 	= '/images/eventlist/venues/';
			$task	= 'venueimg';
		}

		$basePath 	= JPATH_SITE.$Path;
		$images 	= array ();

		// Get the list of files and folders from the given folder
		$fileList 	= JFolder::files($basePath);

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file)
			{
				if (is_file($basePath.DS.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
					if ($this->_isImage($file)) {
						$imageInfo = @ getimagesize($basePath.DS.$file);
						$fileDetails['name'] = $file;
						$fileDetails['file'] = JPath::clean($basePath.DS.$file, false);
						$fileDetails['imgInfo'] = $imageInfo;
						$fileDetails['size'] = filesize($basePath.DS.$file);
						$images[] = $fileDetails;
					}
				}
			}
		}

		//prepare images
		if (count($images) > 0 )
		{
			//now sort the images by name.
			ksort($images);

			// Handle the images
			if ( $numImages = count( $images ) ) {
				for( $i = 0; $i < $numImages; $i++ ) {

					$file		= $images[$i]['name'];
					$img_url	= $Path.rawurlencode($file);
					$info		= $images[$i]['imgInfo'];


					if (($info[0] > 70) || ($info[1] > 70)) {
						$img_dimensions = $this->_imageResize($info[0], $info[1], 80);
					} else {
						$img_dimensions = 'width="' . $info[0] . '" height="' . $info[1] . '"';
					}

					//output the images
					?>
					<div class="imgOutline">
						<div class="imgTotal">
							<div align="center" class="imgBorder">
								<a style="display: block; width: 100%; height: 100%" onclick="window.parent.elSelectImage('<?php echo $file; ?>', '<?php echo $file; ?>');">
									<div class="image">
										<img src="<?php echo '../'.$img_url; ?>" <?php echo $img_dimensions; ?> border="0" />
									</div>
								</a>
							</div>
						</div>
						<div class="imginfoBorder">
							<?php echo htmlspecialchars( substr( $file, 0, 10 ) . ( strlen( $file ) > 10 ? '...' : ''), ENT_QUOTES ); ?>
						</div>
					</div>
					<?php

				}
			}
		} else {
			//no images in the folder, redirect to uploadscreen and raise notice
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('NO IMAGES AVAILABLE'));
			$this->setLayout('uploadimage');
			JRequest::setVar( 'task', $task );
			$this->_displayuploadimage($tpl);
			return;
		}

	}

	/**
	 * Checks if the file is an image
	 *
	 * @access private
	 * @param string The filename
	 * @return boolean
	 */
	function _isImage( $fileName )
	{
		static $imageTypes = 'gif|jpg|png';
		return preg_match("/$imageTypes/i",$fileName);
	}

	/**
	 * Resizes the image if needed
	 *
	 * @access private
	 * @param int $width
	 * @param int $height
	 * @param int $target
	 * @return string
	 */
	function _imageResize($width, $height, $target)
	{
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width 	= round($width * $percentage);
		$height = round($height * $percentage);

		//returns the new sizes in html image tag format...this is so you
		//can plug this function inside an image tag and just get the
		return "width=\"$width\" height=\"$height\"";
	}

	/**
	 * Prepares the upload image screen
	 *
	 * @param unknown_type $tpl
	 */
	function _displayuploadimage($tpl = null)
	{
		//initialise variables
		$document	= & JFactory::getDocument();
		$uri 		= & JFactory::getURI();
		$elsettings = ELAdmin::config();

		//get vars
		$task 		= JRequest::getVar( 'task' );

		//add css
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//assign data to template
		$this->assignRef('task'      	, $task);
		$this->assignRef('elsettings'  	, $elsettings);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>