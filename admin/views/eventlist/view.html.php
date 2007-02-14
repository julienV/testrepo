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
		
	//	$lang 		= & JFactory::getLanguage();
		$document	= & JFactory::getDocument();
		
		JMenuBar::title( JText::_( 'EVENTLIST' ), 'home' );
		JMenuBar::help( 'el.intro', true );
		
		// Get data from the model
		$events      = & $this->get( 'Eventsdata');
		$venue       = & $this->get( 'Venuesdata');
		$category	 = & $this->get( 'Categoriesdata' );
		
		$submenu 	= ELAdmin::submenu();
		$document->setBuffer($submenu, 'module', 'submenu');
		
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		
		jimport('joomla.html.pane');
		
		$live_site 	= $mainframe->getCfg('live_site');
		$pane   	= & JPane::getInstance('sliders');
		
				
		$this->assignRef('live_site' 	, $live_site);
		$this->assignRef('pane'			, $pane);
		$this->assignRef('events'		, $events);
		$this->assignRef('venue'		, $venue);
		$this->assignRef('category'		, $category);
		
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
					echo JAdminMenus::ImageCheck( $image, '/components/com_eventlist/assets/images/', NULL, NULL, $text ); 
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}
?>