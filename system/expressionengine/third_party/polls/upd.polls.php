<?php
class Polls_upd
{
	var $version = '1.4';

	function __construct()
	{
		$this->EE =& get_instance();
	}

	function install()
	{
		// install module
		$this->EE->db->insert('modules', array(
			'module_name' => 'Polls',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		));

		// create action
		$this->EE->db->insert('actions', array(
			'class' => 'Polls',
			'method' => 'vote_submit',
		));

		$this->EE->db->query("CREATE TABLE IF NOT EXISTS `".$this->EE->db->dbprefix('polls')."` (
				`id` int(10) NOT NULL AUTO_INCREMENT,
				`question` varchar(250) NOT NULL DEFAULT '',
				`creation_date` int(10) NOT NULL DEFAULT '0',
				`expiration_date` int(10) NOT NULL DEFAULT '0',
				`status` enum('open','closed') NOT NULL DEFAULT 'open',
				`limit_votes` enum('yes','no') NOT NULL DEFAULT 'no',
				`results` enum('after_vote','after_expired','never') NOT NULL DEFAULT 'after_vote',
				`member_id` int(10) NOT NULL DEFAULT '0',
				`site_id` INT(6) NOT NULL DEFAULT '1',
			PRIMARY KEY (`id`));");

		$this->EE->db->query("CREATE TABLE IF NOT EXISTS `".$this->EE->db->dbprefix('polls_options')."` (
				`id` int(10) NOT NULL AUTO_INCREMENT,
				`value` varchar(250) NOT NULL DEFAULT '',
				`is_other` enum('true','false') NOT NULL DEFAULT 'false',
				`poll_id` int(10) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`));");

		$this->EE->db->query("CREATE TABLE IF NOT EXISTS `".$this->EE->db->dbprefix('polls_votes')."` (
				`id` int(10) NOT NULL AUTO_INCREMENT,
				`data` varchar(250) DEFAULT '',
				`poll_id` int(10) NOT NULL DEFAULT '0',
				`option_id` int(10) NOT NULL DEFAULT '0',
				`member_id` int(10) NOT NULL DEFAULT '0',
				`creation_date` int(10) NOT NULL DEFAULT '0',
				`ip_address` varchar(15) NOT NULL DEFAULT '0.0.0.0',
			PRIMARY KEY (`id`));");

		$this->EE->db->insert('actions', array(
			'class' => 'Polls',
			'method' => 'show'
		));

		return TRUE;
	}

	function update( $current = '' )
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}

		$this->EE->load->dbforge();

		if( version_compare($current, '1.1', '<') )
		{
			$fields = array('site_id' => array('type' => 'INT', 'constraint' => '6'));
			$this->EE->dbforge->add_column('polls', $fields);	
		}

		if( version_compare($current, '1.2', '<') )
		{
			$fields = array('limit_votes' => array('type' => 'ENUM', 'constraint' => "'yes', 'no'"));
			$this->EE->dbforge->add_column('polls', $fields);
		}

		if( version_compare($current, '1.2.1', '<') )
		{
			if( $this->EE->db->field_exists('site_id', 'polls_votes') )
			{
				$this->EE->dbforge->drop_column('polls_votes', 'site_id');
			}

			// create action
			$this->EE->db->insert('actions', array(
				'class' => 'Polls',
				'method' => 'vote_submit',
			));
		}

		if( version_compare($current, '1.2.2', '<') )
		{
			if( !$this->EE->db->field_exists('limit_votes', 'polls') )
				$this->EE->dbforge->add_column('polls', array('limit_votes' => array('type' => 'ENUM', 'constraint' => "'yes', 'no'")));

			// update polls with default site_id and limit_vote settings if they are NULL
			$this->EE->db->where('site_id IS NULL');
			$this->EE->db->update('polls', array('site_id' => 1));

			$this->EE->db->where('limit_votes IS NULL');
			$this->EE->db->update('polls', array('limit_votes' => 'no'));
		}

		if( version_compare($current, '1.3', '<') )
		{
			$fields = array('data' => array('type' => 'VARCHAR', 'constraint' => '250', 'default' => ''));
			$this->EE->dbforge->add_column('polls_votes', $fields);

			$fields = array('is_other' => array('type' => 'ENUM', 'constraint' => '"true","false"', 'default' => 'false'));
			$this->EE->dbforge->add_column('polls_options', $fields);
		}
		return TRUE;
	}

	function uninstall()
	{
		$this->EE->db->query("DELETE FROM exp_modules WHERE module_name = 'Polls'");

		$this->EE->db->query("DROP TABLE IF EXISTS ".$this->EE->db->dbprefix('polls'));
		$this->EE->db->query("DROP TABLE IF EXISTS ".$this->EE->db->dbprefix('polls_options'));
		$this->EE->db->query("DROP TABLE IF EXISTS ".$this->EE->db->dbprefix('polls_votes'));

		return TRUE;
	}
}

/* End of File: upd.polls.php */
