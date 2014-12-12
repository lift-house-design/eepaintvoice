<?php
$lang = array(

// Required for MODULES page
'polls_module_name'			=> 'Polls',
'polls_module_description'	=> 'Polls for ExpressionEngine',

//----------------------------------------

// Control Panel Links
'polls_dashboard'	=> 'Dashboard',
'polls_create'		=> 'Create a Poll',

// Control Panel Titles
'polls_create_new_poll_title'	=> 'Create New Poll',
'polls_edit_new_poll_title'		=> 'Edited Poll',
'polls_delete_poll_title'		=> 'Delete Poll',
'polls_show_poll_title'			=> 'Poll Statistics',

// Poll listing table
'polls_listing_table_id'			=> 'ID',
'polls_listing_table_question'		=> 'Question',
'polls_listing_table_created'		=> 'Created',
'polls_listing_table_expires'		=> 'Expires',
'polls_listing_table_status'		=> 'Status',
'polls_listing_table_votes'			=> 'Votes',
'polls_listing_table_actions'		=> 'Actions',
'polls_listing_no_polls'			=> !defined('BASE') ? '' : 'Looks like you need to <a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=create'.'">Create a Poll</a>!',
'polls_listing_stats'				=> 'Stats',
'polls_listing_edit'				=> 'Edit',
'polls_listing_delete'				=> 'Delete',

// Poll inline results
'poll_inline_votes'		=> 'Votes:',
'poll_inline_results'	=> 'Result:',

// Poll options table
'polls_options_table_id'	=> 'ID',
'polls_options_table_value'	=> 'Option',
'polls_options_table_votes'	=> 'Votes',
'polls_options_table_ratio'	=> 'Result',

// Poll votes table
'polls_votes_table_id'		=> 'ID',
'polls_votes_table_answer'	=> 'Selected Answer',
'polls_votes_table_data'	=> '"Other" Data',
'polls_votes_table_member'	=> 'Member',
'polls_votes_table_date'	=> 'Date',
'polls_votes_table_ip'		=> 'IP Address',

// Form Submit Messages
'polls_create_message_success'			=> 'Poll created!',
'polls_update_message_success'			=> 'Poll updated!',
'polls_create_message_invalid_options'	=> 'At least two options must be submitted.',
'polls_reset_message_success' => 'Poll has been reset',
'polls_message_confirm_reset' => 'Are you sure you want to reset all votes?',

'polls_delete_message_success'	=> 'Poll deleted!',
'polls_delete_poll'				=> 'Are you sure you want to remove the following poll: ',
'polls_delete_poll_notice'		=> 'All data associated with this poll will be permanently deleted!',
'polls_delete_poll_submit'		=> 'Delete Poll',

// Form buttons
'polls_submit_value'		=> 'Vote',
'polls_view_results_value'	=> 'View Results',
'polls_view_options_value'	=> 'Cast Your Vote',

// Polls create / edit labels
'polls_label_question'		=> 'Question',
'polls_label_option'		=> 'Option',
'polls_label_value'			=> 'Value',
'polls_label_options'		=> 'Options',
'polls_label_add_option'	=> 'Add Option',
'polls_label_add_status'	=> 'Status',
'polls_label_limit_votes'	=> 'Limit Votes',
'polls_label_site_id'		=> 'Site ID',
'polls_label_remove'		=> '[remove]',
'polls_label_add_option_other' => 'Add "Other" Option',
'polls_label_reset_votes' => 'Reset votes',

'polls_value_limit_votes_no' => 'No (unlimited votes)',
'polls_value_limit_votes_yes' => 'Yes (one vote per visitor)',

// Polls show view
'polls_show_total'		=> 'Total:',
'polls_show_no_votes'	=> 'No Votes',

// END
'' => ''
);

/* End of File: lang.safeharbor.php */
