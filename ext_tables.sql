#
# Add SQL definition of database tables
#

#
# Table structure for table 'tx_errorlog_domain_model_error'
#
CREATE TABLE tx_errorlog_domain_model_error (
        uid int(11) NOT NULL auto_increment,
        PRIMARY KEY (uid),
        page_uid int(11) DEFAULT '0' NOT NULL,
        root_page_uid int(11) DEFAULT '0' NOT NULL,
        message text,
        code int DEFAULT '0' NOT NULL,
        file varchar(1024) DEFAULT '' NOT NULL,
        line int DEFAULT '0' NOT NULL,
        date int DEFAULT '0' NOT NULL,
        trace LONGTEXT DEFAULT '',
        browser_info LONGTEXT DEFAULT '',
        server_name varchar(255) DEFAULT '' NOT NULL,
        request_uri varchar(1024) DEFAULT '' NOT NULL,
        crdate int DEFAULT '0' NOT NULL,
        deleted tinyint(4) DEFAULT '0' NOT NULL,
        user_id int(11) unsigned DEFAULT '0' NOT NULL,
        workspace int(11) DEFAULT '0' NOT NULL,
        IP varchar(39) DEFAULT '' NOT NULL,
        data text,
        user varchar(255) DEFAULT '' NOT NULL,
        event_dispatched tinyint(1) DEFAULT '0' NOT NULL,
        channel varchar(255) DEFAULT '' NOT NULL,
);

#
# Table structure for table 'tx_errorlog_domain_model_settings'
#
CREATE TABLE tx_errorlog_domain_model_settings (
    general_enable tinyint(1) unsigned DEFAULT '0' NOT NULL,
    general_expire_days int(4) unsigned DEFAULT '0' NOT NULL,
    slack_enable tinyint(1) unsigned DEFAULT '0' NOT NULL,
    slack_auth_token varchar(255) DEFAULT '' NOT NULL,
    slack_channel_id varchar(31) DEFAULT '' NOT NULL,
    slack_report_type varchar(255) DEFAULT '' NOT NULL,
    slack_occurrence_type tinyint(1) unsigned DEFAULT 0 NOT NULL,
    openai_enable tinyint(1) unsigned DEFAULT '0' NOT NULL,
    openai_auth_token varchar(255) DEFAULT '' NOT NULL,
    openai_model varchar(63) DEFAULT '' NOT NULL,
    pre_prompt varchar(1024) DEFAULT '' NOT NULL,
);

#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
    errorlog_slack varchar(255) DEFAULT '' NOT NULL,
    errorlog_enable_email tinyint(1) unsigned DEFAULT '0' NOT NULL,
    errorlog_enable_slack tinyint(1) unsigned DEFAULT '0' NOT NULL,
    errorlog_report_type varchar(255) DEFAULT '' NOT NULL,
    errorlog_occurrence_type tinyint(1) unsigned DEFAULT 0 NOT NULL
);

#
# Table structure for table 'tx_errorlog_hashes'
#
CREATE TABLE tx_errorlog_hashes (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    error_hash VARCHAR(255) NOT NULL,
    error_uid INT NOT NULL,
    occurred_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY error_unique (error_hash)
);
