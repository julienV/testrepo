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

	<script language="JavaScript" type="text/javascript">
		function mailToFriend() {
			if (document.mailToFriend) {
				window.open('about:blank',
				'MailToFriend',
				'width=400,height=300,menubar=yes,resizable=yes');
				document.mailToFriend.submit();
 			 }
		}
	</script>
	<form action="index.php" name="mailToFriend" method="post" target="MailToFriend" style="display:inline">
		<input type="hidden" name="option" value="com_mailto" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="link" value="<?php echo urlencode( JRequest::getURI() );?>" />
	</form>

	<?php if ($this->params->get('page_title')) : ?>
	<h1 class="componentheading floattext">
		<span class="buttons">
			<a href="javascript:void mailToFriend()"><?php echo $this->mailbutton;	?></a>
			<?php echo ELOutput::printbutton( $this->print_link, $this->params ); ?>
		</span>
		<?php echo $this->params->get('header').' - '.JText::_( 'DETAILS' ) ; ?>
	</h1>
	<?php endif; ?>

	<!-- Details EVENT -->
	<div id="eventlist" class="event_id<?php echo $this->row->did; ?> el_details">
		<h2 class="eventlist">
		    <?php
    		echo JText::_( 'EVENT' ) ;
    		echo ELOutput::editbutton($this->item->id, $this->row->did, $this->params, $this->allowedtoeditevent, 'editevent' );
    		?>
		</h2>
			<?php //cell for flyer
				if (!empty($this->row->datimage)) :
					if (file_exists(JPATH_SITE.'/images/eventlist/events/small/'.$this->row->datimage)) :

						if ($this->elsettings->lightbox == 0) :
						?>
						    <a class="flyer" href="javascript:void window.open('<?php echo $this->dimage['original']; ?>','Popup','width=<?php echo $this->dimage['widthev']; ?>,height=<?php echo $this->dimage['heightev']; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">
						<?php
						 else :
						?>
						    <a class="flyer" href="<?php echo $this->dimage['original']; ?>" rel="lightbox" title="<?php echo $this->row->title; ?>">
						<?php endif; ?>
						    <img src="<?php echo $this->dimage['thumb']; ?>" width="<?php echo $this->dimage['thumbwidthev']; ?>" height="<?php echo $this->dimage['thumbheightev']; ?>" alt="<?php echo $this->row->title; ?>" title="<?php echo JText::_( 'CLICK TO ENLARGE' ); ?>" />
						    </a>
					<?php
					//No thumbnail? Then take the in the settings specified values for the original
					else : ?>
						<img class="flyer" src="<?php echo $this->dimage['original']; ?>" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>"  />
					<?php
					endif;
				 else :
					echo '&nbsp;';
				endif;
			?>
		<dl class="event_info floattext">
			<dt class="when"><?php echo JText::_( 'WHEN' ).':' ;?></dt>
			<dd class="when">
			<?php
				echo $this->displaydate;
				echo $this->displaytime;
			?>
			</dd>

		  	<?php
  			if ($this->elsettings->showdetailstitle == 1) :
  			?>
				<dt class="title"><?php echo JText::_( 'TITLE' ).':'; ?></dt>
    			<dd class="title"><?php echo $this->row->title; ?></dd>
			<?php
  			endif;
  			if ($this->row->locid != 0) :
  			?>
		    <dt class="where"><?php echo JText::_( 'WHERE' ).':' ; ?></dt>
		    <dd class="where">
    		<?php if (($this->elsettings->showdetlinkvenue == 1) && (!empty($this->row->url))) :	?>
    		
			    <a href="<?php echo $this->row->url; ?>" target="_blank"> <?php echo $this->row->venue; ?></a>
				
			<?php elseif ($this->elsettings->showdetlinkvenue == 2) : ?>
			
			    <a href="<?php echo JRoute::_( 'index.php?view=venueevents&locatid='.$this->row->venueslug ); ?>"><?php echo $this->row->venue; ?></a>
			<?php
				elseif ($this->elsettings->showdetlinkvenue == 0) :
					echo $this->row->venue;
				endif;
				?> - <?php echo $this->row->city; ?>
			</dd>
			<?php endif; ?>

			<dt class="category"><?php echo JText::_( 'CATEGORY' ).':' ;?></dt>
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
			<?php //cell for flyer
			
			if (!empty($this->row->locimage)) :
					if (file_exists(JPATH_SITE.'/images/eventlist/venues/small/'.$this->row->locimage)) :

						if ($this->elsettings->lightbox == 0) :
						?>
						<a class="flyer" href="javascript:void window.open('<?php echo $this->limage['originalloc']; ?>','Popup','width=<?php echo $this->limage['widthloc']; ?>,height=<?php echo $this->limage['heightloc']; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');" title="<?php echo JText::_( 'CLICK TO ENLARGE' ); ?>">
						<?php
						 else :
						?>
						<a href="<?php echo $this->limage['originalloc']; ?>" class="flyer" rel="lightbox" title="<?php echo $this->row->venue.' '.JText::_( 'CLICK TO ENLARGE' ); ?>">
						<?php endif; ?>

						<img src="<?php echo $this->limage['thumbloc']; ?>" width="<?php echo $this->limage['thumbwidthloc']; ?>" height="<?php echo $this->limage['thumbheightloc']; ?>" alt="venue image" title="<?php echo JText::_( 'CLICK TO ENLARGE' ); ?>" />
						</a>
						<?php else : ?>
						<img src="<?php echo $this->limage['originalloc']; ?>" class="flyer" width="<?php echo $this->elsettings->imagewidth; ?>" height="<?php echo $this->elsettings->imagehight; ?>" />
					<?php
					endif;
				else :
					echo '&nbsp;';
				endif;


				//Link to map		
				$mapimage = JAdminMenus::ImageCheck( 'mapsicon.png', '/components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'MAP' ), JText::_( 'MAP' ) );
				
				switch ($this->elsettings->showmapserv) :
					case 0:
					break;

					case 1:
  						if ($this->elsettings->map24id != '') :
						?>
							<a class="flyer" href="http://link2.map24.com/?lid=<?php echo $this->elsettings->map24id ?>&maptype=JAVA&width0=2000&street0=<?php echo $this->row->street ?>&zip0=<?php echo $this->row->plz ?>&city0=<?php echo $this->row->city ?>&country0=<?php echo $this->row->country ?>&sym0=10280&description0=<?php echo $this->row->venue ?>" target="_blank">
								<?php echo $mapimage; ?>
							</a>
						<?php
						endif;
					break;

					case 2:
					?>
						<a class="flyer" href="http://maps.google.com/maps?q=<?php echo $this->row->street; ?>+<?php echo $this->row->city ?>+<?php echo $this->row->plz ?>+<?php echo $this->row->country ?>" title="<?php echo JText::_( 'MAP' ); ?>" target="_blank">
							<?php echo $mapimage; ?>
						</a>
				<?php
					break;
				endswitch; //switch ende
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
    			<?php echo $this->row->country; ?>
    		</dd>
    		<?php endif; ?>
		<?php
		endif; //showdetails ende
		?>
	</dl>

	<?php
  		if ($this->elsettings->showlocdescription == 1) :
		?>

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