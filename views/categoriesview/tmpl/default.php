<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="eventlist" class="el_catview">
<span class="buttons">
	<?php
		echo ELOutput::submitbutton( $this->dellink, $this->params, 'categoriesview' );
		echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params, $this->task );
	?>
</span>

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
	<h2 class="eventlist">
		<?php echo $row->catname; ?>
	</h2>

	<div class="catimg">
	  	<?php
		if ($row->image != '') :
			if ($this->task == 'archive') :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&task=catarchive&categid='.$row->slug)."'><img src='images/stories/".$row->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$row->catname."' /></a>";
			else :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&categid='.$row->slug)."'><img src='images/stories/".$row->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$row->catname."' /></a>";
			endif;
		else :
			echo JHTML::_('image.site', 'noimage.png', '/components/com_eventlist/assets/images/', NULL, NULL, $row->catname );
		endif;
		?>
		<p>
			<?php
			echo JText::_( 'EVENTS' ).': ';
			if ($this->task == 'archive') :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&task=catarchive&categid='.$row->slug)."'>". $row->assignedevents."</a>";
			else :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&categid='.$row->slug)."'>". $row->assignedevents."</a>";
			endif;
			?>
		</p>
	</div>

	<div class="catdescription"><?php echo $row->catdescription ; ?>
	<p>
		<?php
		if ($this->task == 'archive') :
			echo "<a href='".JRoute::_('index.php?view=categoryevents&task=catarchive&categid='.$row->slug)."'>".JText::_( 'SHOW ARCHIVE' )."</a>";
		else :
			echo "<a href='".JRoute::_('index.php?view=categoryevents&categid='.$row->slug)."'>".JText::_( 'SHOW EVENTS' )."</a>";
		endif;
		?>
	</p>
	</div>

</div>
<?php endforeach; ?>
</div>

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