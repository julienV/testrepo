<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form method="post" action="<?php echo $this->request_url; ?>" enctype="multipart/form-data" name="adminForm">

<table class="adminlist">
	<tr>
  		<td><img src="components/com_eventlist/assets/images/evlogo.png" height="108" width="250" alt="Event List Logo" align="left"></td>
  		<td class="sectionname" align="right"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;">::<?php echo JText::_( 'UPLOAD IMAGE' ); ?>::</font></td>
	</tr>
</table>

<table class="noshow">
  	<tr>
		<td width="50%" valign="top">

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'SELECT IMAGE UPLOAD' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td>
 							<input class="inputbox" name="userfile" id="userfile" type="file" />
							<br /><br />
							<input class="button" type="submit" value="<?php echo JText::_('UPLOAD') ?>" name="adminForm" />
    			       	</td>
      				</tr>
				</tbody>
			</table>
			</fieldset>

		</td>
        <td width="50%" valign="top">

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'ATTENTION' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td>
 							<b><?php
 							echo JText::_( 'TARGET DIRECTORY' ).':'; ?></b>
							<?php
							if ($this->task == 'venueimg') {
								echo "/images/eventlist/venues/";
								$this->task = 'venueimgup';
							} else {
								echo "/images/eventlist/events/";
								$this->task = 'eventimgup';
							}

							?><br />
							<b><?php echo JText::_( 'IMAGE FILESIZE' ).':'; ?></b> <?php echo $this->elsettings->sizelimit; ?> kb<br />

							<?php
							if ( $this->elsettings->gddisabled ) {

								if (imagetypes() & IMG_PNG) {
									echo "<br /><font color='green'>".JText::_( 'PNG SUPPORT' )."</font>";
								} else {
									echo "<br /><font color='red'>".JText::_( 'NO PNG SUPPORT' )."</font>";
								}
								if (imagetypes() & IMG_JPEG) {
									echo "<br /><font color='green'>".JText::_( 'JPG SUPPORT' )."</font>";
								} else {
									echo "<br /><font color='red'>".JText::_( 'NO JPG SUPPORT' )."</font>";
								}
								if (imagetypes() & IMG_GIF) {
									echo "<br /><font color='green'>".JText::_( 'GIF SUPPORT' )."</font>";
								} else {
									echo "<br /><font color='red'>".JText::_( 'NO GIF SUPPORT' )."</font>";
								}
							} else {
								echo "<br /><font color='green'>".JText::_( 'PNG SUPPORT' )."</font>";
								echo "<br /><font color='green'>".JText::_( 'JPG SUPPORT' )."</font>";
								echo "<br /><font color='green'>".JText::_( 'GIF SUPPORT' )."</font>";
							}
							?>
    			       	</td>
      				</tr>
				</tbody>
			</table>
			</fieldset>

		</td>
	</tr>
</table>

<?php if ( $this->elsettings->gddisabled ) { ?>

<table class="noshow">
	<tr>
		<td>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'ATTENTION' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
	          			<td align="center">
							<?php echo JText::_( 'GD WARNING' ); ?>
    			     	 </td>
      				</tr>
				</tbody>
			</table>
			</fieldset>

		</td>
	</tr>
</table>

<?php } ?>

<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="controller" value="imageupload" />
<input type="hidden" name="task" value="<?php echo $this->task;?>" />
</form>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>