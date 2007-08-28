/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @author Sascha Karnatz
 * @license GNU/GPL, see LICENCE.php
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
**/

var $content;
var $select_value;

function start_recurrencescript() {
	$content = $("recurrence_output");
	var $type = parseInt($("recurrence_type").value);
	if (!isNaN($type)) {
		if ($type > 3) {
			$("recurrence_select").value = 4;
		} else {
			$("recurrence_select").value = $type;
		}
		output_recurrencescript();
	}
	$("recurrence_select").onchange = output_recurrencescript;
}

function output_recurrencescript() {
	var $select_value = $("recurrence_select").value;
	if ($select_value != 0) {
		var $element = generate_output($select_output[$select_value], $select_value);
		$content.replaceChild($element, $content.firstChild);
		set_parameter();
		$("counter_row").style.display = "table-row"; // show the counter
	} else {
		$("recurrence_number").value = 0;
		$("recurrence_type").value = 0;
		$nothing = document.createElement("span");
		$nothing.appendChild(document.createTextNode(""));
		$content.replaceChild($nothing, $content.firstChild);
		$("counter_row").style.display = "none"; // hide the counter
	}

}

function generate_output($select_output, $select_value) {
	var $output_array = $select_output.split("[placeholder]");
	var $span = document.createElement("span");
	for ($i = 0; $i < $output_array.length; $i++) {
		$weekday_array = $output_array[$i].split("[placeholder_weekday]");
		if ($weekday_array.length > 1) {
			for ($k = 0; $k < $weekday_array.length; $k++) {
				$span.appendChild(document.createTextNode($weekday_array[$k]));
				if ($k == 0) {
					$span.appendChild(generate_selectlist_weekday());
				}
			}
		} else {
			$span.appendChild(document.createTextNode($output_array[$i]));
		}
		if ($i == 0) {
			$span.appendChild(generate_selectlist($select_value));
		}

	}
	return $span;
}

function generate_selectlist($select_value) {
	var $selectlist = document.createElement("select");
	$selectlist.name = "recurrence_selectlist";
	$selectlist.onchange = set_parameter;
	switch($select_value) {
		case "1":
			$limit = 14;
			break;
		case "2":
			$limit = 8;
			break;
		case "3":
			$limit = 12;
			break;
		default:
			$limit = 4;
			break;
	}
	for ($j = 0; $j < $limit; $j++) {
		var $option = document.createElement("option");
		if ($j == (parseInt($("recurrence_number").value) - 1)) {
			$option.selected = true;
		}
		$option.appendChild(document.createTextNode($j + 1)); // + 1 day because their is no recuring each "0" day
		$selectlist.appendChild($option);
	}
	return $selectlist;
}

function generate_selectlist_weekday() {
	var $selectlist = document.createElement("select");
	$selectlist.name = "recurrence_selectlist_weekday";
	$selectlist.onchange = set_parameter;
	for ($j = 0; $j < 7; $j++) {
		var $option = document.createElement("option");
		if ($j == (parseInt($("recurrence_type").value) - 4)) {
			$option.selected = true;
		}
		$option.value = $j;
		$option.appendChild(document.createTextNode($weekday[$j])); // + 1 day because their is no recuring each "0" day
		$selectlist.appendChild($option);
	}
	return $selectlist;
}

function set_parameter() {
	if ($("recurrence_select").value != 4) {
		$("recurrence_type").value = $("recurrence_select").value;
	} else {
		$("recurrence_type").value = parseInt($("recurrence_select").value) + parseInt(document.getElementsByName("recurrence_selectlist_weekday")[0].value);
	}
	$("recurrence_number").value = document.getElementsByName("recurrence_selectlist")[0].value;
}