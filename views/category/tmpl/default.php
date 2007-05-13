<?php
/**
 * @version 0.9 $Id: default.php 35 2007-03-26 21:41:46Z schlu $
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

<div class="catimg">

	<?php if ($this->category->image != '') : ?>
		<img src="images/stories/<?php echo $this->category->image; ?>" name="image" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>" border="0" alt="<?php echo $this->category->catname; ?>" />
	<?php else :
		echo "&nbsp;";
		endif;
	?>
</div>

<div class="catdescription">
		<?php echo $this->catdescription; ?>
</div>

<div class="clear"></div>

<br />

<!--table-->

<?php echo $this->loadTemplate('table'); ?>


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