<p><strong><?php echo lang('polls_delete_poll'); ?></strong></p>
<p><?php echo $poll->question; ?></p>
<p class="notice"><?php echo lang('polls_delete_poll_notice'); ?><br/><br/></p>
<p><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=polls'.AMP.'method=delete'.AMP.'poll='.$poll->id.AMP.'confirm=1'; ?>" class="submit"><?php echo lang('polls_delete_poll_submit'); ?></a></p>
