<?php
/**
 * @package Social Login
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="form-group">
    <div class="col-md-10 col-md-offset-2">
      <div class="checkbox">
        <label>
          <input type="checkbox" value="1" name="settings[register]"<?php echo $settings['register'] ? ' checked' : ''; ?>> <?php echo $this->text('Register non-existing users'); ?>
        </label>
        <div class="help-block"><?php echo $this->text('Forcibly register all new users if they were logged in by this module'); ?></div>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" value="1" name="settings[register_login]"<?php echo $settings['register_login'] ? ' checked' : ''; ?>> <?php echo $this->text('Log in registered users immediately'); ?>
        </label>
        <div class="help-block"><?php echo $this->text('Immediately log in new users if they were registered by this module'); ?></div>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox" value="1" name="settings[register_status]"<?php echo $settings['register_status'] ? ' checked' : ''; ?>> <?php echo $this->text('Enable registered users by default'); ?>
        </label>
        <div class="help-block"><?php echo $this->text('Make users active if they were registered by this module'); ?></div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><?php echo $this->text('Credentials'); ?></label>
    <div class="col-md-10">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th><?php echo $this->text('Provider'); ?></th>
              <th><?php echo $this->text('Status'); ?></th>
              <th><?php echo $this->text('Client ID'); ?></th>
              <th><?php echo $this->text('Client secret'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($providers as $id => $provider) { ?>
            <tr>
              <td class="middle"><?php echo $this->e($provider['name']); ?></td>
              <td class="middle">
                <input name="settings[status][<?php echo $this->e($id); ?>]" type="hidden" value="0">
                <input name="settings[status][<?php echo $this->e($id); ?>]" type="checkbox" value="1"<?php echo empty($settings['status'][$id]) ? '' : ' checked'; ?>>
              </td>
              <td><input name="settings[client_id][<?php echo $this->e($id); ?>]" class="form-control" value="<?php echo isset($settings['client_id'][$id]) ? $this->e($settings['client_id'][$id]) : ''; ?>"></td>
              <td><input name="settings[client_secret][<?php echo $this->e($id); ?>]" class="form-control" value="<?php echo isset($settings['client_secret'][$id]) ? $this->e($settings['client_secret'][$id]) : ''; ?>"></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="help-block"><?php echo $this->text('Specify credentials to access service APIs'); ?></div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-10 col-md-offset-2">
      <div class="btn-toolbar">
        <a href="<?php echo $this->url("admin/module/list"); ?>" class="btn btn-default"><?php echo $this->text('Cancel'); ?></a>
        <button class="btn btn-default save" name="save" value="1"><?php echo $this->text('Save'); ?></button>
      </div>
    </div>
  </div>
</form>