<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Votes_model extends CI_Model
{
	private $EE;

	public function __construct()
	{
		$this->EE =& get_instance();
	}

	public function create( $params=array() )
	{
		$defaults = array(
			'poll_id'       => 0,
			'option_id'     => 0,
			'member_id'     => 0,
			'creation_date' => time(),
			'ip_address'    => '0.0.0.0'
		);
		$params = array_merge($defaults, $params);

		$this->EE->db->insert('polls_votes', $params);
		return $this->EE->db->insert_id();
	}

	public function retrieve( $poll_id )
	{
		return $this->_retrieve($poll_id, false);
	}

	/*
	 * Show all votes, including those from guests
	 */
	public function retrieve_all( $poll_id )
	{
		return $this->_retrieve($poll_id, true);
	}

	private function _retrieve( $poll_id, $show_guests )
	{
		$this->EE->db->select('polls_votes.id');
		$this->EE->db->select('polls_votes.data');
		$this->EE->db->select('polls_votes.poll_id');
		$this->EE->db->select('polls_votes.creation_date');
		$this->EE->db->select('polls_votes.ip_address');
		$this->EE->db->select('polls_options.value as option_value');
		$this->EE->db->select('members.username');
		$this->EE->db->from('polls_votes');
		$this->EE->db->join('polls_options', 'polls_options.id = polls_votes.option_id');
		
		if($show_guests)
		{
			$this->EE->db->join('members', 'members.member_id = polls_votes.member_id', 'left outer');
		}
		else
		{
			$this->EE->db->join('members', 'members.member_id = polls_votes.member_id');
		}

		$this->EE->db->where('polls_votes.poll_id', $poll_id);
		$poll_votes = $this->EE->db->get();
		return $poll_votes->result();
	}

	public function user_voted( $member_id, $poll_id )
	{
		$has_votes = $this->EE->db->get_where('polls_votes', array('member_id'=>(int)$member_id, 'poll_id'=>(int)$poll_id), 1);
		$has_votes = $has_votes->result();
		return empty($has_votes) ? FALSE : TRUE;
	}
}

/* End of File: options_model.php */
