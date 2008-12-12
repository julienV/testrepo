<?php
/**
 * @version 1.0 $Id: default.php 662 2008-05-09 22:28:53Z schlu $
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2008 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
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

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
  <fieldset>
    <legend><?php echo JText::_('Import events'); ?></legend>
	    <?php echo JText::_( 'Instructions: ' ) ?>
			<ul>
			<li>
			<?php echo JText::_( "The column names must match a subset of the fields name for events table, plus 'categories' field." ); ?>
			</li>
			<li>
			<?php echo JText::_( "The first row of the file must contain the column names." ); ?>
			</li>
			<li>
			<?php echo JText::_( "Fields must be terminated by ' , ', enclosed by ' \" '." ); ?>
			</li>
      <li>
      <?php echo JText::_( "categories field must contain list of category ids separated by comma." ); ?>
      </li>
			<li>
			<?php echo JText::_( "Possible column names are: categories, " ).implode(", ",$this->eventfields); ?>
			</li>
			</ul>
      <table>
        <tr>
          <td>    
            <label for="file">
                <?php echo JText::_( 'Select csv file' ).':'; ?>
            </label>
          </td>
          <td>
			      <input type="file" id="event-file-upload" accept="text/*" name="Filedata" />
			      <input type="submit" id="event-file-upload-submit" value="<?php echo JText::_('Start import'); ?>" onclick="document.getElementsByName('task')[0].value='csveventimport';return true;"/>
			      <span id="upload-clear"></span>
          </td>
        </tr>
        <tr>
          <td>    
            <label for="replace_events">
                <?php echo JText::_( 'Replace existing events (if Id already exits) ?' ).':'; ?>
            </label>
          </td>
          <td>
            <?php
            $html = JHTML::_('select.booleanlist', 'replace_events', 'class="inputbox"', 0 );
            echo $html;
            ?>      
          </td>
        </tr>
      </table>
  </fieldset>
  
  <fieldset>
    <legend><?php echo JText::_('Import Categories'); ?></legend>
      <?php echo JText::_( 'Instructions: ' ) ?>
      <ul>
      <li>
      <?php echo JText::_( "The column names must match a subset of the fields name of categories table." ); ?>
      </li>
      <li>
      <?php echo JText::_( "The first row of the file must contain the column names." ); ?>
      </li>
      <li>
      <?php echo JText::_( "Fields must be terminated by ' , ', enclosed by ' \" '." ); ?>
      </li>
      <li>
      <?php echo JText::_( "Possible column names are: " ).implode(", ",$this->catfields); ?>
      </li>
      </ul>
      <table>
        <tr>
          <td>    
            <label for="file">
                <?php echo JText::_( 'Select csv file' ).':'; ?>
            </label>
          </td>
          <td>
            <input type="file" id="cat-file-upload" accept="text/*" name="Filedata" />
            <input type="submit" id="cat-file-upload-submit" value="<?php echo JText::_('Start import'); ?>" onclick="document.getElementsByName('task')[0].value='csvcategoriesimport';return true;"/>
            <span id="upload-clear"></span>
          </td>
        </tr>
        <tr>
          <td>    
            <label for="replace_cats">
                <?php echo JText::_( 'Replace existing events (if Id already exits) ?' ).':'; ?>
            </label>
          </td>
          <td>
            <?php
            $html = JHTML::_('select.booleanlist', 'replace_cats', 'class="inputbox"', 0 );
            echo $html;
            ?>      
          </td>
        </tr>
      </table>
  </fieldset>
	<input type="hidden" name="option" value="com_eventlist" />
	<input type="hidden" name="view" value="import" />
	<input type="hidden" name="controller" value="import" />
	<input type="hidden" name="task" value="" />
</form>

<p class="copyright">
  <?php echo ELAdmin::footer( ); ?>
</p>