#
# Table structure for table 'tx_timelinevis_domain_model_timeline'
#

DROP TABLE IF EXISTS tx_timelinevis_domain_model_timeline;
CREATE TABLE tx_timelinevis_domain_model_timeline (
	uid int(11) UNSIGNED NOT NULL auto_increment,
  pid int(11) UNSIGNED DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	range_start DATE,
	range_end DATE,
	enable_pagination tinyint(4) DEFAULT '0' NOT NULL,
	parent_id int(11) DEFAULT '0' NOT NULL,
	points int(11) unsigned DEFAULT '0' NOT NULL,
	description text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_timelinevis_timelines text
);

#
# Table structure for table 'tx_timelinevis_timeline_content'
#
CREATE TABLE tx_timelinevis_timeline_content (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	content_uid int(11) DEFAULT '0' NOT NULL,
	timeline_uid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_timelinevis_domain_model_point'
#
CREATE TABLE tx_timelinevis_domain_model_point (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	timeline int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	source varchar(255) DEFAULT '' NOT NULL,
	pointdate DATE,
	description text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
