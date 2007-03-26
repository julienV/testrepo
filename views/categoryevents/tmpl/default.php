<?php 
/**
 * @version 0.9 $Id$
 * @package Joomla 
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<p class="buttons">
	<?php
		if ( !$this->params->get( 'popup' ) ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->dellink, $this->params, 'categoryevents');
			echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params, $this->task, $this->category->id );
		endif;
		
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</p>

<?php if ($this->params->get('page_title')) : ?>

    <h1 class='componentheading'>
		<?php
		echo $this->params->get('header').' - '.$this->category->catname;
		?>
	</h1>
	
<?php endif; ?>

<div class="clear"></div>

<!--<div class="categoryevents">--> 

	<?php if ($this->category->image != '') : ?>
		<img class="flyer" src="<?php echo $this->live_site."/images/stories/".$this->category->image ; ?>" name="image" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>" border="0" alt="<?php echo $this->category->catname; ?>" />
	<?php else :
		echo "&nbsp;";
		endif;
	?>

	<p class="description">
		<?php echo $this->catdescription; ?>
	</p>

<!--</div>-->

<div class="clear"></div>

<br />
		
<!--table-->

<?php echo $this->loadTemplate('table'); ?>

<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="view" value="categoryevents" />
<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
<input type="hidden" name="categid" value="<?php echo $this->categid; ?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->item->id;?>" />
</form>

<!--pagination-->

<?php if (( $this->page > 0 ) && ( !$this->params->get( 'popup' ) )) : ?>
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