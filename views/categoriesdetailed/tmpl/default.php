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

<div id="eventlist" class="el_catdetails">
<span class="buttons">
	<?php
		if ( !$this->params->get( 'popup' ) ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->dellink, $this->params, 'categoriesdetailed' );
			echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params );
		endif;
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</span>

<?php if ($this->params->get('show_page_title')) : ?>

	<h1 class="componentheading">
	<?php echo $this->params->get('page_title'); ?>
	</h1>

<?php endif;
foreach($this->categories as $category) :
?>
	<div class="floattext">
	<h2 class="eventlist">
		<?php
    		echo $category->catname;
    	?>
	</h2>

	<div class="catimg">
	  	<?php
		if ($category->image != '') :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&categid='.$category->slug)."'><img src='".$this->live_site."/images/stories/".$category->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$category->catname."' /></a>";
		else :
			echo JHTML::_('image.site', 'noimage.png', '/components/com_eventlist/assets/images/', NULL, NULL, $category->catname );
		endif;
		?>
		<p>
			<?php
			echo JText::_( 'EVENTS' ).': ';
				echo "<a href='".JRoute::_('index.php?view=categoryevents&categid='.$category->slug)."'>". $category->assignedevents."</a>";
			?>
		</p>
	</div>

	<div class="catdescription"><?php echo $category->catdescription ; ?>
		<p>
			<?php
				echo "<a href='".JRoute::_('index.php?view=categoryevents&categid='.$category->slug)."'>".JText::_( 'SHOW EVENTS' )."</a>";
			?>
		</p>
	</div>

</div>
<!--table-->
<br />
<?php
//TODO move out of template
$this->rows		= & $this->model->getEventdata( $category->id );

echo $this->loadTemplate('table');

endforeach;
?>
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