<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=edit'); ?>

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
							<li><?php echo form_input(array('name'=>'options[]['.$option->id.']', 'value'=>$option->value, 'class'=>'field')); ?></li>
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
			<tr class="even">
				<td><strong><?php echo lang('polls_label_site_id'); ?></strong></td>
				<td><?php echo form_dropdown('site_id', $sites_listing, $poll->site_id); ?></td>
			</tr>
		</tbody>
	</table>

	<?php echo form_hidden('poll_id', $poll->id); ?>
	<?php echo form_submit(array('name'=>'submit', 'value'=>lang('submit'), 'class'=>'submit')); ?>
	<a style="margin-left:8px;" href="<?php echo $base_url.AMP.'method=reset'; ?>" onclick="if (confirm('<?php echo lang('polls_message_confirm_reset'); ?>')) {var f = document.createElement('form');f.style.display = 'none';this.parentNode.appendChild(f);f.method = 'POST';f.action = this.href;var m = document.createElement('input');m.setAttribute('type', 'hidden');m.setAttribute('name', 'poll');m.setAttribute('value', '<?php echo $poll->id; ?>');f.appendChild(m);var s = document.createElement('input');s.setAttribute('type', 'hidden');s.setAttribute('name', 'XID');s.setAttribute('value', '<?php echo $this->javascript->global_vars['XID']; ?>');f.appendChild(s);f.submit();};return false;"><?php echo lang('polls_label_reset_votes'); ?></a>
<?php echo form_close(); ?>

