<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

// load required classes
require_once PATH_THIRD.'polls/mod.polls.php';

class Polls_ft extends EE_Fieldtype
{
	public $info = array(
		'name'    => 'Polls',
		'version' => '1.4'
	);

	public function __construct()
	{
		$this->EE =& get_instance();
		
		if( $this->EE->config->item('app_version') < '260' ) 
		{
			parent::EE_Fieldtype();
		}
		else
		{
			parent::__construct();
		}
		

		$this->EE->lang->loadfile('polls');
	}

	public function display_field( $data )
		{
			// load models
			$this->EE->load->model('poll_model');
			$this->EE->load->model('options_model');
			$this->EE->load->model('votes_model');

			$poll_id = FALSE;

			// get current poll_id if any
			$this->EE->db->where('entry_id', $this->EE->input->get_post('entry_id'));
			$result = $this->EE->db->get('channel_data');
			$result = $result->row('field_id_'.$this->field_id);

			$poll_id = empty($result) ? FALSE : (int)$result;

			// load javascript and css
			$this->EE->cp->add_to_foot('<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>');
			$this->EE->cp->add_to_foot('<script type="text/javascript" charset="utf-8" src="'.$this->EE->config->item('theme_folder_url').'third_party/polls/js/polls.js" ></script>');

			$this->EE->cp->add_js_script('plugin', 'datepicker');
			$this->EE->cp->add_js_script('plugin', 'tablesorter');
			$this->EE->cp->add_to_head('<link rel="stylesheet" href="'.$this->EE->config->item('theme_folder_url').'third_party/polls/css/polls_cp.css" />');

			$polls = $this->EE->poll_model->retrieve();
			
			$c = 1;
			if( empty($polls) )
			{
				$polls_dropdown = array('0' => 'Create New');
			}
			else
			{
				foreach($polls as $poll)
				{
					if(!empty($poll_id) && $poll_id == $poll->id)
					{
						$polls_dropdown[$poll->id] = 'Edit: ' . $poll->id . ' - ' . $poll->question;
					}
					else
					{
						if($c == 1 && empty($poll_id))
							$polls_dropdown = array('' => 'Create New');

						$polls_dropdown[$poll->id] = 'ID: ' . $poll->id . ' - ' . $poll->question;
					}

					$c++;
				}
			}
			
			if( !empty($poll_id) )
			{
				//option to remove poll
				$polls_dropdown['_remove_'] = 'Remove poll';
				
				// retrieve poll
				$poll = $this->EE->poll_model->retrieve($poll_id);

				// retrieve poll options
				$poll_options = $this->EE->options_model->retrieve($poll_id);

				// retrieve poll votes
				$poll_votes = $this->EE->votes_model->retrieve($poll_id);

				if( !empty($poll) )
				{
					// load javascript tablesorter
					$this->EE->cp->add_js_script(array('plugin'=>'tablesorter'));
					$this->EE->jquery->tablesorter('#poll_options', '{
						textExtraction: "complex",
						widgets: ["zebra"]
					}');

					$this->EE->jquery->tablesorter('#poll_votes', '{
						textExtraction: "complex",
						widgets: ["zebra"]
					}');

					return $this->EE->load->view('fieldtype_show', array('poll'=>$poll, 'poll_options'=>$poll_options, 'poll_votes'=>$poll_votes, 'polls_dropdown' => $polls_dropdown, 'poll_selected' => $poll_id), TRUE);
				}
			}

			return $this->EE->load->view('fieldtype_create', array('polls_dropdown' => $polls_dropdown), TRUE);
		}

	public function save( $data )
	{
		// load models
		$this->EE->load->model('poll_model');
		$this->EE->load->model('options_model');
		$this->EE->load->model('votes_model');
		
		$selected_poll = $this->EE->input->post('poll_selector');
		$poll_question = $this->EE->input->post('question');
		$poll_options  = $this->EE->input->post('options');

		$poll_id = $poll_id = $this->EE->input->post('poll_id');

		if( !empty($poll_id))
		{
			//this means the choose a different poll for the field type.
			if($selected_poll != $poll_id)
			{
				return $selected_poll;
			}
			// TODO abstract this to the model
			// load libraries
			$this->EE->load->library('form_validation');

			// set validation rules
			$this->EE->form_validation->set_rules('question', 'question', 'required');
			$this->EE->form_validation->set_rules('options',' options', 'required|callback_valid_options');

			// process submission
			if( $this->EE->input->post('submit') )
			{
				if( $this->EE->form_validation->run() )
				{
					// update poll
					$this->EE->poll_model->update($poll_id, array(
						'site_id' => $this->EE->config->config['site_id'],
						'question' => $this->EE->input->post('question', TRUE),
						'status' => $this->EE->input->post('poll_status'),
						'limit_votes' => $this->EE->input->post('limit_votes'),
						'member_id' => $this->EE->session->userdata('member_id'),
					));

					// update poll options
					$poll_options = $this->EE->input->post('options');
					foreach( $poll_options as $option )
					{
						foreach( $option as $key => $val )
						{
							$this->EE->options_model->update($key, array(
								'value' => $val,
								'poll_id' => $poll_id
							));
						}
					}
				}
				else
				{
					return NULL;
				}

			}
			return $poll_id;
		}

		if( !empty($selected_poll) )
		{
			return $selected_poll;
		}
		else if( !empty($poll_question) AND isset($poll_options[0]) AND isset($poll_options[1]) )
		{
			// create poll
			$poll_id = $this->EE->poll_model->create(array(
				'question'        => $this->EE->input->post('question', TRUE),
				'status'          => $this->EE->input->post('poll_status'),
				'limit_votes'     => $this->EE->input->post('limit_votes'),
				'member_id'       => $this->EE->session->userdata('member_id')
			));

			// create poll options
			$poll_options = $this->EE->input->post('options');
			foreach( $poll_options as $option )
			{
				$this->EE->options_model->create(array(
					'value' => $option,
					'poll_id' => $poll_id
				));
			}

			// check for "oher" poll option
			$poll_other = $this->EE->input->post('other');
			if( !empty($poll_other) )
			{
				$this->EE->options_model->create(array(
					'value' => $poll_other,
					'poll_id' => $poll_id,
					'is_other' => 'true',
				));
			}

			return $poll_id;
		}
	}


	// replacing the weblog tags.
	// if output_html = 1 in params we'll go ahead and render is here
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		if(!empty($params))
		{
			$mod = new Polls();

			return $mod->poll($data, $params);
		}

		return $data;
	}
}

/* End of File: ft.polls.php */
