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
class EventListViewHelp extends JView {

	function display($tpl = null) {

		global $mainframe;

		$document		= & JFactory::getDocument();
		$lang 			= & JFactory::getLanguage();
		$uri 			= & JFactory::getURI();

		JMenuBar::title( JText::_( 'HELP' ), 'help' );
		JMenuBar::back();

		$live_site	 	= $mainframe->getCfg('live_site');

		jimport('joomla.html.pane');
		$pane 			= & JPane::getInstance('sliders');

		$submenu = ELAdmin::submenu();
		$document->setBuffer($submenu, 'module', 'submenu');

		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');

		// Check for files in the actual language
		$langTag = $lang->getTag();

		jimport( 'joomla.filesystem.folder' );

		if( !JFolder::exists( JPATH_SITE . DS.'administrator'.DS.'components'.DS.'com_eventlist/help'.DS .$langTag ) ) {
			$langTag = 'en-GB';		// use english as fallback
		}

		$request_url = $uri->toString();

		$helpsearch = JRequest::getVar( 'search' );
		$toc 		= EventListViewHelp::getHelpToc( $helpsearch );

		$this->assignRef('pane'			, $pane);
		$this->assignRef('langTag'		, $langTag);
		$this->assignRef('live_site'	, $live_site);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('helpsearch'	, $helpsearch);
		$this->assignRef('toc'			, $toc);

		parent::display();
	}

	/**
 	* Compiles the help table of contents
 	* Based on the Joomla admin component
 	*
 	* @param string A specific keyword on which to filter the resulting list
 	*/
	function getHelpTOC( $helpsearch )
	{
		global $mainframe;

		$lang =& JFactory::getLanguage();
		jimport( 'joomla.filesystem.folder' );

		// Check for files in the actual language
		$langTag = $lang->getTag();
		if( !JFolder::exists( JPATH_SITE . DS.'administrator'.DS.'components'.DS.'com_eventlist'.DS.'help'.DS .$langTag ) ) {
			$langTag = 'en-GB';		// use english as fallback
		}
		$files = JFolder::files( JPATH_SITE . DS.'administrator'.DS.'components'.DS.'com_eventlist'.DS.'help'.DS.$langTag, '\.xml$|\.html$' );

		$toc = array();
		foreach ($files as $file) {
			$buffer = file_get_contents( JPATH_SITE . DS.'administrator'.DS.'components'.DS.'com_eventlist'.DS.'help'.DS.$langTag.DS.$file );
			if (preg_match( '#<title>(.*?)</title>#', $buffer, $m )) {
				$title = trim( $m[1] );
				if ($title) {
					if ($helpsearch) {
						if (JString::strpos( strip_tags( $buffer ), $helpsearch ) !== false) {
							$toc[$file] = $title;
						}
					} else {
						$toc[$file] = $title;
					}
				}
			}
		}
		asort( $toc );
		return $toc;
	}
}
?>