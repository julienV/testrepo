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
	* Writes footer. Do not remove!
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

		// Load tooltips behavior
		jimport('joomla.html.tooltips');

		if ($dellink == 1) {

			$document->addScript('includes/js/joomla/modal.js');
			$document->addStyleSheet('includes/js/joomla/modal.css');

			if ( $params->get('icons') ) {
				$image = JAdminMenus::ImageCheck( 'submitevent.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'DELIVER NEW EVENT' ), JText::_( 'DELIVER NEW EVENT' ) );
			} else {
				$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'DELIVER NEW EVENT' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
			}

			$link = 'index.php?option=com_eventlist&amp;view=editevent&amp;returnview='.$view;

			$overlib = JText::_( 'SUBMIT EVENT TIP' );
			?>
			<a href="<?php echo $link ?>" class="editlinktip hasTip" title="<?php echo JText::_( 'DELIVER NEW EVENT' ); ?>::<?php echo $overlib; ?>"><?php echo $image; ?></a>
			<?php

		} else {
			echo '&nbsp;';
		}
	}//function submitevbutton end

	/**
	* Writes Archivebutton
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param int $Itemid The Itemid of the Component
	* @param int $oldevent Archive used or not
	* @param array $params needed params
	* @param string $task The current task
	*/
	function archivebutton( $oldevent, &$params, $task = NULL, $categid = NULL )
	{
		if ( $oldevent == 2 ) {

			// Load tooltips behavior
			jimport('joomla.html.tooltips');

			switch ($task) {
				case 'archive':

					if ( $params->get('icons') ) {
						$image = JAdminMenus::ImageCheck( 'eventlist.png', '/components/com_eventlist/assets/', NULL, NULL, JText::_( 'SHOW EVENTS' ), JText::_( 'SHOW EVENTS' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'SHOW EVENTS' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'SHOW EVENTS TIP' );
					$link = JRoute::_( 'index.php?option=com_eventlist' );
					?>
					<a href="<?php echo $link ?>" class="editlinktip hasTip" title="<?php echo JText::_( 'SHOW EVENTS' ); ?>::<?php echo $overlib; ?>"><?php echo $image; ?></a>
					<?php
					break;

				case 'catarchive':

					if ( $params->get('icons') ) {
						$image = JAdminMenus::ImageCheck( 'eventlist.png', '/components/com_eventlist/assets/', NULL, NULL, JText::_( 'SHOW EVENTS' ), JText::_( 'SHOW EVENTS' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'SHOW EVENTS' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'SHOW EVENTS TIP' );
					$link = JRoute::_( 'index.php?option=com_eventlist&amp;view=categoryevents&amp;categid='.$categid );
					?>
					<a href="<?php echo $link ?>" class="editlinktip hasTip" title="<?php echo JText::_( 'SHOW EVENTS' ); ?>::<?php echo $overlib; ?>"><?php echo $image; ?></a>
					<?php

					break;

				default:

					if ( $params->get('icons') ) {
						$image = JAdminMenus::ImageCheck( 'archive_front.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'SHOW ARCHIVE' ), JText::_( 'SHOW ARCHIVE' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'SHOW ARCHIVE' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'SHOW ARCHIVE TIP' );
					$link = JRoute::_('index.php?option=com_eventlist&amp;view=categoriesview&amp;task=archive');
						?>
						<a href="<?php echo $link ?>" class="editlinktip hasTip" title="<?php echo JText::_( 'SHOW ARCHIVE' ); ?>::<?php echo $overlib; ?>"><?php echo $image; ?></a>
						<?php
						break;
			}

		} else {
			echo '&nbsp;';
		}
	}//function archiveevbutton end

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
		// Load tooltips behavior
		jimport('joomla.html.tooltips');

		if ( $allowedtoedit ) {

			$document->addScript('includes/js/joomla/modal.js');
			$document->addStyleSheet('includes/js/joomla/modal.css');

			switch ($view) {

				case 'editevent':
					if ( $params->get('icons') ) {
						$image = JAdminMenus::ImageCheck( 'calendar_edit.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'EDIT EVENT' ), JText::_( 'EDIT EVENT' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'EDIT EVENT' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'EDIT EVENT TIP' );
					$text = JText::_( 'EDIT EVENT' );
					break;

				case 'editvenue':
					if ( $params->get('icons') ) {
						$image = JAdminMenus::ImageCheck( 'calendar_edit.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'EDIT EVENT' ), JText::_( 'EDIT EVENT' ) );
					} else {
						$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'EDIT VENUE' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
					}
					$overlib = JText::_( 'EDIT VENUE TIP' );
					$text = JText::_( 'EDIT VENUE' );
					break;
			}

			$link = 'index.php?option=com_eventlist&amp;Itemid='.$Itemid.'&amp;Returnid='.$Itemid.'&amp;view='.$view.'&amp;id='.$id;

				?>
				<a href="<?php echo $link ?>" class="editlinktip hasTip" title="<?php echo $text; ?>::<?php echo $overlib; ?>"><?php echo $image; ?></a>
			<?php
		} else {
			echo '&nbsp;';
		}
	}//function editbutton end

	/**
	 * Creates the print button
	 *
	 * @param string $print_link
	 * @param array $params
	 * @since 0.9
	 */
	function printbutton( $print_link, &$params )
	{
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ( $params->get( 'icons' ) ) {
			$text = JAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ), JText::_( 'Print' ) );
		} else {
			$text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
		}

		$attribs['title']   = '"'.JText::_( 'Print' ).'"';
		$attribs['onclick'] = "\"window.open('".$print_link."','win2','".$status."'); return false;\"";

		return JHTML::Link($print_link, $text, $attribs);
	}
}
?>