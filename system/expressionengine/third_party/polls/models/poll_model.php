<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Poll_model extends CI_Model
{
	private $EE;

	public function __construct()
	{
		$this->EE =& get_instance();
		$this->site_id = $this->EE->config->config['site_id'];
	}

	public function create( $params=array() )
	{
		$defaults = array(
			'site_id'			=> $this->site_id,
			'question'          => '',
			'creation_date'     => time(),
			'expiration_date'   => 0,
			'status'            => 'open',
			'limit_votes'       => 'no',
			'results'           => 'after_vote',
			'member_id'         => 0,
		);
		$params = array_merge($defaults, $params);

		$this->EE->db->insert('polls', $params);
		return $this->EE->db->insert_id();
	}

	public function retrieve( $poll_id=NULL )
	{
		$this->EE->db->select('*');
		$this->EE->db->select('(SELECT COUNT(id) FROM '.$this->EE->db->dbprefix('polls_votes').' WHERE poll_id='.$this->EE->db->dbprefix('polls').'.id) as total_votes');

		if( empty($poll_id) )
		{
			$polls = $this->EE->db->get('polls');
			return $polls->result();
		}
		elseif( $poll_id == 'newest' )
		{
			$query = $this->EE->db->where('site_id', $this->site_id);
			$query = $this->EE->db->where('status', 'open');
			$query = $this->EE->db->order_by("id", "desc");
			$poll = $this->EE->db->get('polls', 1);
		}
		elseif( $poll_id == 'random' )
		{
			$query = $this->EE->db->where('site_id', $this->site_id);
			$query = $this->EE->db->order_by('id', 'RANDOM');
			$poll = $this->EE->db->get('polls', 1);
		}
		else
		{
			$poll = $this->EE->db->get_where('polls', array('id' => (int)$poll_id));
		}

		return $poll->row();
	}

	public function update( $poll_id, $params=array() )
	{
		$this->EE->db->where('id', (int)$poll_id);
		return $this->EE->db->update('polls', $params);
	}

	public function delete( $poll_id )
	{
		$this->EE->db->delete('polls', array('id' => (int)$poll_id));
		$this->EE->db->delete('polls_options', array('poll_id' => $poll_id));
		$this->EE->db->delete('polls_votes', array('poll_id' => $poll_id));

		return TRUE;
	}

	public function reset( $poll_id )
	{
		$this->EE->db->delete('polls_votes', array('poll_id' => $poll_id));

		return TRUE;
	}
}

/* End of File: poll_model.php */
