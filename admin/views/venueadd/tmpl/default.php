<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
	function submitbutton(task)
	{
		var form = document.adminForm;
		if (task == 'locimupview')
		{
			var url='index3.php?option=com_eventlist&task=locimupview';
			document.popup.show(url, 700, 500, null);

		} else if (task == 'cancel') {
			submitform( task );
			return;
		} else if (form.venue.value == ""){
			alert( "<?php echo JText::_( 'ADD VENUE' ); ?>" );
		} else if (form.city.value == ""){
			alert( "<?php echo JText::_( 'ADD CITY' ); ?>" );
		} else {
			<?php
			echo $this->editor->save( 'locdescription' );
			?>
			submitform( task );
		}
	}

	function elAddVenue(savevenue) {
			submitform(savevenue);
			window.parent.close();
	}
</script>

<?php
//Set the info image
$infoimage = JAdminMenus::ImageCheck( 'icon-16-hint.png', '../components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );
?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">


<table class="adminform" width="100%">
	<tr>
		<td>
			<div style="float: left;">
				<label for="venue">
					<?php echo JText::_( 'VENUE' ).':'; ?>
				</label>
				<input name="venue" value="<?php echo $this->row->venue; ?>" size="55" maxlength="50" />
					&nbsp;&nbsp;&nbsp;
			</div>

			<div style="float: right;">
				<button type="button" onclick="elAddVenue('savevenue')">
					<?php echo JText::_('SAVE') ?>
				</button>
				<button type="button" onclick="window.parent.close()" />
					<?php echo JText::_('CANCEL') ?>
				</button>
				</div>
		</td>
	</tr>
</table>

<br />

<fieldset>
<legend><?php echo JText::_('VARIOUS'); ?></legend>
<table>
	<tr>
		<td>
			<label for="publish">
				<?php echo JText::_( 'PUBLISHED' ).':'; ?>
			</label>
		</td>
		<td>
			<?php
			$html = JHTMLSelect::yesnoList( 'published', 'class="inputbox"', $this->row->published );
			echo $html;
			?>
		</td>
	</tr>
	<tr>
		<td>
			<label for="locimage">
				<?php echo JText::_( 'CHOOSE IMAGE' ).':'; ?>
			</label>
		</td>
		<td>
			<?php echo $this->imageselect; ?>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td>
			<script language="javascript" type="text/javascript">
			if (document.forms[0].a_imagename.value!=''){
				var imname = document.forms[0].a_imagename.value;
				jsimg='../images/eventlist/venues/' + imname;
			} else {
				jsimg='../images/M_images/blank.png';
			}
			document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="Preview" />');
			</script>
			<br />
			<br />
		</td>
	</tr>
</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_('ADDRESS'); ?></legend>
	<table class="adminform" width="100%">
		<tr>
  			<td><?php echo JText::_( 'STREET' ).':'; ?></td>
			<td><input name="street" value="<?php echo $this->row->street; ?>" size="55" maxlength="50" /></td>
	 	</tr>
  		<tr>
  		  	<td><?php echo JText::_( 'ZIP' ).':'; ?></td>
  		  	<td><input name="plz" value="<?php echo $this->row->plz; ?>" size="15" maxlength="10" /></td>
	  	</tr>
  		<tr>
  			<td><?php echo JText::_( 'CITY' ).':'; ?></td>
  			<td><input name="city" value="<?php echo $this->row->city; ?>" size="55" maxlength="50" />
			</td>
  		</tr>
  		<tr>
  			<td><?php echo JText::_( 'STATE' ).':'; ?></td>
  			<td><input name="state" value="<?php echo $this->row->state; ?>" size="55" maxlength="50" />
			</td>
  		</tr>
  		<tr>
  		  	<td><?php echo JText::_( 'COUNTRY' ).':'; ?></td>
  		  	<td>
				<input name="country" value="<?php echo $this->row->country; ?>" size="4" maxlength="3" />&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('NOTES'); ?>::<?php echo JText::_( 'COUNTRY HINT' ); ?>">
					<?php echo $infoimage; ?>
				</span>
			</td>
		</tr>
  		<tr>
    		<td><?php echo JText::_( 'WEBSITE' ).':'; ?></td>
    		<td>
    			<input name="url" value="<?php echo $this->row->url; ?>" size="55" maxlength="50" />&nbsp;
    			<span class="editlinktip hasTip" title="<?php echo JText::_('NOTES'); ?>::<?php echo JText::_( 'WEBSITE HINT' ); ?>">
					<?php echo $infoimage; ?>
				</span>
    		</td>
  		</tr>
	</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_('DESCRIPTION'); ?></legend>
		<?php echo $this->editor->display('locdescription', $this->row->locdescription, '655', '400', '70', '15'); ?>
</fieldset>

<fieldset>
	<table>
		<tr>
			<td valign="top">
				<label for="metadesc">
					<?php echo JText::_( 'Description' ); ?>:
				</label>
				<br />
				<textarea class="inputbox" cols="40" rows="5" name="meta_description" id="metadesc" style="width:300px;"><?php echo str_replace('&','&amp;',$this->row->meta_description); ?></textarea>
			</td>
			<td valign="top">
				<label for="metakey">
					<?php echo JText::_( 'Keywords' ); ?>:
				</label>
				<br />
				<textarea class="inputbox" cols="40" rows="5" name="meta_keywords" id="metakey" style="width:300px;"><?php echo str_replace('&','&amp;',$this->row->meta_keywords); ?></textarea>
				<br />
				<input type="button" class="button" value="<?php echo JText::_( 'ADD VENUE CITY' ); ?>" onclick="f=document.adminForm;f.metakey.value=f.venue.value+', '+f.city.value+f.metakey.value;" />
			</td>
		</tr>
	</table>
</fieldset>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="controller" value="venues" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="task" value="" />
</form>