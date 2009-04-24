<?php
/**
 * @version 1.1 $Id: default.php 668 2008-05-12 14:32:13Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
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

// no direct access
defined('_JEXEC') or die ('Restricted access');
?>
<div id="eventlist" class="jlcalendar">
    <?php if ($this->params->def('show_page_title', 1)): ?>
    	<h1 class="componentheading">
        	<?php echo $this->escape($this->pagetitle); ?>
    	</h1>
    <?php endif; ?>
	
<?php
    //TODO: move to a helper
    /**
     * Creates a tooltip
     *
     * @access  public
     * @param string  $tooltip The tip string
     * @param string  $title The title of the tooltip
     * @param string  $text The text for the tip
     * @param string  $href An URL that will be used to create the link
     * @param string  $class the class to use for tip.
     * @return  string
     * @since 1.5
     */
    function caltooltip($tooltip, $title = '', $text = '', $href = '', $class = 'editlinktip hasTip')
    {
    
        //$tooltip  = addslashes(htmlspecialchars($tooltip));
        //$title    = addslashes(htmlspecialchars($title));
        $tooltip = (htmlspecialchars($tooltip));
        $title = (htmlspecialchars($title));
    
        //$text   = JText::_( $text, true );
    
        if ($title) {
            $title = $title.'::';
        }
    
        if ($href) {
            $href = JRoute::_($href);
            $style = '';
            $tip = '<span class="'.$class.'" title="'.$title.$tooltip.'"><a href="'.$href.'">'.$text.'</a></span>';
        } else {
            $tip = '<span class="'.$class.'" title="'.$title.$tooltip.'">'.$text.'</span>';
        }
    
        return $tip;
    }
    
    require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'calendar.class.php');
    
    $app = & JFactory::getApplication();
    
    $cal = new ELCalendar($this->year, $this->month, 0, $app->getCfg('offset'));
    $cal->enableMonthNav('index.php?view=calendar&id='.$this->category->slug);
    $cal->setFirstWeekDay($this->params->get('firstweekday', 1));
    $cal->enableDayLinks(false);
    
    $countcatevents = array ();
    
    foreach ($this->rows as $row)
    {
        // get event date
        $year = strftime('%Y', strtotime($row->dates));
        $month = strftime('%m', strtotime($row->dates));
        $day = strftime('%d', strtotime($row->dates));
    
        // for time printing
        $timehtml = '';
        
		if ($this->elsettings->showtime == 1)
        {
            $start = ELOutput::formattime($row->dates, $row->times);
            $end = ELOutput::formattime($row->dates, $row->endtimes);
            
			if ($start != '') {
                $timehtml = '<div class="time"><span class="label">'.JTEXT::_('Time').': </span>';
                $timehtml .= $start;
                if ($end != '') {
                    $timehtml .= ' - '.$end;
                }
                $timehtml .= '</div>';
            }
        }
    
        //Link to details
        $detaillink = JRoute::_('index.php?view=details&cid='.$this->category->slug.'&id='.$row->slug);
        $eventname = '<div class="eventName">'.$this->escape($row->title).'</div>';
        
		if ($row->color) {
            $catname = '<div class="catname"><span class="colorpic" style="background-color: '.$row->color.';"></span>'.$row->catname.'</div>';
        } else {
            $catname = '<div class="catname">'.$row->catname.'</div>';
        }
        $eventdate = ELOutput::formatdate($row->dates, $row->times);
    
        // venue
        if ($this->elsettings->showlocate == 1) {
            $venue = '<div class="location"><span class="label">'.JTEXT::_('Venue').': </span>';
            
			if ($this->elsettings->showlinkvenue == 1 && 0) {
                $venue .= $row->locid != 0 ? "<a href='".JRoute::_('index.php?view=venueevents&id='.$row->venueslug)."'>".$this->escape($row->venue)."</a>" : '-';
            } else {
             	$venue .= $row->locid ? $this->escape($row->venue) : '-';
            }
                $venue .= '</div>';
				
        } else {
			$venue = '';
		}
        
		$content = '<div class="cat'.$row->catid.'">';
		//TODO: add color field to categories table
		if ( isset ($row->color) && $row->color) {
          	$content .= '<span class="colorpic" style="background-color: '.$row->color.';"></span>';
        }
        
		$content .= caltooltip($catname.$eventname.$timehtml.$venue, $eventdate, $row->title, $detaillink, 'eventTip');
    
        $content .= '</div>';
    
        $cal->setEventContent($year, $month, $day, $content);
                
		if (!array_key_exists($row->catid, $countcatevents)) {
			$countcatevents[$row->catid] = 1;
        } else {
            $countcatevents[$row->catid]++;
        }
	}
    // print the calendar
    print ($cal->showMonth());
?>
</div>

<div id="jlcalendarlegend">
	
    <div id="buttonshowall">
        <?php echo JText::_('SHOWALL'); ?>
    </div>
	
    <div id="buttonhideall">
        <?php echo JText::_('HIDEALL'); ?>
    </div>
	
    <?php
    //print the legend
    foreach ($this->categories as $cat)
    {
        if (array_key_exists($cat->id, $countcatevents)):
    	?>
    		<div class="eventCat" catid="<?php echo $cat->id; ?>">
        		<?php
        		if ( isset ($cat->color) && $cat->color) {
            		echo '<span class="colorpic" style="background-color: '.$cat->color.';"></span>';
        		}
        		echo $cat->catname.' ('.$countcatevents[$cat->id].')';
        		?>
    		</div>
    	<?php endif;
    }
    ?>
</div>

<div class="clr"/>