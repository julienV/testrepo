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

defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div id="eventlist" class="el_categoriesdetailed">
<p class="buttons">
	<?php
		if ( !$this->params->get( 'popup' ) ) : //don't show in printpopup
			echo ELOutput::submitbutton( $this->dellink, $this->params, 'categoriesdetailed' );
			echo ELOutput::archivebutton( $this->elsettings->oldevent, $this->params );
		endif;
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</p>

<?php if ($this->params->get('show_page_title')) : ?>

<h1 class="componentheading">
<?php echo $this->params->get('page_title'); ?>
</h1>

<?php endif;
foreach($this->categories as $category) :
?>
	<h2 class="eventlist cat<?php echo $category->id; ?>">
		<?php
    		echo $category->catname;
    	?>
	</h2>

<div class="cat<?php echo $category->id; ?> floattext">

	<div class="catimg">
	  	<?php
		if ($category->image != '') :
				echo "<a href='".JRoute::_('index.php?view=categoryevents&id='.$category->slug)."'><img src=".$this->baseurl."'/images/stories/".$category->image."' width='".$this->elsettings->imagewidth."' height='".$this->elsettings->imagehight."' border='0' alt='".$category->catname."' /></a>";
		else :
			echo JHTML::_('image.site', 'noimage.png', '/components/com_eventlist/assets/images/', NULL, NULL, $category->catname );
		endif;
		?>
		<p>
			<?php
			echo JText::_( 'EVENTS' ).': ';
				echo "<a href='".JRoute::_('index.php?view=categoryevents&id='.$category->slug)."'>". $category->assignedevents."</a>";
			?>
		</p>
	</div>

	<div class="catdescription"><?php echo $category->catdescription; ?>
		<p>
			<?php
				echo "<a href='".JRoute::_('index.php?view=categoryevents&id='.$category->slug)."'>".JText::_( 'SHOW EVENTS' )."</a>";
			?>
		</p>
	</div>
	<br class="clear" />

</div>

<!--table-->
<?php
//TODO move out of template
$this->rows		= & $this->model->getEventdata( $category->id );
$this->categoryid = $category->id;

echo $this->loadTemplate('table');

endforeach;
?>

<!--pagination-->

<?php if (( $this->page > 0 ) ) : ?>
<div class="pageslinks">
	<?php echo $this->pageNav->getPagesLinks($this->link); ?>
</div>

<p class="pagescounter">
	<?php echo $this->pageNav->getPagesCounter(); ?>
</p>

<?php endif; ?>

<!--copyright-->

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>
</div>