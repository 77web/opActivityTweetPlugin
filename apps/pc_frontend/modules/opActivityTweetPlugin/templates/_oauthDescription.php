<?php if('' == $sf_user->getMember()->getConfig('twitter_oauth_token')): ?>
<strong><p><?php echo __('Click send first in order to connect your twitter account.'); ?></p></strong>
<?php endif; ?>