<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Polls_mcp
{
	private $EE;
	private $base_url;
	private $data = array();

	public function __construct()
	{
		$this->EE =& get_instance();
		$this->base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls';
		$this->data['base_url'] = $this->base_url;

		// update breadcrumbs
		$this->EE->cp->set_breadcrumb($this->base_url.AMP.'method=index', $this->EE->lang->line('polls_module_name'));

		// set navigation
		$this->EE->cp->set_right_nav(array(
			'polls_dashboard'	=> $this->base_url.AMP.'method=index',
			'polls_create'		=> $this->base_url.AMP.'method=create'
		));

		// load models
		$this->EE->load->model('poll_model');
		$this->EE->load->model('options_model');
		$this->EE->load->model('votes_model');
	}

	public function index()
	{
		// load libraries
		$this->EE->load->library('table');
		// load helpers
		$this->EE->load->helper(array('string', 'date'));

		$this->EE->db->order_by('id', 'desc');
		$this->data['polls_listing'] = $this->EE->poll_model->retrieve();

		// load javascript tablesorter
		$this->EE->cp->add_js_script(array('plugin'=>'tablesorter'));
		$this->EE->jquery->tablesorter('#polls_listing', '{
			textExtraction: "complex",
			widgets: ["zebra"],
			headers: { 4:{sorter:false} }
		}');

		// Set page title
		if( $this->EE->config->item('app_version') < '260' ) 
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('polls_module_name'));
		}
		else
		{
			$this->EE->view->cp_page_title = $this->EE->lang->line('polls_module_name');
		}
		return $this->EE->load->view('index', $this->data, TRUE);
	}

	public function create()
	{
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
				// create poll
				$poll_id = $this->EE->poll_model->create(array(
					'site_id' => $this->EE->input->post('site_id'),
					'question' => $this->EE->input->post('question', TRUE),
					'status' => $this->EE->input->post('poll_status'),
					'limit_votes' => $this->EE->input->post('limit_votes'),
					'member_id' => $this->EE->session->userdata('member_id'),
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

				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('polls_create_message_success'));
			}
			else
			{
				$this->EE->session->set_flashdata('message_failure', validation_errors());
			}

			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=edit'.AMP.'poll='.$poll_id);
		}

		// get available sites
		$this->data['sites_listing'] = array();
		$sites_listing = $this->EE->db->get('sites');
		$sites_listing = $sites_listing->result();
		foreach( $sites_listing as $row )
		{
			$this->data['sites_listing'][$row->site_id] = $row->site_label;
		}

		// load javascript and css
		$this->EE->cp->add_js_script('ui', 'datepicker');

		$this->EE->cp->add_to_foot('<script type="text/javascript" charset="utf-8" src="'.$this->EE->config->item('theme_folder_url').'third_party/polls/js/polls.js" ></script>');
		$this->EE->cp->add_to_head('<link rel="stylesheet" href="'.$this->EE->config->item('theme_folder_url').'third_party/polls/css/polls_cp.css" />');

		// Set page title
		if( $this->EE->config->item('app_version') < '260' ) 
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('polls_create_new_poll_title'));
		}
		else
		{
			$this->EE->view->cp_page_title = $this->EE->lang->line('polls_create_new_poll_title');
		}

		return $this->EE->load->view('create', $this->data, TRUE);
	}

	public function edit()
	{
		$poll_id = $this->EE->input->get('poll');

		// load libraries
		$this->EE->load->library('form_validation');

		// set validation rules
		$this->EE->form_validation->set_rules('question', 'question', 'required');
		$this->EE->form_validation->set_rules('options',' options', 'required|callback_valid_options');

		// process submission
		if( $this->EE->input->post('submit') )
		{
			$poll_id = $this->EE->input->post('poll_id');

			if( $this->EE->form_validation->run() )
			{
				// update poll
				$this->EE->poll_model->update($poll_id, array(
					'site_id' => $this->EE->input->post('site_id'),
					'question' => $this->EE->input->post('question', TRUE),
					'status' => $this->EE->input->post('poll_status'),
					'limit_votes' => $this->EE->input->post('limit_votes'),
					'member_id' => $this->EE->session->userdata('member_id'),
				));

				// create poll options
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

				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('polls_update_message_success'));
			}
			else
			{
				$this->EE->session->set_flashdata('message_failure', validation_errors());
			}

			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=edit'.AMP.'poll='.$poll_id);
		}

		// get poll
		$this->data['poll'] = $this->EE->poll_model->retrieve($poll_id);
		$this->data['poll'] = $this->data['poll'];

		// get poll options
		$this->data['poll_options'] = $this->EE->options_model->retrieve($poll_id);

		// get available sites
		$this->data['sites_listing'] = array();
		$sites_listing = $this->EE->db->get('sites');
		$sites_listing = $sites_listing->result();
		foreach( $sites_listing as $row )
		{
			$this->data['sites_listing'][$row->site_id] = $row->site_label;
		}

		// load javascript and css
		$this->EE->cp->add_js_script('ui', 'datepicker');
		$this->EE->cp->add_to_foot('<script type="text/javascript" charset="utf-8" src="'.$this->EE->config->item('theme_folder_url').'third_party/polls/js/polls.js" ></script>');
		$this->EE->cp->add_to_head('<link rel="stylesheet" href="'.$this->EE->config->item('theme_folder_url').'third_party/polls/css/polls_cp.css" />');

		// Set page title
		if( $this->EE->config->item('app_version') < '260' ) 
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('polls_edit_new_poll_title'));
		}
		else
		{
			$this->EE->view->cp_page_title = $this->EE->lang->line('polls_edit_new_poll_title');
		}

		return $this->EE->load->view('edit', $this->data, TRUE);
	}

	public function delete()
	{
		$poll_id = $this->EE->input->get('poll');
		$confirm = $this->EE->input->get('confirm', FALSE);

		if( empty($poll_id) )
		{
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=index');
		}

		if( $confirm == TRUE )
		{
			// delete the poll and all related data
			$this->EE->poll_model->delete($poll_id);

			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('polls_delete_message_success'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=index');
		}

		// retrieve poll
		$this->data['poll'] = $this->EE->poll_model->retrieve($poll_id);

		// Set page title
		if( $this->EE->config->item('app_version') < '260' ) 
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('polls_delete_poll_title'));
		}
		else
		{
			$this->EE->view->cp_page_title = $this->EE->lang->line('polls_delete_poll_title');
		}

		return $this->EE->load->view('delete', $this->data, TRUE);
	}

	public function reset()
	{
		$poll_id = $this->EE->input->post('poll');

		if( !empty($poll_id) )
		{
			$this->EE->poll_model->reset($poll_id);
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('polls_reset_message_success'));
		}

		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=index');
	}

	public function show()
	{
		$poll_id = $this->EE->input->get('poll');

		// setup pagination
		$this->EE->load->library('pagination');

		$this->EE->db->from('polls_votes');
		$this->EE->db->where('poll_id', (int)$poll_id);
		$total_votes = $this->EE->db->count_all_results();

		$config = array();
		$config['base_url'] = $this->base_url.AMP.'method=show'.AMP.'poll='.$poll_id;
		$config['page_query_string'] = TRUE;
		$config['total_rows'] = $total_votes;
		$config['per_page'] = 25;

		$this->EE->pagination->initialize($config);

		$this->data['pagination'] = $this->EE->pagination->create_links();

		$current_page = $this->EE->input->get('per_page');
		$current_page = empty($current_page) ? 0 : $current_page;

		// retrieve poll
		$this->data['poll'] = $this->EE->poll_model->retrieve($poll_id);
		$this->data['poll_options'] = $this->EE->options_model->retrieve($poll_id);
		$this->EE->db->limit($config['per_page'], $current_page);
		$this->data['poll_votes'] = $this->EE->votes_model->retrieve_all($poll_id);

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

		// Set page title
		if( $this->EE->config->item('app_version') < '260' ) 
		{
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('polls_show_poll_title'));
		}
		else
		{
			$this->EE->view->cp_page_title = $this->EE->lang->line('polls_show_poll_title');
		}

		return $this->EE->load->view('show', $this->data, TRUE);
	}

	public function valid_options( $options )
	{
		// make sure there are at least 2 options being returned
		if( count($options) >= 2 )
		{
			$new_options = array();

			// check if any of the options are empty. If one is found to be empty, remove it.
			foreach( $options as $option )
			{
				if($option != '')
				{
					$new_options[] = $option;
				}
			}

			// if we have at least two options filled out, validation has passed.
			if( count($new_options) >= 2 )
			{
				return $new_options;
			}
			else
			{
				$this->EE->form_validation->set_message('valid_options', $this->EE->lang->line('polls_create_message_invalid_options'));
				return FALSE;
			}
		}
		else
		{
			$this->EE->form_validation->set_message('valid_options', $this->EE->lang->line('polls_create_message_invalid_options'));
			return FALSE;
		}
	}
}

/* End of File: mcp.polls.php */
