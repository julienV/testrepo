<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<h2 class="register"><?php echo JText::_( 'REGISTERED USERS' ).':'; ?></h2>
<?php
//only set style info if users allready have registered
if ($this->registers) :
?>
	<ul class="user floattext">
<?php endif; ?>


<?php
//loop through attendees
foreach ($this->registers as $register) :

	//if CB
	if ($this->elsettings->comunsolution == 1) :

		$thumb_path = 'images/comprofiler/tn';
		$no_photo 	= ' alt="'.$register->urname.'" border=0';

		//avatars should be displayed
		if (($this->elsettings->comunoption == 2) && ($this->elsettings->comunoption != 0)) :

			foreach ($this->pics as $pic) :

				//User has avatar
				if(($pic->avatar!='') && ($register->uid!='0')) :
					echo "<li><a href='".JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$register->uid )."'><img src=".$thumb_path.$pic->avatar.$no_photo." alt='no photo' /><span class='username'>".$register->urname."</span></a></li>";

				//User has no avatar
				else :
					echo "<li><a href='".JRoute::_( 'index.php?option=com_comprofiler&task=userProfile&user='.$register->uid )."'><img src=\"components/com_comprofiler/images/english/tnnophoto.jpg\" border=0 alt=\"no photo\" /><span class='username'>".$register->urname."</span></a></li>";
				endif;

			endforeach;

		endif;

	//only show the username with link to profile
	if ($this->elsettings->comunoption == 1) :
		echo "<li><span class='username'><a href='".JRoute::_( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='.$register->uid )."'>".$register->urname." </a></span></li>";
	endif;


//if CB end - if not CB than only name
endif;

//no communitycomponent is set so only show the username
if (($this->elsettings->comunoption == 0) || ($this->elsettings->comunsolution == 0)) :
	echo "<li><span class='username'>".$register->urname."</span></li>";
endif;

//end loop through attendees
endforeach;
?>

<?php
//only set style info if users allready have registered
if ($this->registers) : ?>
	</ul>
<?php endif; ?>

<?php
switch ($this->formhandler) {

	case 1:
		echo JText::_( 'TOO LATE REGISTER' );
	break;

	case 2:
		echo JText::_( 'LOGIN' );
	break;

	case 3:

		//the user is allready registered. Let's check if he can unregister from the event
		if ($this->row->unregistra == 0) :

			//no he is not allowed to unregister
			echo JText::_( 'ALLREADY REGISTERED' );

		else:

			//he is allowed to unregister -> display form
			?>
			<form name="Eventlist" action="<?php echo JRoute::_('index.php'); ?>" method="post">
			<input type="hidden" name="rdid" value="<?php echo $this->row->did; ?>">
			<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
			<input type="hidden" name="task" value="delreguser">

			<?php echo JText::_( 'UNREGISTER BOX' ); ?>

			<input type="checkbox" name="reg_check" onClick="check(this, document.Eventlist.senden)">
			<br /><br />
			<input type="submit" name="senden" value="<?php echo JText::_( 'UNREGISTER' ); ?>" disabled>
			</form>

			<script language="JavaScript">
			function check(checkbox, senden) {
				if(checkbox.checked==true){
					senden.disabled = false;
				} else {
					senden.disabled = true;
				}
			}
			</script>
			<?php
		endif;

	break;

	case 4:

		//the user is not registered allready -> display registration form
		?>
		<form name="Eventlist" action="<?php echo JRoute::_('index.php'); ?>" method="post">
		<input type="hidden" name="rdid" value="<?php echo $this->row->did; ?>">
		<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
		<input type="hidden" name="task" value="userregister">

		<?php echo JText::_( 'I WILL GO' ).':'; ?>

		<input type="checkbox" name="reg_check" onClick="check(this, document.Eventlist.senden)">
		<br /><br />
		<input type="submit" name="senden" value="<?php echo JText::_( 'REGISTER' ); ?>" disabled>
		</form>

		<script language="JavaScript">
		function check(checkbox, senden) {
			if(checkbox.checked==true){
				senden.disabled = false;
			} else {
				senden.disabled = true;
			}
		}
		</script>
		<?php
	break;
}
?>