<?php
/*
 * Copyright 2014 Osclass
 * Copyright 2023 Osclass by OsclassPoint.com
 *
 * Osclass maintained & developed by OsclassPoint.com
 * You may not use this file except in compliance with the License.
 * You may download copy of Osclass at
 *
 *     https://osclass-classifieds.com/download
 *
 * Do not edit or add to this file if you wish to upgrade Osclass to newer
 * versions in the future. Software is distributed on an "AS IS" basis, without
 * warranties or conditions of any kind, either express or implied. Do not remove
 * this NOTICE section as it contains license information and copyrights.
 */


// meta tag robots
osc_add_hook('header','sigma_nofollow_construct');

osc_enqueue_script('jquery-validate');
sigma_add_body_class('contact');
osc_current_web_theme_path('header.php');
?>

<div class="form-container form-horizontal form-container-box">
  <div class="header">
    <h1><?php _e('Send to a friend', 'sigma'); ?></h1>
  </div>
  <div class="resp-wrapper">
    <?php if(osc_item_send_friend_form_disabled()) { ?>
      <p class="problem expired">
        <?php _e("Send to friend form is disabled.", 'sigma'); ?>
      </p>
    <?php } else { ?>
      <ul id="error_list"></ul>
      <form name="sendfriend" action="<?php echo osc_base_url(true); ?>" method="post" >
        <input type="hidden" name="action" value="send_friend_post" />
        <input type="hidden" name="page" value="item" />
        <input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" />
        <?php if(osc_is_web_user_logged_in()) { ?>
                <input type="hidden" name="yourName" value="<?php echo osc_esc_html( osc_logged_user_name() ); ?>" />
                <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
        <?php } else { ?>
        <div class="control-group">
          <label class="control-label" for="yourName"><?php _e("Your name",'sigma'); ?></label>
          <div class="controls ">
            <?php SendFriendForm::your_name(); ?>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="yourEmail"><?php _e("Your e-mail",'sigma'); ?></label>
          <div class="controls ">
            <?php SendFriendForm::your_email(); ?>
          </div>
        </div>
        <?php } ?>
        <div class="control-group">
          <label class="control-label" for="friendName"><?php _e("Your friend's name",'sigma'); ?></label>
          <div class="controls">
            <?php SendFriendForm::friend_name(); ?>
          </div>
        </div>
        <div class="control-group">
          <label for="friendEmail"><?php _e("Your friend's e-mail address", 'sigma'); ?></label> </label>
          <div class="controls">
            <?php SendFriendForm::friend_email(); ?>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="subject">
            <?php _e('Subject (optional)', 'sigma'); ?>
          </label>
          <div class="controls">
            <?php ContactForm::the_subject(); ?>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="message">
            <?php _e('Message', 'sigma'); ?></label>
          <div class="controls textarea">
            <?php SendFriendForm::your_message(); ?>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <?php osc_run_hook('contact_form'); ?>
            <?php osc_show_recaptcha(); ?>
            <button type="submit" class="btn btn-primary"><?php _e("Send", 'sigma');?></button>
            <?php osc_run_hook('admin_contact_form'); ?>
          </div>
        </div>
      </form>
      <?php SendFriendForm::js_validation(); ?>
    <?php } ?>
  </div>
</div>
<?php osc_current_web_theme_path('footer.php'); ?>