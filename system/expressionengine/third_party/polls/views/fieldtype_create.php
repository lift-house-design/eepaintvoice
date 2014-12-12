<div id="poll-selector">
	<label>Select a Poll or Create a new one:</label>
	<?php echo form_dropdown('poll_selector', $polls_dropdown); ?>
</div>

<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th>Option</th>
			<th>Value</th>
		</tr>
	</thead>
	<tbody>
		<tr class="even">
			<td><strong><?php echo lang('polls_label_question'); ?></strong></td>
			<td><?php echo form_input(array('name'=>'question', 'value'=> $this->input->post('question'))); ?></td>
		</tr>
		<tr class="odd">
			<td><strong><?php echo lang('polls_label_options'); ?></strong></td>
			<td>
				<ol id="poll-options">
					<?php $options = $this->input->post('options'); ?>

					<?php if( !empty($options)) : ?>
						<?php foreach($options as $val) : ?>
							<li><span class="option-handle"><img src="<?php echo $this->config->item('theme_folder_url'); ?>/third_party/polls/img/option-handle.jpg" alt="handle" /></span><?php echo form_input(array('name'=>'options[]', 'value'=>$val, 'class'=>'field')); ?>&nbsp;<a href="#" class="remove-option"><?php echo lang('polls_label_remove'); ?></a></li>
						<?php endforeach; ?>
					<?php else : ?>
						<li><span class="option-handle"><img src="<?php echo $this->config->item('theme_folder_url'); ?>/third_party/polls/img/option-handle.jpg" alt="handle" /></span><?php echo form_input(array('name'=>'options[]', 'value'=>'', 'class'=>'field')); ?>&nbsp;<a href="#" class="remove-option"><?php echo lang('polls_label_remove'); ?></a></li>
						<li><span class="option-handle"><img src="<?php echo $this->config->item('theme_folder_url'); ?>/third_party/polls/img/option-handle.jpg" alt="handle" /></span><?php echo form_input(array('name'=>'options[]', 'value'=>'', 'class'=>'field')); ?>&nbsp;<a href="#" class="remove-option"><?php echo lang('polls_label_remove'); ?></a></li>
					<?php endif; ?>
				</ol>
				<a id="add-option" class="button" href="#">+ <?php echo lang('polls_label_add_option'); ?></a>
				<a id="add-option-other" class="button" href="#">+ <?php echo lang('polls_label_add_option_other'); ?></a>
			</td>
		</tr>
		<tr class="even">
			<td><strong><?php echo lang('polls_label_limit_votes'); ?></strong></td>
			<td><?php echo form_dropdown('limit_votes', array('no' => lang('polls_value_limit_votes_no'), 'yes' => lang('polls_value_limit_votes_yes'))); ?></td>
		</tr>
		<tr class="odd">
			<td><strong><?php echo lang('polls_label_add_status'); ?></strong></td>
			<td><?php echo form_dropdown('poll_status', array('open'=>'Open', 'closed'=>'Closed')); ?></td>
		</tr>
	</tbody>
</table>
