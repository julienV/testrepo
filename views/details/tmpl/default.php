<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license GNU/GPL, see LICENCE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

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

<p class="buttons">
		<?php echo ELOutput::mailbutton( $this->params ); ?>
		<?php echo ELOutput::printbutton( $this->print_link, $this->params ); ?>
</p>

<?php if ($this->params->def( 'show_page_title', 1 )) : ?>

	<h1 class="componentheading floattext">

		<?php echo $this->params->get('page_title'); ?>

	</h1>

<?php endif; ?>

<!-- Details EVENT -->
<div id="eventlist" class="event_id<?php echo $this->row->did; ?> el_details">
	<h2 class="eventlist">
		<?php
    	echo JText::_( 'EVENT' );
    	echo ELOutput::editbutton($this->item->id, $this->row->did, $this->params, $this->allowedtoeditevent, 'editevent' );
    	?>
	</h2>

	<?php //flyer
	echo ELOutput::flyer( $this->row, $this->elsettings, $this->dimage, 'event' );
	?>

	<dl class="event_info floattext">

		<?php if ($this->elsettings->showdetailstitle == 1) : ?>
			<dt class="title"><?php echo JText::_( 'TITLE' ).':'; ?></dt>
    		<dd class="title"><?php echo $this->row->title; ?></dd>
		<?php
  		endif;
  		?>
  		<dt class="when"><?php echo JText::_( 'WHEN' ).':'; ?></dt>
		<dd class="when">
			<?php
			echo $this->displaydate;
			echo $this->displaytime;
			?>
		</dd>
  		<?php
  		if ($this->row->locid != 0) :
  		?>
		    <dt class="where"><?php echo JText::_( 'WHERE' ).':'; ?></dt>
		    <dd class="where">
    		<?php if (($this->elsettings->showdetlinkvenue == 1) && (!empty($this->row->url))) : ?>

			    <a href="<?php echo $this->row->url; ?>" target="_blank"> <?php echo $this->row->venue; ?></a>

			<?php elseif ($this->elsettings->showdetlinkvenue == 2) : ?>

			    <a href="<?php echo JRoute::_( 'index.php?view=venueevents&locatid='.$this->row->venueslug ); ?>"><?php echo $this->row->venue; ?></a>

			<?php elseif ($this->elsettings->showdetlinkvenue == 0) :

				echo $this->row->venue;

			endif;

			?> - <?php echo $this->row->city; ?>

			</dd>

		<?php endif; ?>

		<dt class="category"><?php echo JText::_( 'CATEGORY' ).':'; ?></dt>
    		<dd class="category">
				<?php echo "<a href='".JRoute::_( 'index.php?view=categoryevents&categid='.$this->row->categoryslug )."'>".$this->row->catname."</a>";?>
			</dd>
	</dl>


  	<?php if ($this->elsettings->showevdescription == 1) : ?>

  	    <h2 class="description"><?php echo JText::_( 'DESCRIPTION' ); ?></h2>
  		<div class="description">
  			<?php echo $this->eventdescription; ?>
  		</div>

  	<?php endif; ?>

<!--  	Venue  -->

	<?php if ($this->row->locid != 0) : ?>

		<h2 class="location">
			<?php echo JText::_( 'VENUE' ) ; ?>
  			<?php echo ELOutput::editbutton($this->item->id, $this->row->locid, $this->params, $this->allowedtoeditvenue, 'editvenue' ); ?>
		</h2>

		<?php //flyer
		echo ELOutput::flyer( $this->row, $this->elsettings, $this->limage );
		echo ELOutput::mapicon( $this->row, $this->elsettings );
		?>

		<dl class="location floattext">
			 <dt class="venue"><?php echo $this->elsettings->locationname.':'; ?></dt>
				<dd class="venue">
				<?php echo "<a href='".JRoute::_( 'index.php?view=venueevents&locatid='.$this->row->venueslug )."'>".$this->row->venue."</a>"; ?>

				<?php if (!empty($this->row->url)) : ?>
					&nbsp; - &nbsp;
					<a href="<?php echo $this->row->url; ?>" target="_blank"> <?php echo JText::_( 'WEBSITE' ); ?></a>
				<?php
				endif;
				?>
				</dd>

			<?php
  			if ( $this->elsettings->showdetailsadress == 1 ) :
  			?>

  				<?php if ( $this->row->street ) : ?>
  				<dt class="venue_street"><?php echo JText::_( 'STREET' ).':'; ?></dt>
				<dd class="venue_street">
    				<?php echo $this->row->street; ?>
				</dd>
				<?php endif; ?>

				<?php if ( $this->row->plz ) : ?>
  				<dt class="venue_plz"><?php echo JText::_( 'ZIP' ).':'; ?></dt>
				<dd class="venue_plz">
    				<?php echo $this->row->plz; ?>
				</dd>
				<?php endif; ?>

				<?php if ( $this->row->city ) : ?>
    			<dt class="venue_city"><?php echo JText::_( 'CITY' ).':'; ?></dt>
    			<dd class="venue_city">
    				<?php echo $this->row->city; ?>
    			</dd>
    			<?php endif; ?>

    			<?php if ( $this->row->state ) : ?>
    			<dt class="venue_state"><?php echo JText::_( 'STATE' ).':'; ?></dt>
    			<dd class="venue_state">
    				<?php echo $this->row->state; ?>
    			</dd>
				<?php endif; ?>

				<?php if ( $this->row->country ) : ?>
				<dt class="venue_country"><?php echo JText::_( 'COUNTRY' ).':'; ?></dt>
    			<dd class="venue_country">
    				<?php echo $this->row->countryimg ? $this->row->countryimg : $this->row->country; ?>
    			</dd>
    			<?php endif; ?>
			<?php
			endif;
			?>
		</dl>

		<?php if ($this->elsettings->showlocdescription == 1) :	?>

			<h2 class="location_desc"><?php echo JText::_( 'DESCRIPTION' ); ?></h2>
  			<div class="location_desc">
  				<?php echo $this->venuedescription;	?>
  			</div>

		<?php endif; ?>

	<?php endif; //row->locid !=0 end ?>

	<?php if ($this->row->registra == 1) : ?>

		<!-- Registration -->
		<?php echo $this->loadTemplate('attendees'); ?>

	<?php endif; ?>

</div>

<p class="copyright">
	<?php echo ELOutput::footer( ); ?>
</p>