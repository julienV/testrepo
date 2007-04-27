<?php
/**
 * @version 0.9 $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2007 Christoph Lukes
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Holds the logic for image manipulation
 *
 * @package Joomla
 * @subpackage EventList
 */
class ELImage {

	/**
	* Creates a Thumbnail of an image
	*
 	* @author Christoph Lukes
	* @since 0.9
 	*
 	* @param string $file The path to the file
	* @param string $save The targetpath
	* @param string $width The with of the image
	* @param string $height The height of the image
	* @param int $prop Keep propertions or not
	*/
	function thumb($file, $save, $width, $height, $prop = TRUE)
	{
		/*
		* GD-Lib > 2.0
		* If $prop=TRUE, then the proportions of the image are kept in the thumbnail
		*/
		@unlink($save);
		$infos = @getimagesize($file);

		if ($prop) {
			// keep proportions
			$iWidth = $infos[0];
			$iHeight = $infos[1];
			$iRatioW = $width / $iWidth;
			$iRatioH = $height / $iHeight;

			if ($iRatioW < $iRatioH) {
				$iNewW = $iWidth * $iRatioW;
				$iNewH = $iHeight * $iRatioW;
			} else {
				$iNewW = $iWidth * $iRatioH;
				$iNewH = $iHeight * $iRatioH;
			} // end if

		} else {

			//Stretch it
			$iNewW = $width;
			$iNewH = $height;
		}


		//Don't resize images which are smaller than thumbs
		if ($infos[0] < $width && $infos[1] < $height) {
			$iNewW = $infos[0];
			$iNewH = $infos[1];
		}

		if($infos[2] == 1) {
			/*
			* Image is typ gif
			*/
			$imgA = imagecreatefromgif($file);
			$imgB = imagecreatetruecolor($iNewW,$iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagegif($imgB, $save);

		} elseif($infos[2] == 2) {
			/*
			* Image is typ jpg
			*/
			$imgA = imagecreatefromjpeg($file);
			$imgB = imagecreatetruecolor($iNewW,$iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagejpeg($imgB, $save);

		} elseif($infos[2] == 3) {
			/*
			* Image is typ png
			*/
			$imgA = imagecreatefrompng($file);
			$imgB = imagecreatetruecolor($iNewW, $iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagepng($imgB, $save);
		} else {
			return FALSE;
		}
		return true;
	}

	/**
	* Determine the GD version
	* Code from php.net
	*
	* @since 0.9
	* @param int
	*
	* @return int
	*/
	function gdVersion($user_ver = 0)
	{
		if (! extension_loaded('gd')) {
			return;
		}
		static $gd_ver = 0;

		// Just accept the specified setting if it's 1.
		if ($user_ver == 1) {
			$gd_ver = 1;
			return 1;
		}
		// Use the static variable if function was called previously.
		if ($user_ver !=2 && $gd_ver > 0 ) {
			return $gd_ver;
		}
		// Use the gd_info() function if possible.
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		// If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return 2;
			} else {
				$gd_ver = 1;
				return 1;
			}
		}
		// ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];

		return $match[0];
	}

	/**
	* Creates image information of event picture
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param string $live_site The path to the site
	* @param string $image The image name
	* @return array $dimage which holds the information
	*/
	function eventimage($live_site, $image, $imagewidth, $imagehight, $imageprob, $gddisabled)
	{
		if (!empty($image)) {

			/*
			*Create thumbnail
			*/

			if ($gddisabled == 1 && !file_exists(JPATH_SITE.'/images/eventlist/events/small/'.$image)) {

				$filepath 	= JPATH_SITE.'/images/eventlist/events/'.$image;
				$save 		= JPATH_SITE.'/images/eventlist/events/small/'.$image;

				ELImage::thumb($filepath, $save, $imagewidth, $imagehight, $imageprob);
			}

			/*
			* set paths
			*/
			$dimage['original'] = $live_site.'/images/eventlist/events/'.$image;
			$dimage['thumb'] 	= $live_site.'/images/eventlist/events/small/'.$image;

			if (file_exists(JPATH_SITE.'/images/eventlist/events/small/'.$image)) {

				/*
				* get imagesize of the original
				*/
				$iminfo = @getimagesize('images/eventlist/events/'.$image);
				$dimage['width'] 	= $iminfo[0];
				$dimage['height'] = $iminfo[1];

				/*
				* get imagesize of the thumbnail
				*/
				$thumbiminfo = @getimagesize('images/eventlist/events/small/'.$image);
				$dimage['thumbwidth'] 	= $thumbiminfo[0];
				$dimage['thumbheight'] 	= $thumbiminfo[1];
			}
			return $dimage;
		}
		return false;
	}

	/**
	* Creates image information of venue picture
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param string $live_site The path to the site
	* @param string $image The image name
	* @return array $limage which holds the information
	*/
	function venueimage($live_site, $image, $imagewidth, $imagehight, $imageprob, $gddisabled)
	{
		if (!empty($image)) {

			//Create thumbnail
			if ($gddisabled == 1 && !file_exists(JPATH_SITE.'/images/eventlist/venues/small/'.$image)) {

				$filepath 	= JPATH_SITE.'/images/eventlist/venues/'.$image;
				$save 		= JPATH_SITE.'/images/eventlist/venues/small/'.$image;

				ELImage::thumb($filepath, $save, $imagewidth, $imagehight, $imageprob);
			}

			//set paths
			$limage['original'] 	= $live_site.'/images/eventlist/venues/'.$image;
			$limage['thumb'] 	= $live_site.'/images/eventlist/venues/small/'.$image;

			if (file_exists(JPATH_SITE.'/images/eventlist/venues/small/'.$image)) {

				/*
				* get imagesize of the original
				*/
				$iminfoloc 	= @getimagesize('images/eventlist/venues/'.$image);
				$limage['width'] 	= $iminfoloc[0];
				$limage['height'] 	= $iminfoloc[1];

				/*
				* get imagesize of the thumbnail
				*/
				$thumbiminfoloc 	= @getimagesize('images/eventlist/venues/small/'.$image);
				$limage['thumbwidth'] 	= $thumbiminfoloc[0];
				$limage['thumbheight']	= $thumbiminfoloc[1];
			}
			return $limage;
		}
		return false;
	}
}
?>