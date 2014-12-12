
<div id="poll_section_edit">
	<div id="poll-selector">
		<label>Select a Poll or Create a new one:</label>
		<?php echo form_dropdown('poll_selector', $polls_dropdown, $poll_selected); ?>
	</div>

	<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo lang('polls_label_option'); ?></th>
				<th><?php echo lang('polls_label_value'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<td><strong><?php echo lang('polls_label_question'); ?></strong></td>
				<td><?php echo form_input(array('name'=>'question', 'value'=>$poll->question)); ?></td>
			</tr>
			<tr class="odd">
				<td><strong><?php echo lang('polls_label_options'); ?></strong></td>
				<td>
					<ol id="poll-options">
						<?php foreach( $poll_options as $option ) : ?>
							<li>
								<div class="textfield">
									<?php echo form_input(array('name'=>'options[]['.$option->id.']', 'value'=>$option->value, 'class'=>'field')); ?>
									<div class="votes">
										<span class="key"><?php echo lang('poll_inline_votes'); ?></span>
										<span class="val"><?php echo $option->votes; ?></span>
									</div>
									<div class="results">
										<span class="key"><?php echo lang('poll_inline_results'); ?></span>
										<span class="val"><?php echo (empty($option->votes) OR empty($poll->total_votes)) ? '0%' : round(($option->votes/$poll->total_votes)*100).'%'; ?></span>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</td>
			</tr>
			<tr class="even">
				<td><strong><?php echo lang('polls_label_limit_votes'); ?></strong></td>
				<td><?php echo form_dropdown('limit_votes', array('no' => lang('polls_value_limit_votes_no'), 'yes' => lang('polls_value_limit_votes_yes')), $poll->limit_votes); ?></td>
			</tr>
			<tr class="odd">
				<td><strong><?php echo lang('polls_label_add_status'); ?></strong></td>
				<td><?php echo form_dropdown('poll_status', array('open'=>'Open', 'closed'=>'Closed'), $poll->status); ?></td>
			</tr>
		</tbody>
	</table>
</div>

<input type="hidden" name="poll_id" value="<?php echo $poll->id; ?>" />

