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
    require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'calendar.class.php');
    
    $app = & JFactory::getApplication();
    
    $cal = new ELCalendar($this->year, $this->month, 0, $app->getCfg('offset'));
    $cal->enableMonthNav('index.php?view=calendar');
    $cal->setFirstWeekDay($this->params->get('firstweekday', 1));
    $cal->enableDayLinks(false);
    
    $countcatevents = array ();
    
    foreach ($this->rows as $row) :
    
        // get event date
        $year = strftime('%Y', strtotime($row->dates));
        $month = strftime('%m', strtotime($row->dates));
        $day = strftime('%d', strtotime($row->dates));
    
        // for time printing
        $timehtml = '';
        
		if ($this->elsettings->showtime == 1) :

            $start = ELOutput::formattime($row->dates, $row->times);
            $end = ELOutput::formattime($row->dates, $row->endtimes);
            
			if ($start != '') :
                $timehtml = '<div class="time"><span class="label">'.JTEXT::_('Time').': </span>';
                $timehtml .= $start;
                if ($end != '') :
                    $timehtml .= ' - '.$end;
                endif;
                $timehtml .= '</div>';
            endif;
        endif;
    
        $eventname = '<div class="eventName">'.$this->escape($row->title).'</div>';

        $multicatname = '';
        $nr = count($row->categories);
		$ix = 0; 
        foreach($row->categories AS $category) :
        	//TODO: currently only one id possible...so simply just pick one up...
        	$detaillink 	= JRoute::_('index.php?view=details&cid='.$category->catslug.'&id='.$row->slug);
        	$catid			= $category->id;
        	
        	$catcolor 		= $category->color;
        	if ($catcolor):
        		$multicatname .= '<span class="colorpic" style="background-color: '.$catcolor.';"></span>'.$category->catname;
        	else:
				$multicatname 	.= $category->catname;
			endif;
			$ix++;
			if ($ix != $nr) :
				$multicatname .= ', ';
			endif;

       	endforeach;
       	
       	//TODO improve: assign catid, only one id for multiassign events
       	if (!array_key_exists($catid, $countcatevents)) :
			$countcatevents[$catid] = 1;
        else :
            $countcatevents[$catid]++;
        endif;
       	
       	$catname = '<div class="catname">'.$multicatname.'</div>';
       	
        $eventdate = ELOutput::formatdate($row->dates, $row->times);
    
        // venue
        if ($this->elsettings->showlocate == 1) :
            $venue = '<div class="location"><span class="label">'.JText::_('VENUE').': </span>';
            
			if ($this->elsettings->showlinkvenue == 1 && 0) :
                $venue .= $row->locid != 0 ? "<a href='".JRoute::_('index.php?view=venueevents&id='.$row->venueslug)."'>".$this->escape($row->venue)."</a>" : '-';
           	else :
             	$venue .= $row->locid ? $this->escape($row->venue) : '-';
            endif;
                $venue .= '</div>';
        else:
			$venue = '';
		endif;
        
		$content = '<div class="cat'.$catid.'">';

		if ( isset ($category->color) && $category->color && $nr == 1) :
          	$content .= '<span class="colorpic" style="background-color: '.$category->color.';"></span>';
        endif;
        
		
		$content .= $this->caltooltip($catname.$eventname.$timehtml.$venue, $eventdate, $row->title, $detaillink, 'eventTip');
    
        $content .= '</div>';
    
        $cal->setEventContent($year, $month, $day, $content);
        
	endforeach;
		
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
	if($this->params->get('displayLegend')) :
	var_dump($countcatevents);
	foreach ($this->rows as $row):
    	//TODO: ugly see above comment when reworking
		$catsreversed = array_reverse($row->categories);

    	foreach ($catsreversed as $cat) :
    		
        	if (array_key_exists($cat->id, $countcatevents)):
    		?>
    			<div class="eventCat" catid="<?php echo $cat->id; ?>">
        			<?php
        			if ( isset ($cat->color) && $cat->color) :
            			echo '<span class="colorpic" style="background-color: '.$cat->color.';"></span>';
        			endif;
        			echo $cat->catname.' ('.$countcatevents[$cat->id].')';
        			?>
    			</div>
    		<?php
    		//stop after first match, can't support multiassign cats currently
    		break;
    		
			endif;
			
    	endforeach;
    	
    endforeach;
	endif;
    ?>
</div>

<div class="clr"/></div>