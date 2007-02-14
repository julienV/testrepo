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
		} else if (form.club.value == ""){
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
	</script>
		
		<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
		
		<table class="adminlist">
			<tr>
		  		<td><img src="<?php echo $this->live_site."/administrator/components/com_eventlist/assets/images/evlogo.png"; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>  
		  		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;"><?php echo $this->row->id ? '::'.JText::_( 'EDIT VENUE' ).'::' : '::'.JText::_( 'Add Venue' ).'::';?></font></td>
			</tr>
		</table>
		
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
		<table  class="adminform">
		<tr>
			<td>
				<label for="club">
					<?php echo JText::_( 'VENUE' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="club" id= "club" value="<?php echo $this->row->club; ?>" size="40" maxlength="50">
			</td>
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
		</table>
				<table class="adminform">
				<tr>
					<td>
						<?php
						echo $this->editor->display( 'locdescription',  $this->row->locdescription, '100%;', '550', '75', '20' ) ;
						?>
					</td>
				</tr>
				</table>
			</td>
			<td valign="top" width="320px" style="padding: 7px 0 0 5px">
			<?php
			$title = JText::_( 'ADDRESS' );
			echo $this->pane->startPane('det-pane');
			echo $this->pane->startPanel( $title, 'address' );

		//Set the info image
		$infoimage = JAdminMenus::ImageCheck( 'icon-16-hint.png', '../components/com_eventlist/assets/images/', NULL, NULL, JText::_( 'NOTES' ), JText::_( 'NOTES' ) );
		?>
		<table>
		<tr>
			<td>
				<label for="street">
					<?php echo JText::_( 'STREET' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="street" value="<?php echo $this->row->street; ?>" size="35" maxlength="50">
			</td>
		</tr>
		<tr>
			<td>
				<label for="plz">
					<?php echo JText::_( 'ZIP' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="plz" value="<?php echo $this->row->plz; ?>" size="15" maxlength="10">
			</td>
		</tr>
		<tr>
			<td>
				<label for="city">
					<?php echo JText::_( 'CITY' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="city" id="city" value="<?php echo $this->row->city; ?>" size="35" maxlength="50">
			</td>
		</tr>
		<tr>
			<td>
				<label for="state">
					<?php echo JText::_( 'STATE' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="state" id="state" value="<?php echo $this->row->state; ?>" size="35" maxlength="50">
			</td>
		</tr>
		<tr>
			<td>
				<label for="country">
					<?php echo JText::_( 'COUNTRY' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="country" value="<?php echo $this->row->country; ?>" size="4" maxlength="3">&nbsp;
				
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('COUNTRY HINT'); ?>">
					<?php echo $infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>
				<label for="url">
					<?php echo JText::_( 'WEBSITE' ).':'; ?>
				</label>
			</td>
			<td>
				<input name="url" value="<?php echo $this->row->url; ?>" size="35" maxlength="150">&nbsp;

				<span class="editlinktip hasTip" title="<?php echo JText::_( 'NOTES' ); ?>::<?php echo JText::_('WEBSITE HINT'); ?>">
					<?php echo $infoimage; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php echo JText::_( 'ADDRESS NOTICE' ); ?>
			</td>
		</tr>
		</table>
		<?php
			
			$title = JText::_( 'IMAGE' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'image' );

		?>
		<table>
		<tr>
			<td>
				<label for="locimage">
					<?php echo JText::_( 'SELECTIMAGE' ).':'; ?>
				</label>
			</td>
			<td>
				<?php
					echo $this->imageselect;
				?>
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
		<?php

			$title = JText::_( 'Metadata Information' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'metadata' );
		?>
		<table>
		<tr>
			<td>
				<label for="metadesc">
					<?php echo JText::_( 'Description' ); ?>:
				</label>
				<br />
				<textarea class="inputbox" cols="40" rows="5" name="meta_description" id="metadesc" style="width:300px;"><?php echo str_replace('&','&amp;',$this->row->meta_description); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<label for="metakey">
					<?php echo JText::_( 'Keywords' ); ?>:
				</label>
				<br />
				<textarea class="inputbox" cols="40" rows="5" name="meta_keywords" id="metakey" style="width:300px;"><?php echo str_replace('&','&amp;',$this->row->meta_keywords); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" class="button" value="<?php echo JText::_( 'ADD VENUE CITY' ); ?>" onclick="f=document.adminForm;f.metakey.value=f.club.value+', '+f.city.value+f.metakey.value;" />
			</td>
		</tr>
		</table>
		
		<?php
			echo $this->pane->endPanel();
			echo $this->pane->endPane();
		?>
			</td>
		</tr>
		</table>
		
		<?php
		echo ELAdmin::footer( );
		?>
		
			<input type="hidden" name="option" value="com_eventlist" />
			<input type="hidden" name="controller" value="venues" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>