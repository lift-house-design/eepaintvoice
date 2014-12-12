<h3>Poll: <?php echo $poll->question; ?></h3>

<h4>Results</h4>
<table id="poll_options" class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="polls_options_id"><?php echo lang('polls_options_table_id'); ?></th>
			<th class="polls_options_value"><?php echo lang('polls_options_table_value'); ?></th>
			<th class="polls_options_votes"><?php echo lang('polls_options_table_votes'); ?></th>
			<th class="polls_options_ratio"><?php echo lang('polls_options_table_ratio'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td class="polls_options_id"></td>
			<td class="polls_options_value"></td>
			<td class="polls_options_votes"><strong><?php echo lang('polls_show_total'); ?> <?php echo $poll->total_votes; ?></strong></td>
			<td class="polls_options_ratio"></td>
		</tr>
	</tfoot>
	<tbody>
		<?php if( !empty($poll_options) ) : ?>
			<?php foreach( $poll_options as $option ) : ?>
				<tr class="<?php echo alternator('even','odd'); ?>">
					<td class="polls_options_id"><?php echo $option->id; ?></td>
					<td class="polls_options_value"><?php echo $option->value; ?></td>
					<td class="polls_options_votes"><?php echo $option->votes; ?></td>
					<td class="polls_options_ratio"><?php echo (empty($option->votes) OR empty($poll->total_votes)) ? '0%' : round(($option->votes/$poll->total_votes)*100).'%'; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<h4>Votes</h4>
<table id="poll_votes" class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="polls_votes_id"><?php echo lang('polls_votes_table_id'); ?></th>
			<th class="polls_votes_answer"><?php echo lang('polls_votes_table_answer'); ?></th>
			<th class="polls_votes_data"><?php echo lang('polls_votes_table_data'); ?></th>
			<th class="polls_votes_member"><?php echo lang('polls_votes_table_member'); ?></th>
			<th class="polls_votes_date"><?php echo lang('polls_votes_table_date'); ?></th>
			<th class="polls_votes_ip"><?php echo lang('polls_votes_table_ip'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if( !empty($poll_votes) ) : ?>
			<?php foreach( $poll_votes as $vote ) : ?>
				<tr class="<?php echo alternator('even','odd'); ?>">
					<td class="polls_votes_id"><?php echo $vote->id; ?></td>
					<td class="polls_votes_answer"><?php echo $vote->option_value; ?></td>
					<td class="polls_votes_data"><?php echo $vote->data; ?></td>
					<td class="polls_votes_member"><?php echo empty($vote->username) ? 'Guest' : $vote->username; ?></td>
					<td class="polls_votes_date"><?php echo date('Y-m-d g:iA', $vote->creation_date); ?></td>
					<td class="polls_votes_ip"><?php echo $vote->ip_address; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr><td colspan="5"><?php echo lang('polls_show_no_votes'); ?></td></tr>
		<?php endif; ?>
	</tbody>
</table>
<?php echo $pagination; ?>
