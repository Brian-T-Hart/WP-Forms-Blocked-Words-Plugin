/*
 * Check the paragraph text field for blocked words.
 *
 * @link https://wpforms.com/developers/wpforms_process_validate_textarea/
 *
 * @param int     $field_id        Field ID.
 * @param array   $field_submit    Unsanitized field value submitted for the field.
 * @param array   $form_data       Form data and settings.
*/
function wpf_dev_blocked_words_filter_paragraph($field_id, $field_submit, $form_data)
{
  //Create your list of profanity words separated by commas
  $blocked_words = array(
    'seo',
    'SEO',
    'domain authority',
    'website authority',
    'monkeydigital',
    '.ru'
  );

  foreach ($blocked_words as $word) {
    if (strpos($field_submit, $word) !== FALSE) {
      wpforms()->process->errors[$form_data['id']][$field_id] = esc_html__('The message appears to be spam. Please check for spammy content and try again.', 'wpforms');
      return;
    }
  }
}

add_action('wpforms_process_validate_textarea', 'wpf_dev_blocked_words_filter_paragraph', 10, 3);
