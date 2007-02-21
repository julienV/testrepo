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
		if ( !$this->pop ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->dellink, $this->params, 'eventlist' );
			echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params );
		endif;
		
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</p>

<?php if ($this->params->get('page_title')) : ?>

    <h1 class='componentheading'>
		<?php
		echo $this->params->get('header');
		?>
	</h1>
	
<?php endif; ?>

<div class="clear"></div>

<?php if ($this->params->get('showintrotext') == 1) : ?>
	<p class="description">
		<?php echo $this->params->get('introtext'); ?>
	</p>
<?php endif; ?>

<br />

<!--table-->

<?php echo $this->loadTemplate('table'); ?>

<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
</form>

<!--footer-->

<?php if (( $this->page > 0 ) && ( !$this->pop )) : ?>
<p class="pageslinks">
	<?php echo $this->pageNav->getPagesLinks($this->link); ?>
</p>

<p class="pagescounter">
	<?php echo $this->pageNav->getPagesCounter(); ?>
</p>
<?php endif; ?>

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>