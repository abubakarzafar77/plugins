<?php

/*
  Plugin Name: Quiz Plugin
  Plugin URI: http://www.purelogics.net
  Description: Easily create questions for your WordPress website and publish them with a simple shortcode
  Version: 1
  Author: purelogics
  Author URI: http://purelogics.net
  Text Domain: questionare
  Domain Path: /languages/
 */

load_plugin_textdomain('quiz-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages/');

$admin_includes    = array(
    'awesome-surveys-admin'
);
$frontend_includes = array(
    'awesome-surveys-frontend',
);
$includes          = array(
    'awesome-surveys',
    'awesome-surveys-ajax-actions',
);

global $awesome_surveys;
foreach ($includes as $include_file)
{
    include_once( plugin_dir_path(__FILE__) . 'includes/class-' . $include_file . '.php' );
}
if (!isset($awesome_surveys))
{
    $awesome_surveys = new Awesome_Surveys;
}
if (!isset($awesome_surveys_ajax))
{
    $awesome_surveys_ajax = new Awesome_Surveys_Ajax;
}

if (is_admin())
{
    foreach ($admin_includes as $include_file)
    {
        include_once( plugin_dir_path(__FILE__) . 'includes/class-' . $include_file . '.php' );
    }
    new Awesome_Surveys_Admin;
}
else
{
    foreach ($frontend_includes as $include_file)
    {
        include_once( plugin_dir_path(__FILE__) . 'includes/class-' . $include_file . '.php' );
    }
    new Awesome_Surveys_Frontend;
}

if (!defined('WWM_AWESOME_SURVEYS_URL'))
{
    define('WWM_AWESOME_SURVEYS_URL', plugins_url('', __FILE__));
}
if (!defined('WWM_AWESOME_SURVEYS_PATH'))
{
    define('WWM_AWESOME_SURVEYS_PATH', plugin_dir_path(__FILE__));
}

$awesome_surveys_nopriv_ajax_actions = array(
    'answer-survey' => 'process_response',
);
$awesome_surveys_ajax_actions        = array(
    'add-form-element'    => 'add_form_element',
    'options-fields'      => 'echo_options_fields',
    'generate-preview'    => 'generate_preview',
    'wwm-as-get-json'     => 'get_json',
    'parse-elements'      => 'parse_elements',
    'update-post-content' => 'update_post_content',
);

foreach ($awesome_surveys_nopriv_ajax_actions as $action => $function)
{
    add_action('wp_ajax_nopriv_' . $action, array($awesome_surveys_ajax, $function));
    add_action('wp_ajax_' . $action, array($awesome_surveys_ajax, $function));
}
foreach ($awesome_surveys_ajax_actions as $action => $function)
{
    add_action('wp_ajax_' . $action, array($awesome_surveys_ajax, $function));
}

register_activation_hook(__FILE__, 'wwm_as_plugin_activation');

function wwm_as_plugin_activation()
{
    global $awesome_surveys;
    $awesome_surveys->register_post_type();
    flush_rewrite_rules();
}

$filters = array(
    'awesome_surveys_form_preview' => array(10, 1),
);

foreach ($filters as $filter => $args)
{
    add_filter($filter, array($awesome_surveys, $filter), $args[0], $args[1]);
}

function update_post_quiz_desc($post_id, $post, $update = '')
{
    $survey_id = isset($_REQUEST['survey_id']) ? $_REQUEST['survey_id'] : 0;
    if ($survey_id)
    {
        $post_meta = get_post_meta($post_id, 'quiz_details_' . $post_id, false);
        if (!$post_meta)
        {
            $desc = $_REQUEST['quiz_details'];
            add_post_meta($post_id, 'quiz_details_' . $post_id, $desc);
        }
    }
}

add_action('save_post', 'update_post_quiz_desc', 10, 3);