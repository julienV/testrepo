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
 * Holds the logic for all output related things
 *
 * @package Joomla
 * @subpackage EventList
 */
class ELOutput {

	/**
	* Writes footer. Official copyright! Do not remove!
	*
	* @author Christoph Lukes
	* @since 0.9
	*/
	function footer( )
	{
		echo 'EventList powered by <a href="http://www.schlu.net" target="_blank">schlu.net</a>';
	}

	/**
	* Writes Event submission button
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param int $Itemid The Itemid of the Component
	* @param int $dellink Access of user
	* @param array $params needed params
	* @param string $view the view the user will redirected to
	**/
	function submitbutton( $dellink, &$params, $view )
	{
		$document =& JFactory::getDocument();
		JHTML::_('behavior.tooltip');

		if ($dellink == 1) {

			$document->addScript('includes/js/joomla/modal.js');
			$document->addStyleSheet('includes/js/joomla/modal.css');

			if ( $params->get('icons') ) {
				$image = JHTML::_('image.site', 'submitevent.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'DELIVER NEW EVENT' ), JText::_( 'DELIVER NEW EVENT' ) );
			} else {
				$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'DELIVER NEW EVENT' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
			}

			$link 		= 'index.php?view=editevent&returnview='.$view;
			$overlib 	= JText::_( 'SUBMIT EVENT TIP' );
			$output		= '<a href="'.JRoute::_($link).'" class="editlinktip hasTip" title="'.JText::_( 'DELIVER NEW EVENT' ).'::'.$overlib.'">'.$image.'</a>';

			return $output;
		}

		return;
	}

	/**
	* Writes Archivebutton
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param int $oldevent Archive used or not
	* @param array $params needed params
	* @param string $task The current task
	* @param int $categid The cat id
	*/
	function archivebutton( $oldevent, &$params, $task = NULL, $categid = NULL )
	{
		JHTML::_('behavior.tooltip');

		if ( $oldevent == 2 ) {

			switch ($task) {
				case 'archive':

					if ( $params->get('icons') ) {
						$image = JHTML::_('image.site', 'eventlist.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'SHOW EVENTS' ), JText::_( 'SHOW EVENTS' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'SHOW EVENTS' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib 	= JText::_( 'SHOW EVENTS TIP' );
					$link 		= JRoute::_( 'index.php' );
	//				$link 		= JRoute::_( 'index.php?view=category' );
					$title 		= JText::_( 'SHOW EVENTS' );

					break;

				case 'catarchive':

					if ( $params->get('icons') ) {
						$image = JHTML::_('image.site', 'eventlist.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'SHOW EVENTS' ), JText::_( 'SHOW EVENTS' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'SHOW EVENTS' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib 	= JText::_( 'SHOW EVENTS TIP' );
					$link 		= JRoute::_( 'index.php?view=categoryevents&categid='.$categid );
	//				$link 		= JRoute::_( 'index.php?view=category&layout=default&categid='.$categid );
					$title 		= JText::_( 'SHOW EVENTS' );

					break;

				default:

					if ( $params->get('icons') ) {
						$image = JHTML::_('image.site', 'archive_front.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'SHOW ARCHIVE' ), JText::_( 'SHOW ARCHIVE' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'SHOW ARCHIVE' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib 	= JText::_( 'SHOW ARCHIVE TIP' );
					$link		= JRoute::_('index.php?view=categoriesview&task=archive');
	//				$link		= JRoute::_('index.php?view=category&task=archive');
					$title 		= JText::_( 'SHOW ARCHIVE' );

					break;
			}
			$output = '<a href="'.$link.'" class="editlinktip hasTip" title="'.$title.'::'.$overlib.'">'.$image.'</a>';

			return $output;

		}
		return;
	}

	/**
	 * Creates the edit button
	 *
	 * @param int $Itemid
	 * @param int $id
	 * @param array $params
	 * @param int $allowedtoedit
	 * @param string $task
	 * @since 0.9
	 */
	function editbutton( $Itemid, $id, &$params, $allowedtoedit, $view)
	{

		$document =& JFactory::getDocument();
		JHTML::_('behavior.tooltip');

		if ( $allowedtoedit ) {

			$document->addScript('includes/js/joomla/modal.js');
			$document->addStyleSheet('includes/js/joomla/modal.css');

			switch ($view)
			{
				case 'editevent':
					if ( $params->get('icons') ) {
						$image = JHTML::_('image.site', 'calendar_edit.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'EDIT EVENT' ), JText::_( 'EDIT EVENT' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'EDIT EVENT' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'EDIT EVENT TIP' );
					$text = JText::_( 'EDIT EVENT' );
					break;

				case 'editvenue':
					if ( $params->get('icons') ) {
						$image = JHTML::_('image.site', 'calendar_edit.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'EDIT EVENT' ), JText::_( 'EDIT EVENT' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'EDIT VENUE' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'EDIT VENUE TIP' );
					$text = JText::_( 'EDIT VENUE' );
					break;
			}

			$link 	= 'index.php?view='.$view.'&id='.$id.'&returnid='.$Itemid;
			$output	= '<a href="'.JRoute::_($link).'" class="editlinktip hasTip" title="'.$text.'::'.$overlib.'">'.$image.'</a>';

			return $output;
		}

		return;
	}

	/**
	 * Creates the print button
	 *
	 * @param string $print_link
	 * @param array $params
	 * @param int $pop
	 * @since 0.9
	 */
	function printbutton( $print_link, &$params )
	{
		if ($params->get( 'show_print_icon' )) {

			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

			// checks template image directory for image, if non found default are loaded
			if ( $params->get( 'icons' ) ) {
				$text = JHTML::_('image.site', 'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ), JText::_( 'Print' ) );
			} else {
				$text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
			}

			if ($params->get( 'popup' )) {
				//button in popup
				$output = '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
			} else {
				//button in view
				$attribs['title']   = '"'.JText::_( 'Print' ).'"';
				$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

				$output = JHTML::_('link', $print_link, $text, $attribs);
			}

			return $output;
		}
		return;
	}

	/**
	 * Creates the email button
	 *
	 * @param string $print_link
	 * @param array $params
	 * @param int $pop
	 * @since 0.9
	 */
	function mailbutton($params)
	{
		if ($params->get('show_email_icon')) 	{
			$url 	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode( JRequest::getURI());
			$status = 'width=400,height=300,menubar=yes,resizable=yes';

			if ($params->get('icons')) 	{
				$text = JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Email' ), JText::_( 'Email' ));
			} else {
				$text = '&nbsp;'.JText::_( 'Email' );
			}

			$attribs['title']	= '"'.JText::_( 'Email' ).'"';
			$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

			$output = JHTML::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
		return;
	}

	/**
	 * Creates the map button
	 *
	 * @param obj $data
	 * @param obj $settings
	 *
	 * @since 0.9
	 */
	function mapicon($data, $settings)
	{
		//Link to map
		$mapimage = JHTML::_('image.site', 'mapsicon.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'MAP' ), JText::_( 'MAP' ) );

		//set var
		$output 	= null;
		$attributes = null;

		//stop if disabled
		if (!$data->map) {
			return $output;
		}

		//google or map24
		switch ($settings->showmapserv)
		{
			case 1:
			{
  				if ($settings->map24id) {

				$url		= 'http://link2.map24.com/?lid='.$settings->map24id.'&maptype=JAVA&width0=2000&street0='.$data->street.'&zip0='.$data->plz.'&city0='.$data->city.'&country0='.$data->country.'&sym0=10280&description0='.$data->venue;
				$output		= '<a class="flyer" title="'.JText::_( 'MAP' ).'" href="'.$url.'" target="_blank">'.$mapimage.'</a>';

  				}
			} break;

			case 2:
			{
				if($settings->gmapkey) {

					$document 	= & JFactory::getDocument();

					$document->addScript('components/com_eventlist/assets/js/gmapsoverlay.js');
					$document->addScript('http://maps.google.com/maps?file=api&v=2&key='.trim($settings->gmapkey));
  					$document->addStyleSheet('components/com_eventlist/assets/css/gmapsoverlay.css', 'text/css');

					$url		= 'http://maps.google.com/maps?q='.str_replace(" ", "+", $data->street).', '.$data->plz.' '.str_replace(" ", "+", $data->city).', '.$data->country.'&venue='.$data->venue;
					$attributes = ' rel="gmapsoverlay"';
				} else {

					$url		= 'http://maps.google.com/maps?q='.str_replace(" ", "+", $data->street).', '.$data->plz.' '.str_replace(" ", "+", $data->city).', '.$data->country;
				}

				$output		= '<a class="flyer" title="'.JText::_( 'MAP' ).'" href="'.$url.'" target="_blank"'.$attributes.'>'.$mapimage.'</a>';

			} break;
		}

		return $output;
	}

	/**
	 * Creates the flyer
	 *
	 * @param obj $data
	 * @param obj $settings
	 * @param array $image
	 * @param string $type
	 *
	 * @since 0.9
	 */
	function flyer( $data, $settings, $image, $type = 'venue' )
	{

		//define the environment based on the type
		if ($type == 'event') {
			$folder		= 'events';
			$imagefile	= $data->datimage;
			$info		= $data->title;
		} else {
			$folder 	= 'venues';
			$imagefile	= $data->locimage;
			$info		= $data->venue;
		}

		//do we have an image?
		if (empty($imagefile)) {

			//nothing to do
			return;

		} else {

			//does a thumbnail exist?
			if (file_exists(JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$imagefile)) {

				if ($settings->lightbox == 0) {

					$url		= '#';
					$attributes	= 'class="flyer" onclick="window.open(\''.$image['original'].'\',\'Popup\',\'width='.$image['width'].',height='.$image['height'].',location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no\')"';

				} else {

					$url		= $image['original'];
					$attributes	= 'class="flyer" rel="lightbox" title="'.$info.'"';

				}

				$icon	= '<img src="'.$image['thumb'].'" width="'.$image['thumbwidth'].'" height="'.$image['thumbheight'].'" alt="'.$info.'" title="'.JText::_( 'CLICK TO ENLARGE' ).'" />';
				$output	= '<a href="'.$url.'" '.$attributes.'>'.$icon.'</a>';

			//No thumbnail? Then take the in the settings specified values for the original
			} else {

				$output	= '<img class="flyer" src="'.$image['original'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.$info.'" />';

			}
		}

		return $output;
	}

	/**
	 * Creates the country flag
	 *
	 * @param string $country
	 *
	 * @since 0.9
	 */
	function getFlag($country)
	{
        $country = JString::strtolower($country);

        jimport('joomla.filesystem.file');

        if (JFile::exists(JPATH_COMPONENT_SITE.DS.'assets'.DS.'images'.DS.'flags'.DS.$country.'.gif')) {
        	$countryimg = '<img src="components/com_eventlist/assets/images/flags/'.$country.'.gif" alt="'.JText::_( 'COUNTRY' ).': '.$country.'" width="16" height="11" />';

        	return $countryimg;
        }

        return null;
	}
}
?>