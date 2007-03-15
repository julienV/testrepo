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

<span class="buttons">
	<?php
		if ( !$this->pop ) : //don't show in printpopup
			echo ELOutput::submitbutton( $Itemid, $this->dellink, $this->params, 'venueevents' );
		endif;
		
		echo ELOutput::printbutton( $this->print_link, $this->params );
	?>
</span>

<?php if ($this->params->get('page_title')) : ?>

	<h1 class='componentheading'>
		<?php echo $this->params->get('header'); ?>
	</h1>

<?php endif; ?>

<div id="eventlist" class="el_venue">
<!--Venue-->

<?php foreach($this->rows as $row) : ?>


	<h2 class="eventlist">
		<a href="<?php echo JRoute::_('index.php?option=com_eventlist&view=venueevents&locatid='.$row->id); ?>"><?php echo $row->club; ?></a>
	</h2>
	
			<?php			
			//cell for flyer
				if (!empty($row->locimage)) :
					if (file_exists(JPATH_SITE.'/images/eventlist/venues/small/'.$row->locimage)) :

						if ($this->elsettings->lightbox == 0) :
						?>
						<a class="flyer" href="javascript:void window.open('<?php echo $row->limage['originalloc']; ?>','Popup','width=<?php echo $row->limage['widthloc']; ?>,height=<?php echo $row->limage['heightloc']; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">
						<?php
						 else :
						?>
						<a class="flyer" href="<?php echo $row->limage['originalloc']; ?>" class="flyer" rel="lightbox" title="<?php echo $row->club; ?>">
						<?php  endif; ?>

						<img src="<?php echo $row->limage['thumbloc']; ?>" width="<?php echo $row->limage['thumbwidthloc']; ?>" height="<?php echo $row->limage['thumbheightloc']; ?>" alt="location image" />
						</a>
					<?php else : ?>
						<img src="<?php echo $row->limage['originalloc']; ?>" class="flyer" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>" />
					<?php
					endif;
				else :
					echo '';
				endif;


				//Link to map
				switch ($this->elsettings->showmapserv) :
					case 0:
					break;

					case 1:
  						if ($this->elsettings->map24id != '') :
						?>
							<a class="flyer" href="http://link2.map24.com/?lid=<?php echo $this->elsettings->map24id ?>&maptype=JAVA&width0=2000&street0=<?php echo $row->street ?>&zip0=<?php echo $row->plz ?>&city0=<?php echo $row->city ?>&country0=<?php echo $row->country ?>&sym0=10280&description0=<?php echo $row->club ?>" target="_blank">
							<img src="http://img.map24.com/map24/link2map24/de/show_address_7.gif" border=0 alt="Map24" />
							</a>
						<?php
						endif;
					break;

					case 2:
					?>
						<h3 class="anfahrt">XXXXAnfahrtXXXX</h3>
						<p class="map">
						<a href="http://maps.google.com/maps?q=<?php echo $row->street; ?>+<?php echo $row->city ?>+<?php echo $row->plz ?>" title="<?php echo JText::_( 'MAP' ); ?>" target="_blank"><?php echo JText::_( 'MAP' ); ?></a>
						</p>
				<?php
					break;
				endswitch; //switch ende
				?>
            <dl class="location floattext">
				<dt class="club_website"><?php echo JText::_( 'WEBSITE' ).':'; ?></dt>
   				<dd class="club_website">
				<?php
				if (($this->elsettings->showdetlinkclub == 1) && (!empty($row->url))) :
					if(strtolower(substr($row->url, 0, 7)) == "http://") :
          				// Wenn der Teilstring gleich "http://" ist,
           				// dann  Link ohne "http://" erzeugen
						?>
						<a href="<?php echo $row->url; ?>" target="_blank"> <?php echo $row->url; ?></a>
						<?php
					else :
						// Wenn nicht, dann "http://" so dazu
						?>
						<a href="http://<?php echo $row->url; ?>" target="_blank"> <?php echo $row->url; ?></a>
						<?php 
					endif; 
				else :
					echo JText::_( 'NO WEBSITE' );
				endif;
				?>
				</dd>
				
			<?php
  			if ( $this->elsettings->showdetailsadress == 1 ) :
  			?>
			
  			<?php if ( $row->street ) : ?>
  			<dt class="club_street"><?php echo JText::_( 'STREET' ).':'; ?></dt>
			<dd class="club_street">
    			<?php echo $row->street; ?>
			</dd>
			<?php endif; ?>

			<?php if ( $row->plz ) : ?>
  			<dt class="club_plz"><?php echo JText::_( 'ZIP' ).':'; ?></dt>
			<dd class="club_plz">
    			<?php echo $row->plz; ?>
			</dd>
			<?php endif; ?>
 
			<?php if ( $row->city ) : ?>
    		<dt class="club_city"><?php echo JText::_( 'CITY' ).':'; ?></dt>
    		<dd class="club_city">
    			<?php echo $row->city; ?>
    		</dd>
    		<?php endif; ?>

    		<?php if ( $row->state ) : ?>
			<dt class="club_state"><?php echo JText::_( 'STATE' ).':'; ?></dt>
			<dd class="club_state">
    			<?php echo $row->state; ?>
			</dd>
			<?php endif; ?>
 
			<?php if ( $row->country ) : ?>
			<dt class="club_country"><?php echo JText::_( 'COUNTRY' ).':'; ?></dt>
    		<dd class="club_country">
    			<?php echo $row->country; ?>
    		</dd>
    		<?php endif; ?>
    		
    		<dt class="club_assignedevents"><?php echo JText::_( 'EVENTS' ).':'; ?></dt>
    		<dd class="club_assignedevents">
    			<?php echo $row->assignedevents; ?>
    		</dd>
		<?php
		endif;
		?>

	<?php
  		if ($this->elsettings->showlocdescription == 1) :
		?>
		<dt class="location_desc"><?php echo JText::_( 'DESCRIPTION' ); ?></dt>
  		<dd class="location_desc">
  			<?php echo $row->locdescription; ?>
  		</dd>

		<?php endif; ?> 	
	</dl>
	<?php endforeach; ?>
</div>

<!--pagination-->
<?php if (( $this->page > 0 ) && ( !$this->pop )) : ?>
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