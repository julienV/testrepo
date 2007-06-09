<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

if ($this->updatedata->failed == 0) {
		?>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td>
		  		<?php
		  			if ($this->updatedata->current == 0 ) {
		  				echo JHTML::_('image.administrator', 'icon-48-checkin.png', '/templates/'. $this->template .'/images/header/', NULL, NULL);
		  			} elseif( $this->updatedata->current == -1 ) {
		  				echo JHTML::_('image.administrator', 'icon-48-help_header.png', '/templates/'. $this->template .'/images/header/', NULL, NULL);
		  			} else {
		  				echo JHTML::_('image.administrator', 'icon-48-help_header.png', '/templates/'. $this->template .'/images/header/', NULL, NULL);
		  			}
		  		?>
		  		</td>
		  		<td>
		  		<?php
		  			if ($this->updatedata->current == 0) {
		  				echo '<b><font color="green">'.JText::_( 'LATEST VERSION' ).'</font></b>';
		  			} elseif( $this->updatedata->current == -1 ) {
		  				echo '<b><font color="red">'.JText::_( 'OLD VERSION' ).'</font></b>';
		  			} else {
		  				echo '<b><font color="orange">'.JText::_( 'NEWER VERSION' ).'</font></b>';
		  			}
		  		?>
		  		</td>
			</tr>
		</table>

		<br />

		<table  cellspacing="0" cellpadding="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td><b><?php echo JText::_( 'VERSION' ).':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->versiondetail;
					?>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'RELEASE DATE' ).':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->date;
					?>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'CHANGES' ).':'; ?></b></td>
		  		<td><ul>
		  			<?php
					foreach ($this->updatedata->changes as $change) {
   						echo '<li>'.$change.'</li>';
					}
					?>
					<ul>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'INFORMATION' ).':'; ?></b></td>
		  		<td>
					<a href="<?php echo $this->updatedata->info; ?>" target="_blank">Click for more information</a>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'FILES' ).':'; ?></b></td>
		  		<td>
					<a href="<?php echo $this->updatedata->download; ?>" target="_blank">Download new release or upgradepack</a>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'NOTES' ).':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->notes;
					?>
		  		</td>
			</tr>
		</table>

<?php
} else {
?>

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
		  		<td>
		  		<?php
		  			echo JHTML::_('image.administrator', 'icon-48-help_header.png', '/templates/'. $this->template .'/images/header/', NULL, NULL);
		  		?>
		  		</td>
		  		<td>
		  		<?php
		  			echo '<b><font color="red">'.JText::_( 'CONNECTION FAILED' ).'</font></b>';
		  		?>
		  		</td>
			</tr>
		</table>
<?php
}
?>
<br />

<div align="center">
	<button type="button" onclick="window.top.document.popup.hide();">
	<?php echo JText::_( 'Close' );?>
	</button>
</div>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>