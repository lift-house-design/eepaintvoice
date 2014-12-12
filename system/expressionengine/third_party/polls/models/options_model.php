<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Options_model extends CI_Model
{
	private $EE;

	public function __construct()
	{
		$this->EE =& get_instance();
	}

	public function create( $params=array() )
	{
		$defaults = array(
			'value'     => '',
			'poll_id'   => 0
		);
		$params = array_merge($defaults, $params);

		$this->EE->db->insert('polls_options', $params);
		return $this->EE->db->insert_id();
	}

	public function retrieve( $poll_id )
	{
		$this->EE->db->select('*');
		$this->EE->db->select('(SELECT COUNT(id) FROM '.$this->EE->db->dbprefix('polls_votes').' WHERE poll_id='.$poll_id.' AND option_id = '.$this->EE->db->dbprefix('polls_options').'.id) AS votes');
		$this->EE->db->order_by('id', 'asc');
		$poll_options = $this->EE->db->get_where('polls_options', array('poll_id' => (int)$poll_id));
		return $poll_options->result();
	}

	public function update( $option_id, $params=array() )
	{
		$this->EE->db->where('id', (int)$option_id);
		return $this->EE->db->update('polls_options', $params);
	}

	public function delete( $option_id )
	{
		return $this->EE->db->delete('polls_options', array('id' => (int)$option_id));
	}

	public function valid_option( $option_id, $poll_id )
	{
		$poll_option = $this->EE->db->get_where('polls_options', array(
			'id' => $option_id,
			'poll_id' => $poll_id,
		), 1);
		return $poll_option->row();
	}
}

/* End of File: options_model.php */
