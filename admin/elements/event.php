<?php
/**
 * @version 0.9 $Id$
 * @package Joomla 
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

/**
 * Renders an Event element
 *
 * @package Joomla 
 * @subpackage EventList
 * @since 0.9
 */

class JElement_Event extends JElement
{
   /**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Title';

	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;

		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$url 		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		$fieldName	= $control_name.'['.$name.']';
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eventlist'.DS.'tables');
		
		$event =& JTable::getInstance('eventlist_events', '');
		if ($value) {
			$event->load($value);
		} else {
			$event->title = JText::_('SELECTEVENT');
		}

		$js = "
		function elSelectEvent(id, title) {
			document.getElementById('a_id').value = id;
			document.getElementById('a_name').value = title;
			document.popup.hide();
		}";

		$link = 'index.php?option=com_eventlist&amp;view=eventelement&amp;tmpl=component';
		$doc->addScriptDeclaration($js);
		$doc->addScript($url.'includes/js/joomla/modal.js');
		$doc->addStyleSheet($url.'includes/js/joomla/modal.css');
		$html = "\n<div style=\"float: left;\"><input style=\"background: #ffffff;\" type=\"text\" id=\"a_name\" value=\"$event->title\" disabled=\"disabled\" /></div>";
		$html .= "\n &nbsp; <input class=\"inputbox\" type=\"button\" onclick=\"document.popup.show('$link', 650, 375, null);\" value=\"".JText::_('Select')."\" />";
		$html .= "\n<input type=\"hidden\" id=\"a_id\" name=\"$fieldName\" value=\"$value\" />";

		return $html;
	}
}
?>