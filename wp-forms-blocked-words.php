<?php
/*
Plugin Name: Blocked Words Filter for WPForms
Plugin URI:  https://www.brianthart.com/
Description: A plugin to filter blocked words in WPForms text areas.
Version:     1.21
Author:      Brian Hart
Author URI:  https://www.brianthart.com/
License:     GPL2
*/

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// Register the settings and add settings page
function bwf_register_settings()
{
  register_setting('bwf_settings_group', 'bwf_blocked_words');
}

add_action('admin_init', 'bwf_register_settings');

// Add settings page to the menu
function bwf_add_settings_page()
{
  add_options_page(
    'Blocked Words Filter Settings',
    'Blocked Words Filter',
    'manage_options',
    'bwf-settings',
    'bwf_render_settings_page'
  );
}

add_action('admin_menu', 'bwf_add_settings_page');

// Render the settings page
function bwf_render_settings_page()
{
?>
  <div class="wrap">
    <h1>Blocked Words Filter Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('bwf_settings_group'); ?>
      <?php do_settings_sections('bwf_settings_group'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Blocked Words</th>
          <td>
            <textarea name="bwf_blocked_words" rows="10" cols="50" class="large-text"><?php echo esc_textarea(get_option('bwf_blocked_words')); ?></textarea>
            <p class="description">Enter blocked words separated by commas.</p>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}

// Check the paragraph text field for blocked words
function wpf_dev_blocked_words_filter_paragraph($field_id, $field_submit, $form_data)
{
  // Get blocked words from settings
  $blocked_words_option = get_option('bwf_blocked_words');
  $blocked_words = array_map('trim', explode(',', $blocked_words_option));

  foreach ($blocked_words as $word) {
    if (stripos($field_submit, $word) !== FALSE) {
      wpforms()->process->errors[$form_data['id']][$field_id] = esc_html__('The message appears to be spam or it contains blocked content. Please check your message and try again.', 'wpforms');
      return;
    }
  }
}

add_action('wpforms_process_validate_textarea', 'wpf_dev_blocked_words_filter_paragraph', 10, 3);
