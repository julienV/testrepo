<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
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
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="eventlist" class="el_categoriesview">
<p class="buttons">
	<?php
		echo ELOutput::submitbutton( $this->dellink, $this->params, 'categoriesview' );
		echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params, $this->task );
	?>
</p>

<?php if ($this->params->def( 'show_page_title', 1 )) : ?>
	<h1 class="componentheading">
	<?php
	if ( $this->task == 'archive' ) :
		echo $this->params->get('page_title').' - '.JText::_( 'ARCHIVE' );
	else :
		echo $this->params->get('page_title');
	endif;
	?>
	</h1>
<?php endif; ?>

<?php foreach ($this->rows as $row) : ?>

<div class="floattext">
	<h2 class="eventlist cat<?php echo $row->id; ?>">
		<?php echo $row->catname; ?>
	</h2>

	<div class="catimg">
	  	<?php
		if ($row->image != '') :
			if ($this->task == 'archive') :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&task=catarchive&id='.$row->slug)."'><img src='images/stories/".$row->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$row->catname."' /></a>";
			else :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&id='.$row->slug)."'><img src='images/stories/".$row->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$row->catname."' /></a>";
			endif;
		else :
			echo JHTML::_('image.site', 'noimage.png', '/components/com_eventlist/assets/images/', NULL, NULL, $row->catname );
		endif;
		?>
		<p>
			<?php
			echo JText::_( 'EVENTS' ).': ';
			if ($this->task == 'archive') :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&task=catarchive&id='.$row->slug)."'>". $row->assignedevents."</a>";
			else :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&sid='.$row->slug)."'>". $row->assignedevents."</a>";
			endif;
			?>
		</p>
	</div>

	<div class="catdescription cat<?php echo $row->id; ?>"><?php echo $row->catdescription ; ?>
	<p>
		<?php
		if ($this->task == 'archive') :
			echo "<a href='".JRoute::_('index.php?view=categoryevents&task=catarchive&id='.$row->slug)."'>".JText::_( 'SHOW ARCHIVE' )."</a>";
		else :
			echo "<a href='".JRoute::_('index.php?view=categoryevents&id='.$row->slug)."'>".JText::_( 'SHOW EVENTS' )."</a>";
		endif;
		?>
	</p>
	</div>

</div>
<?php endforeach; ?>

<!--pagination-->

<?php if (( $this->page > 0 ) ) : ?>

<p class="pageslinks">
	<?php echo $this->pageNav->getPagesLinks($this->link); ?>
</p>

<p class="pagescounter">
	<?php echo $this->pageNav->getPagesCounter(); ?>
</p>

<?php endif; ?>

<!--copyright-->

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>
</div>