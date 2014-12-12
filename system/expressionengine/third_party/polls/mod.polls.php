<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Polls
{
	public $return_data = NULL;
	public $isAJAX = FALSE;

	public function __construct()
	{
		$this->EE =& get_instance();

		// load models
		$this->EE->load->model('poll_model');
		$this->EE->load->model('options_model');
		$this->EE->load->model('votes_model');

		// Call lang file
		$this->EE->lang->loadfile('polls');

		// Set AJAX variable
		if( $this->EE->input->is_ajax_request() )
			$this->isAJAX = TRUE;

		// start session if it hasn't been already
		if( session_id() == '' ) session_start();
	}

	/**
	 * Module Tag Processing
	 *
	 * {exp:polls:poll}
	 *
	 * Params:
	 *
	 *	poll_id (required)
	 *		Accepts a number representing the poll_id or the words "newest" for
	 *		the latest poll or "random" for a random poll.
	 *
	 *	output_html (optional)
	 *		If this parameter is set, the default poll style will be displayed.
	 *
	 *
	 * Conditionals:
	 *
	 *	poll_is_open
	 *		Controls whether the poll is displayed or not.
	 *
	 *	poll_limit_votes
	 *		Returns TRUE if users have a limited number of votes
	 *
	 *	poll_show_vote
	 *		Returns TRUE if the user is on the vote page
	 *
	 *	poll_show_results
	 *		Returns TRUE if the user is on the results page
	 *
	 *
	 * Variables:
	 *
	 *	poll_id
	 *		Outputs poll id
	 *
	 *	poll_question
	 *		Outputs poll question
	 *
	 *	poll_total_votes
	 *		Outputs the total number of votes on the poll
	 *
	 *	poll_options
	 *		Loops through the poll options
	 *
	 *	option_input
	 *		Outputs the option input in the options loop
	 *
	 *	option_label
	 *		Outputs the option label in the options loop
	 *
	 *	option_value
	 *		Outputs the option value in the options loop
	 *
	 *	option_votes
	 *		Outputs the option votes in the options loop
	 *
	 *	option_ratio
	 *		Outputs the option ratio in the options loop
	 *
	 *	poll_form
	 *		Outputs the poll form html
	 *
	 *	poll_form_errors
	 *		Outputs the poll form errors
	 *
	 *	poll_form_submit
	 *		Outputs the poll form submit button
	 *
	 *	poll_view_results
	 *		Outputs the poll view results button
	 *
	 *	poll_view_option
	 *		Outputs the poll view options button
	 *
	 *	has_voted
	 *		outputs if the current visitor has voted yet
	 *
	 */
	public function get_ids()
	{
		$poll_status = $this->EE->TMPL->fetch_param('status');

		$tag_data = '';

		if( !empty($poll_status) )
		{
			$query = $this->EE->db->get_where('polls', array('status' => $poll_status));
			$query = $query->result_array();

			foreach($query as $key => $poll)
				$tag_data .= str_replace('{poll_id}', $poll['id'], $this->EE->TMPL->tagdata);

		}

		return $tag_data;
	}

	public function poll( $args = array() )
	{
		$this->EE->load->helper('form');
		$variables = array();

		$defaults = array(
							'poll_id' => null,
							'output_html' => 0,
							'return_url' => $this->EE->uri->uri_string(),
							'submit' => lang('polls_submit_value'),
							'view_results' => lang('polls_view_results_value'),
							'view_options' => lang('polls_view_options_value'),
							'bar_color' => '#ccc'
						);

		$args = array_merge($defaults, $args);

		// return nothing if the poll_id has not been set
		$poll_id = ( $args['poll_id'] !== null ) ? $args['poll_id'] : $this->EE->TMPL->fetch_param('poll_id');
		if( empty($poll_id) ) return 'Please specify a poll.';
		if( $poll_id == '_remove_')	return FALSE;

		// added this for additional support on random monde when someone wants to view results.
		if(!empty($_SESSION['polls_module']))
		{
			$key = key($_SESSION['polls_module']);
			if(!empty($_SESSION['polls_module'][$key]['poll_id']))
			{
				$poll_id = $_SESSION['polls_module'][$key]['poll_id'];
				// added unset here to blow out polls data after the show results is present
				unset($_SESSION['polls_module'][$poll_id]['poll_id']);
			}
		}

		// get poll data
		$poll = $this->EE->poll_model->retrieve($poll_id);
		if( empty($poll) ) return;

		$variables['poll_id'] = $poll->id;
		$variables['poll_question'] = $poll->question;
		$variables['poll_total_votes'] = $poll->total_votes;
		$variables['poll_status'] = $poll->status;
		$variables['poll_limit_votes'] = ($poll->limit_votes == 'yes') ? TRUE : FALSE;

		// get poll options
		$poll_options = $this->EE->options_model->retrieve((in_array($poll_id, array('newest','random')) ? $poll->id : $poll_id));
		if( empty($poll_options) ) return;

		foreach( $poll_options as $option )
		{
			$variables['poll_options'][] = array(
				'option_id' => $option->id,
				'option_value' => $option->value,
				'option_votes' => $option->votes,
				'option_ratio' => (empty($option->votes) OR empty($poll->total_votes)) ? 0 : round(($option->votes/$poll->total_votes)*100, 2),
				'option_label' => $option->value,
				'option_label_html' => '<label for="poll-'.$poll->id . '-option-'. $option->id.'">'.$option->value.'</label>',
				'option_input' => '<input type="radio" name="option" value="' . $option->id . '" id="poll-'.$poll->id . '-option-'. $option->id.'" />',
				'option_is_other' => ($option->is_other == 'true') ? TRUE : FALSE,
				'option_input_other' => '<input type="text" name="other" value="" />',
			);
		}
		$variables['result_options'] = $variables['poll_options'];

		// prep params
		// note: this looks cleaner but could be expanded out to 1 conditional check instead of 5...
		$submit = ( $this->isAJAX ) ? $args['submit'] : $this->EE->TMPL->fetch_param('submit', $defaults['submit']);
		$view_results = ( $this->isAJAX ) ? $args['view_results'] : $this->EE->TMPL->fetch_param('view_results', $defaults['view_results']);
		$view_options = ( $this->isAJAX ) ? $args['view_options'] : $this->EE->TMPL->fetch_param('view_options', $defaults['view_options']);
		$output_html = ( $this->isAJAX ) ? $args['output_html'] : $this->EE->TMPL->fetch_param('output_html', $defaults['output_html']);
		$return_url = ( $this->isAJAX ) ? $args['return_url'] : $this->EE->TMPL->fetch_param('return_url', $defaults['return_url']);
		$bar_color = ( $this->isAJAX ) ? $args['bar_color'] : $this->EE->TMPL->fetch_param('bar_color', $defaults['bar_color']);

		//pack up the params and store them in session so we can render the correct template data through AJAX call
		$_SESSION['polls_module'][$poll->id]['tag_args'] = compact('submit', 'view_results', 'view_options', 'output_html', 'bar_color');

		// additional tags
		$variables['poll_is_open'] = ($poll->status == 'open') ? TRUE : FALSE;
		$variables['has_voted'] = $this->_has_voted($poll->id);

		$variables['poll_form_submit'] = '<input type="submit" name="poll_submit" value="' . $submit . '" />';
		$variables['poll_view_results'] = '<input type="submit" name="poll_results" value="' . $view_results . '" />';
		$variables['poll_view_options'] = '<input type="submit" name="poll_options" value="' . $view_options . '" />';
		$variables['bar_color'] = $bar_color;

		$variables['poll_form_errors'] = empty($_SESSION['polls_module'][$poll->id]['error']) ? '' : $_SESSION['polls_module'][$poll->id]['error'];

		if( ! empty($_SESSION['polls_module'][$poll->id]['poll_show_vote']) || ! empty($_SESSION['polls_module'][$poll->id]['poll_show_results']) )
		{
			$variables['poll_show_vote'] = $_SESSION['polls_module'][$poll->id]['poll_show_vote'];
			$variables['poll_show_results'] = $_SESSION['polls_module'][$poll->id]['poll_show_results'];
		}
		else
		{
			if( $variables['poll_limit_votes'] AND $variables['has_voted'] )
			{
				$variables['poll_show_vote'] = FALSE;
				$variables['poll_show_results'] = TRUE;
			}
			else
			{
				$variables['poll_show_vote'] = TRUE;
				$variables['poll_show_results'] = FALSE;
			}
		}

		$variables['action_url'] = $this->_get_action_url();
		$variables['return_url'] = $return_url;

		// output default template html if parameter is set
		if( $output_html )
		{
			ob_start();
			extract($variables);
			include PATH_THIRD . 'polls/views/poll_template.php';
			$template_html = ob_get_contents();
			ob_end_clean();

			if( $this->isAJAX )
			{
				return array('success' => TRUE, 'html' => $template_html);
			}
			else
			{
				return $template_html;
			}
		}

		if( !$this->isAJAX ) {
			$this->EE->TMPL->tagdata = str_replace('{poll_form}', $this->EE->functions->form_declaration(array('action' => $variables['action_url'])), $this->EE->TMPL->tagdata);

			$this->EE->TMPL->tagdata = str_replace('{/poll_form}', '<input type="hidden" name="poll_id" value="' . $poll->id . '" /><input type="hidden" name="return_url" value="'.$variables['return_url'].'" /></form>', $this->EE->TMPL->tagdata);
		}

		// either return an array for JSON encoded AJAX response
		// or parse the template variables for EE
		return ( $this->isAJAX ) ? array('success' => TRUE, 'poll_vars' => array($variables)) : $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, array($variables));
	}

	public function vote_submit()
	{
		if( !$this->EE->input->post('append_poll_id') )
			$previous_page = $this->EE->functions->fetch_site_index(TRUE) . $this->EE->input->post('return_url') . '/#poll-' . $this->EE->input->post('poll_id');
		else
			$previous_page = $this->EE->functions->fetch_site_index(TRUE) . $this->EE->input->post('return_url');

		// get vote data
		$poll_id = $this->EE->input->post('poll_id');
		$option_id = $this->EE->input->post('option');
		$option_other = $this->EE->input->post('other');

		// tag args from original template call, persisted for AJAX calls
		$tag_args = $_SESSION['polls_module'][$poll_id]['tag_args'];

		$cookie = $this->_get_cookie();
		$cookie = empty($cookie) ? array() : $cookie;

		// clear polls session variable
		$_SESSION['polls_module'] = array();

		// get poll data
		$poll = $this->EE->poll_model->retrieve($poll_id);
		$option = $this->EE->options_model->valid_option($option_id, $poll->id);

		// if only 'show results' was clicked, redirect
		if( $this->EE->input->post('poll_results') )
		{
			$_SESSION['polls_module'][$poll->id]['poll_show_vote'] = FALSE;
			$_SESSION['polls_module'][$poll->id]['poll_show_results'] = TRUE;
			$_SESSION['polls_module'][$poll->id]['poll_id'] = $poll->id;

			// return JSON response or send back to last page
			if( $this->isAJAX )
			{
				$this->EE->output->send_ajax_response( $this->poll( array_merge($tag_args, array('poll_id' => $poll_id)) ) );
			}
			else
			{
				$this->EE->functions->redirect($previous_page);
			}
		}

		// if only 'show options' was clicked, redirect
		if( $this->EE->input->post('poll_options') )
		{
			$_SESSION['polls_module'][$poll->id]['poll_show_vote'] = TRUE;
			$_SESSION['polls_module'][$poll->id]['poll_show_results'] = FALSE;
			$_SESSION['polls_module'][$poll->id]['poll_id'] = $poll->id;

			// return JSON response or send back to last page
			if( $this->isAJAX )
			{
				$this->EE->output->send_ajax_response( $this->poll( array_merge($tag_args, array('poll_id' => $poll_id)) ) );
			}
			else
			{
				$this->EE->functions->redirect($previous_page);
			}
		}

		// load library
		$this->EE->load->library('form_validation');
		$this->EE->form_validation->set_rules('option', 'option', 'required');
		$this->EE->form_validation->set_message('required', 'Please select one option');

		// require "other" input if selected option is "other"
		if( !empty($option->is_other) && $option->is_other == 'true' )
			$this->EE->form_validation->set_rules('other', 'other', 'required|xss_clean');

		$_SESSION['polls_module'][$poll->id]['poll_show_vote'] = FALSE;
		$_SESSION['polls_module'][$poll->id]['poll_show_results'] = TRUE;

		if( $this->EE->form_validation->run() )
		{
			if( ($poll->limit_votes == 'no') OR ($poll->limit_votes == 'yes' AND !$this->_has_voted($poll->id)) )
			{
				// add vote
				$this->EE->votes_model->create(array(
					'poll_id' => $poll->id,
					'option_id' => $option->id,
					'member_id' => $this->EE->session->userdata['member_id'],
					'ip_address' => $this->EE->session->userdata['ip_address'],
					'data' => empty($option_other) ? '' : $option_other,
				));
			}

			$_SESSION['polls_module'][$poll->id]['poll_show_vote'] = FALSE;
			$_SESSION['polls_module'][$poll->id]['poll_show_results'] = TRUE;
			$_SESSION['polls_module'][$poll->id]['poll_id'] = $poll->id;
			$cookie = array($poll->id => TRUE) + $cookie; // appending voted poll_id to existing cookie array
		}
		else
		{
			$_SESSION['polls_module'][$poll->id]['poll_show_vote'] = TRUE;
			$_SESSION['polls_module'][$poll->id]['poll_show_results'] = FALSE;

			$_SESSION['polls_module'][$poll->id]['error'] = validation_errors('<div class="error">', '</div>');
		}

		$this->_save_cookie($cookie);

		// return JSON response or send back to last page
		if( $this->isAJAX )
		{
			$this->EE->output->send_ajax_response( $this->poll( array_merge($tag_args, array('poll_id' => $poll_id)) ) );
		}
		else
		{
			$this->EE->functions->redirect($previous_page);
		}
	}

	private function _has_voted( $poll_id )
	{
		$cookie = $this->_get_cookie();
		return empty($cookie[$poll_id]) ? FALSE : TRUE;
	}

	private function _get_action_url()
	{
		// build trigger url
		$trigger_url = $this->EE->functions->fetch_site_index(0);

		$action_id = $this->EE->db->get_where('actions', array('class' => 'Polls', 'method' => 'vote_submit'), 1);
		$action_id = $action_id->row('action_id');

		$trigger_url .= QUERY_MARKER . 'ACT=' . $action_id;

		return $trigger_url;
	}

	private function _save_cookie( $data )
	{
		$expires = 60*60*24*365; // 365 days
		$data = in_array(gettype($data), array('array','object')) ? serialize($data) : $data;
		$this->EE->functions->set_cookie('polls', $data, $expires);
	}

	private function _get_cookie()
	{
		$cookie = $this->EE->input->cookie('polls');
		return @unserialize($cookie) ? unserialize($cookie) : $cookie;
	}
}

/* End of File: mod.polls.php */
