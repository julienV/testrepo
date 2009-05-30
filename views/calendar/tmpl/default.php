<?php
/**
 * @version 1.1 $Id$
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
    $countcatevents = array ();
    
    foreach ($this->rows as $row) :
    
        //get event date
        $year = strftime('%Y', strtotime($row->dates));
        $month = strftime('%m', strtotime($row->dates));
        $day = strftime('%d', strtotime($row->dates));
    
        //for time printing
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

        //initialize variables
        $multicatname = '';
        $colorpic = '';
        $nr = count($row->categories);
		$ix = 0;
		$content = '';
		$contentend = '';
		
		//walk through categories assigned to an event
        foreach($row->categories AS $category) :
        
        	//Currently only one id possible...so simply just pick one up...
        	$detaillink 	= JRoute::_('index.php?view=details&cid='.$category->catslug.'&id='.$row->slug);
			
        	//wrap a div for each category around the event for show hide toggler
        	$content 		.= '<div class="cat'.$category->id.'">';
        	$contentend		.= '</div>';
        	
        	//attach category color if any in front of the catname
        	if ($category->color):
        		$multicatname .= '<span class="colorpic" style="background-color: '.$category->color.';"></span>'.$category->catname;
        	else:
				$multicatname 	.= $category->catname;
			endif;
			$ix++;
			if ($ix != $nr) :
				$multicatname .= ', ';
			endif;
			
			//attach category color if any in front of the event title in the calendar overview
			if ( isset ($category->color) && $category->color) :
          		$colorpic .= '<span class="colorpic" style="background-color: '.$category->color.';"></span>';
        	endif;
			
        	//count occurence of the category
       		if (!array_key_exists($category->id, $countcatevents)) :
				$countcatevents[$category->id] = 1;
        	else :
            	$countcatevents[$category->id]++;
        	endif;

       	endforeach;
       	
       	$catname = '<div class="catname">'.$multicatname.'</div>';
       	
        $eventdate = ELOutput::formatdate($row->dates, $row->times);
    
        //venue
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
        
		//generate the output
		$content .= $colorpic;       
		$content .= $this->caltooltip($catname.$eventname.$timehtml.$venue, $eventdate, $row->title, $detaillink, 'eventTip');
       	$content	.= $contentend;
    
        $this->cal->setEventContent($year, $month, $day, $content);
        
	endforeach;
	
    // print the calendar
    print ($this->cal->showMonth());
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
	
	$counter = array();
	
	//walk through events
	foreach ($this->rows as $row):
		
		//walk through the event categories
    	foreach ($row->categories as $cat) :
    	
    		//sort out dupes
    		if(!in_array($cat->id, $counter)):
    	
    			//add cat id to cat counter
    			$counter[] = $cat->id;
    		
    			//build legend
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
				endif;
			
			endif;
						
    	endforeach;
    	
    endforeach;
	endif;
    ?>
</div>

<div class="clr"/></div>