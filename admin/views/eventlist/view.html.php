<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * View class for the EventList home screen
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEventList extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$user 		= & JFactory::getUser();

		//build toolbar
		JToolBarHelper::title( JText::_( 'EVENTLIST' ), 'home' );
		JToolBarHelper::help( 'el.intro', true );

		// Get data from the model
		$events      = & $this->get( 'Eventsdata');
		$venue       = & $this->get( 'Venuesdata');
		$category	 = & $this->get( 'Categoriesdata' );

		//add css and submenu to document
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		//Create Submenu
		JSubMenuHelper::addEntry( JText::_( 'EVENTLIST' ), 'index.php?option=com_eventlist', true);
		JSubMenuHelper::addEntry( JText::_( 'EVENTS' ), 'index.php?option=com_eventlist&view=events');
		JSubMenuHelper::addEntry( JText::_( 'VENUES' ), 'index.php?option=com_eventlist&view=venues');
		JSubMenuHelper::addEntry( JText::_( 'CATEGORIES' ), 'index.php?option=com_eventlist&view=categories');
		JSubMenuHelper::addEntry( JText::_( 'ARCHIVE' ), 'index.php?option=com_eventlist&view=archive');
		JSubMenuHelper::addEntry( JText::_( 'GROUPS' ), 'index.php?option=com_eventlist&view=groups');
		JSubMenuHelper::addEntry( JText::_( 'HELP' ), 'index.php?option=com_eventlist&view=help');
		if ($user->get('gid') > 24) {
			JSubMenuHelper::addEntry( JText::_( 'SETTINGS' ), 'index.php?option=com_eventlist&controller=settings&task=edit');
		}

		//assign vars to the template
		$this->assignRef('pane'			, $pane);
		$this->assignRef('events'		, $events);
		$this->assignRef('venue'		, $venue);
		$this->assignRef('category'		, $category);
		$this->assignRef('user'			, $user);

		parent::display($tpl);

	}

	/**
	 * Creates the buttons view
	 *
	 * @param string $link targeturl
	 * @param string $image path to image
	 * @param string $text image description
	 * @param boolean $modal 1 for loading in modal
	 */
	function quickiconButton( $link, $image, $text, $modal = 0 )
	{
		global $mainframe;

		//initialise variables
		$lang 		= & JFactory::getLanguage();
		$document	= & JFactory::getDocument();
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<?php
				if ($modal == 1) {
						$url 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
						$document->addScript($url.'includes/js/joomla/modal.js');
						$document->addStyleSheet($url.'includes/js/joomla/modal.css');
				?>
					<a style="cursor:pointer" onclick="document.popup.show('<?php echo $link.'&tmpl=component'; ?>', 650, 400, null);">
				<?php
				} else {
				?>
				<a href="<?php echo $link; ?>">
				<?php
				}
					echo JHTML::_('image.site', $image, '/components/com_eventlist/assets/images/', NULL, NULL, $text );
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}
?>