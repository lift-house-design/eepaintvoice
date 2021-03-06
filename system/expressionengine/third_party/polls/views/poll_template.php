<style type="text/css">
.poll { padding:15px; background:#fff; border:1px solid #aaa; }
.poll h1 { margin:0px; padding-bottom:15px; font:16px Georgia, serif; color:#000; }
.poll .error { margin-bottom:15px; padding:10px; background:#fbe3e4; color:#8a1f11; border:2px solid #fbc2c4; }
.poll form { margin:0px; }
.poll ul.answers { margin:0px; padding:0px 0px 5px; list-style-type:none; }
.poll ul.answers li.option { display:block; float:none; margin:0px; padding-bottom:10px; width:auto; overflow:hidden; zoom:1.0; }

.poll ul.answers li.option .radio { float:left; padding:3px; }
.poll ul.answers li.option .radio input { display:block; }
.poll ul.answers li.option label { float:left; font:12px Arial, Helvetica, sans-serif; line-height:25px; }
.poll ul.answers li.option .textfield { clear:both; padding-left:27px; }

.poll ul.answers li.option .option-value { float:left; padding-bottom:5px; font:14px Arial, Helvetica, sans-serif; }
.poll ul.answers li.option .option-detail { float:right; padding-bottom:5px; width:150px; font:12px Arial, Helvetica, sans-serif; text-align:right; }
.poll ul.answers li.option .option-detail .vote-count { display:inline; padding-right:10px; }
.poll ul.answers li.option .option-detail .vote-ratio { display:inline; font-size:14px; font-weight:bold; }
.poll ul.answers li.option .option-bar { clear:both; padding:5px 0px 10px; border-top:1px solid #aaa; }
.poll ul.answers li.option .option-bar .option-bar-value { height:20px; background:<?php echo $variables['bar_color']; ?>; }

.poll .buttons { padding-top:10px; border-top:1px solid #aaa; overflow:hidden; zoom:1.0; }
.poll .buttons .cast-vote { float:left; }
.poll .buttons .cast-vote input { display:block; cursor:pointer; }
.poll .buttons .view-results { float:right; }
.poll .buttons .view-results input { display:block; cursor:pointer; border:none; background:none; }

.poll .poll-detail { text-align:right; }
.poll .poll-detail .total-votes {}
.poll .poll-detail .view-options { float:left; }
.poll .poll-detail .view-options input { display:block; cursor:pointer; border:none; background:none; }
.poll .poll-detail p { float:left; margin:0; }
</style>

<div id="poll-<?php echo $variables['poll_id']; ?>" class="poll">
	<?php if( $variables['poll_is_open'] ) : ?>

		<h1><?php echo $variables['poll_question']; ?></h1>

		<?php echo $variables['poll_form_errors']; ?>

		<?php echo $this->EE->functions->form_declaration(array('action' => $variables['action_url']));?>
			<ul class="answers">
				<?php foreach( $variables['poll_options'] as $option ) : ?>
					<li class="option">
						<?php if( $variables['poll_show_vote'] ) : ?>
							<div class="radio"><?php echo $option['option_input']; ?></div>
							<?php echo $option['option_label_html']; ?>
							<?php if( $option['option_is_other'] ) : ?>
								<div class="textfield"><?php echo $option['option_input_other']; ?></div>
							<?php endif; ?>
						<?php elseif( $variables['poll_show_results'] ) : ?>
							<div class="option-value"><?php echo $option['option_value']; ?></div>
							<div class="option-detail">
								<div class="vote-count"><?php echo $option['option_votes']; ?> votes</div>
								<div class="vote-ratio"><?php echo $option['option_ratio']; ?>%</div>
							</div>
							<div class="option-bar"><div class="option-bar-value" style="width:<?php echo $option['option_ratio']; ?>%;"></div></div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if( $variables['poll_show_vote'] ) : ?>
				<div class="buttons">
					<div class="cast-vote"><?php echo $variables['poll_form_submit']; ?></div>
					<div class="view-results"><?php echo $variables['poll_view_results']; ?></div>
				</div>
			<?php else : ?>
				<div class="poll-detail">
					<?php if( $variables['poll_limit_votes'] AND $variables['has_voted'] ) : ?>
						<p>Thank you for voting!</p>
					<?php else : ?>
						<div class="view-options"><?php echo $variables['poll_view_options']; ?></div>
					<?php endif; ?>
					<div class="total-votes"><?php echo $variables['poll_total_votes']; ?> Total Votes</div>
				</div>
			<?php endif; ?>

			<input type="hidden" name="poll_id" value="<?php echo $variables['poll_id']; ?>" />
			<input type="hidden" name="return_url" value="<?php echo $variables['return_url']; ?>" />
		</form>

	<?php endif; ?>
</div>
