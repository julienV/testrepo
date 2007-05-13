<?php
/**
 * @version 0.9 $Id: default.php 119 2007-05-03 22:53:47Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="eventlist" class="el_catview">
<span class="buttons">
	<?php
		echo ELOutput::submitbutton( $this->dellink, $this->params, 'categoriesview' );
		echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params, $this->task );
	?>
</span>

<?php if ($this->params->get('page_title')) : ?>
	<h1 class="componentheading">
	<?php
	if ( $this->task == 'archive' ) :
		echo $this->params->get('header').' - '.JText::_( 'ARCHIVE' );
	else :
		echo $this->params->get('header');
	endif;
	?>
	</h1>
<?php endif; ?>

<?php foreach ($this->rows as $category) :
?>

<div class="floattext">
	<h2 class="eventlist">
		<?php echo $category->catname; ?>
	</h2>

	<div class="catimg">
	  	<?php
		if ($category->image != '') :
			if ($this->task == 'archive') :
				echo '<a href="'.JRoute::_('index.php?view=category&layout=default&task=catarchive&cid='.$category->slug).'"><img src="images/stories/'.$category->image.'" width="'.$this->elsettings->imagewidth.'" height="'.$this->elsettings->imagehight.'" border="0" alt="'.$category->catname."' /></a>";
			else :
				echo "<a href='".JRoute::_('index.php?view=category&layout=default&cid='.$category->slug)."'><img src='images/stories/".$category->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$category->catname."' /></a>";
			endif;
		else :
			echo JHTML::_('image.site', 'noimage.png', '/components/com_eventlist/assets/images/', NULL, NULL, $category->catname );
		endif;
		?>
		<p>
			<?php
			echo JText::_( 'EVENTS' ).': ';
			if ($this->task == 'archive') :
				echo '<a href="'.JRoute::_('index.php?view=category&layout=default&task=catarchive&cid='.$category->slug).'">'.$category->assignedevents.'</a>';
			else :
				echo '<a href="'.JRoute::_('index.php?view=category&layout=default&cid='.$category->slug).'">'.$category->assignedevents.'</a>';
			endif;
			?>
		</p>
	</div>

	<div class="catdescription"><?php echo $category->catdescription ; ?>
	<p>
		<?php
		if ($this->task == 'archive') :
			echo "<a href='".JRoute::_('index.php?view=category&layout=default&task=catarchive&cid='.$category->slug)."'>".JText::_( 'SHOW ARCHIVE' )."</a>";
		else :
			echo "<a href='".JRoute::_('index.php?view=category&layout=default&cid='.$category->slug)."'>".JText::_( 'SHOW EVENTS' )."</a>";
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