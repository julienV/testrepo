DROP TABLE IF EXISTS
`#__eventlist_events`,
`#__eventlist_venues`,
`#__eventlist_categories`,
`#__eventlist_groups`,
`#__eventlist_groupmembers`,
`#__eventlist_settings`,
`#__eventlist_register`;

CREATE TABLE `#__eventlist_events` (
`id` int(11) unsigned NOT NULL auto_increment,
`locid` int(11) unsigned NOT NULL default '0',
`catsid` int(11) unsigned NOT NULL default '0',
`dates` date NOT NULL default '0000-00-00',
`enddates` date NULL default NULL,
`times` time NULL default NULL,
`endtimes` time NULL default NULL,
`title` text NOT NULL default '',
`created_by` int(11) unsigned NOT NULL default '0',
`modified` datetime NOT NULL,
`modified_by` int(11) unsigned NOT NULL default '0',
`author_ip` varchar(45) NOT NULL default '',
`created` datetime NOT NULL,
`datdescription` text NOT NULL,
`meta_keywords` varchar(200) NOT NULL default '',
`meta_description` varchar(255) NOT NULL default '',
`datimage` text NOT NULL default '',
`checked_out` int(11) NOT NULL default '0',
`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
`registra` tinyint(1) NOT NULL default '0',
`unregistra` tinyint(1) NOT NULL default '0',
`published` tinyint(1) NOT NULL default '0',
PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__eventlist_venues` (
`id` int(11) unsigned NOT NULL auto_increment,
`club` varchar(150) NOT NULL default '',
`url` text NOT NULL default '',
`street` varchar(150) default NULL,
`plz` varchar(60) default NULL,
`city` varchar(150) default NULL,
`state` varchar(150) default NULL,
`country` varchar(6) default NULL,
`locdescription` text NOT NULL,
`meta_keywords` text NOT NULL,
`meta_description` text NOT NULL,
`locimage` text NOT NULL default '',
`created_by` int(11) unsigned NOT NULL default '0',
`author_ip` varchar(45) NOT NULL default '',
`created` datetime NOT NULL,
`modified` datetime NOT NULL,
`modified_by` int(11) unsigned NOT NULL default '0',
`published` tinyint(1) NOT NULL default '0',
`checked_out` int(11) NOT NULL default '0',
`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
`ordering` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__eventlist_categories` (
`id` int(11) unsigned NOT NULL auto_increment,
`parent_id` int(11) unsigned NOT NULL default '0',
`catname` text NOT NULL default '',
`catdescription` text NOT NULL,
`meta_keywords` text NOT NULL,
`meta_description` text NOT NULL,
`image` text NOT NULL default '',
`published` tinyint(1) NOT NULL default '0',
`checked_out` int(11) NOT NULL default '0',
`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
`access` int(11) unsigned NOT NULL default '0',
`groupid` int(11) NOT NULL default '0',
`ordering` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__eventlist_register` (
`rid` int(11) unsigned NOT NULL auto_increment,
`rdid` int(11) unsigned NOT NULL default '0',
`uid` int(11) unsigned NOT NULL default '0',
`urname` varchar(60) NOT NULL default '0',
`uregdate` varchar(150) NOT NULL default '',
`uip` varchar(45) NOT NULL default '',
PRIMARY KEY  (`rid`)
) TYPE=MyISAM;

CREATE TABLE `#__eventlist_groups` (
`id` int(11) unsigned NOT NULL auto_increment,
`name` varchar(250) NOT NULL default '',
`description` text NOT NULL,
`checked_out` int(11) NOT NULL default '0',
`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `#__eventlist_groupmembers` (
`group_id` int(11) NOT NULL default '0',
`member` int(11) NOT NULL default '0'
) TYPE=MyISAM;

CREATE TABLE `#__eventlist_settings` (
  `id` int(11) NOT NULL,
  `oldevent` tinyint(4) NOT NULL,
  `minus` tinyint(4) NOT NULL,
  `showtime` tinyint(4) NOT NULL,
  `showtitle` tinyint(4) NOT NULL,
  `showlocate` tinyint(4) NOT NULL,
  `showcity` tinyint(4) NOT NULL,
  `showmapserv` tinyint(4) NOT NULL,
  `map24id` varchar(60) NOT NULL,
  `tablewidth` varchar(60) NOT NULL,
  `datewidth` varchar(60) NOT NULL,
  `titlewidth` varchar(60) NOT NULL,
  `locationwidth` varchar(60) NOT NULL,
  `citywidth` varchar(60) NOT NULL,
  `datename` varchar(200) NOT NULL,
  `titlename` varchar(200) NOT NULL,
  `locationname` varchar(200) NOT NULL,
  `cityname` varchar(200) NOT NULL,
  `formatdate` varchar(200) NOT NULL,
  `formattime` varchar(200) NOT NULL,
  `timename` varchar(150) NOT NULL,
  `showdetails` tinyint(4) NOT NULL,
  `showtimedetails` tinyint(4) NOT NULL,
  `showevdescription` tinyint(4) NOT NULL,
  `showdetailstitle` tinyint(4) NOT NULL,
  `showdetailsadress` tinyint(4) NOT NULL,
  `showlocdescription` tinyint(4) NOT NULL,
  `showlinkclub` tinyint(4) NOT NULL,
  `showdetlinkclub` tinyint(4) NOT NULL,
  `delivereventsyes` tinyint(4) NOT NULL,
  `mailinform` tinyint(4) NOT NULL,
  `mailinformrec` varchar(250) NOT NULL,
  `mailinformrec2` varchar(250) NOT NULL,
  `datdesclimit` varchar(45) NOT NULL,
  `autopubl` tinyint(4) NOT NULL,
  `deliverlocsyes` tinyint(4) NOT NULL,
  `autopublocate` tinyint(4) NOT NULL,
  `showcat` tinyint(4) NOT NULL,
  `catfrowidth` varchar(60) NOT NULL,
  `catfroname` varchar(200) NOT NULL,
  `evdelrec` tinyint(4) NOT NULL,
  `evpubrec` tinyint(4) NOT NULL,
  `locdelrec` tinyint(4) NOT NULL,
  `locpubrec` tinyint(4) NOT NULL,
  `sizelimit` varchar(60) NOT NULL,
  `imagehight` varchar(60) NOT NULL,
  `imagewidth` varchar(60) NOT NULL,
  `imageprob` tinyint(4) NOT NULL,
  `gddisabled` tinyint(4) NOT NULL,
  `imageenabled` tinyint(4) NOT NULL,
  `comunsolution` tinyint(4) NOT NULL,
  `comunoption` tinyint(4) NOT NULL,
  `catlinklist` tinyint(4) NOT NULL,
  `showfroregistra` tinyint(4) NOT NULL,
  `showfrounregistra` tinyint(4) NOT NULL,
  `eventedit` tinyint(4) NOT NULL,
  `eventeditrec` tinyint(4) NOT NULL,
  `eventowner` tinyint(4) NOT NULL,
  `venueedit` tinyint(4) NOT NULL,
  `venueeditrec` tinyint(4) NOT NULL,
  `venueowner` tinyint(4) NOT NULL,
  `lightbox` tinyint(4) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `showstate` tinyint(4) NOT NULL,
  `statename` varchar(200) NOT NULL,
  `statewidth` varchar(60) NOT NULL,
  `lastupdate` varchar(60) NOT NULL default '',
  UNIQUE KEY `id` (`id`)
) TYPE = MYISAM;

INSERT INTO `jos_eventlist_settings` VALUES (1, 0, 1, 0, 1, 1, 1, 0, '', '100%', '15%', '25%', '20%', '20%', 'Date', 'Title', 'Venue', 'City', '%d.%m.%Y', '%H.%M', 'h', 1, 0, 1, 1, 1, 1, 1, 1, -2, 0, 'example@example.com', '', '1000', -2, -2, -2, 1, '20%', 'Type', 1, 1, 1, 1, '100', '100', '100', 1, 0, 1, 0, 0, 1, 2, 2, -2, 1, 0, -2, 1, 0, 0, '[title], [a_name], [catsid], [times]', '', 0, 'State', '', '1174491851');