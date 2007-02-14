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
 * View class for the EventList CSS edit screen
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEditcss extends JView {
	
	function display($tpl = null) {
		
		global $mainframe;
		
		$document	= & JFactory::getDocument();
		
		$document->addStyleSheet('components/com_eventlist/assets/css/eventlistbackend.css');
		
		JMenuBar::title( JText::_( 'EDITCSS' ), 'cssedit' );
		JMenuBar::apply( 'applycss' );
		JMenuBar::spacer();
		JMenuBar::save( 'savecss' );
		JMenuBar::spacer();
		JMenuBar::cancel();
		JMenuBar::spacer();
		JMenuBar::help( 'el.editcss', true );
		
		/*
		 * Initialize some variables
		 */
		$option		= JRequest::getVar('option');
		$filename	= 'eventlist.css';
		$path		= JPATH_SITE.'/components/com_eventlist/assets/css/';
		$css_path 	= $path . $filename;
		$live_site	 = $mainframe->getCfg('live_site');
		
		jimport('joomla.filesystem.file');
		$content = JFile::read($path.$filename);
		
		if ($content !== false) 
		{
			$content = htmlspecialchars($content);
		}
		else 
		{
			$msg = sprintf(JText::_('Operation Failed Could not open'), $path.$filename);
			$mainframe->redirect('index.php?option='.$option, $msg);
		}
		
		$this->assignRef('css_path'		, $css_path);
		$this->assignRef('live_site'	, $live_site);
		$this->assignRef('content'		, $content);
		$this->assignRef('filename'		, $filename);
		$this->assignRef('path'			, $path);
		
		parent::display();
	}
}