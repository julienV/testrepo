<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (form.catname.value == ""){
		alert( "<?php echo JText::_( 'ADD NAME CATEGORY' ); ?>" );
	} else {
		<?php echo $this->editor->save( 'catdescription' ); ?>
		submitform( pressbutton );
	}
}
</script>
		
		
<form action="<?php $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<tr>
  		<td><img src="<?php echo $this->live_site."/administrator/components/com_eventlist/assets/images/evlogo.png"; ?>" height="108" width="250" alt="Event List Logo" align="left"></td>  
  		<td class="sectionname" align="right" width="100%"><font style="color: #C24733; font-size : 18px; font-weight: bold; text-align: left;"><?php echo $this->row->id ? '::'.JText::_( 'EDIT CATEGORY' ).'::' : '::'.JText::_( 'ADD CATEGORY' ).'::';?></font></td>
	</tr>
</table>
		
		
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
				<table  class="adminform">
					<tr>
						<td>
							<label for="catname">
								<?php echo JText::_( 'CATEGORY' ).':'; ?>
							</label>
						</td>
						<td>
							<input name="catname" value="<?php echo $this->row->catname; ?>" size="55" maxlength="50">
						</td>
					</tr>
					<tr>
						<td>
							<label for="published">
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
						// parameters : areaname, content, hidden field, width, height, rows, cols
						echo $this->editor->display( 'catdescription',  $this->row->catdescription, '100%;', '350', '75', '20', false ) ;
						?>
					</td>
				</tr>
			</table>
			</td>
			<td valign="top" width="320px" style="padding: 7px 0 0 5px">
			<?php
			$title = JText::_( 'ACCESS' );
			echo $this->pane->startPane( 'det-pane' );
			echo $this->pane->startPanel( $title, 'access' );
			?>
			<table>
				<tr>
					<td>
						<label for="access">
							<?php echo JText::_( 'ACCESS' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						echo $this->Lists['access'];
						?>
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_( 'GROUP' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'group' );
			?>
			<table>
				<tr>
					<td>
						<label for="Group">
							<?php echo JText::_( 'GROUP' ).':'; ?>
						</label>
					</td>
					<td>
						<?php echo $this->Lists['groups']; ?>
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_( 'IMAGE' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'catimage' );
			?>
			<table>
				<tr>
					<td>
						<label for="catimage">
							<?php echo JText::_( 'CHOOSE IMAGE' ).':'; ?>
						</label>
					</td>
					<td>
						<?php echo $this->Lists['imagelist']; ?>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<script language="javascript" type="text/javascript">
						if (document.forms[0].image.options.value!=''){
							jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'image' );
						} else {
							jsimg='../images/M_images/blank.png';
						}
						document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="Preview" />');
						</script>
						<br /><br />
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
				<input type="button" class="button" value="<?php echo JText::_( 'ADD CATNAME' ); ?>" onclick="f=document.adminForm;f.metakey.value=f.catname.value;" />
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
		
<input type="hidden" name="option" value="com_eventlist" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="controller" value="categories" />
<input type="hidden" name="task" value="" />
</form>

<p class="copyright">
	<?php echo ELAdmin::footer( ); ?>
</p>

<?php 
//keep session alive while editing
JHTML::keepAlive();
?>