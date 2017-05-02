<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
/**
* Description of Webinar_admin
*
* @author
*/

class Webinar_admin{
    protected $pluginPath;
    protected $pluginUrl;
    protected $config;
    protected $model;
    protected $template_url;

    function __construct() {
        // Set Plugin Path
        $this->pluginPath = dirname(__FILE__).'/../';
        $this->config = json_decode(file_get_contents($this->pluginPath.'/config.json'));
        $this->model = new Model();
        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/'.$this->config->plugin_name.'/';
        add_action( 'webinar_teacher_listing_before_loop', array( $this, 'add_search' ) );
        $this->load_admin_script();
        $this->load_admin_style();
    }

    function create_admin_menu() {
        //this is the main item for the menu
        add_menu_page(
            'Mattevideo Webinars', //page title
            'Mattevideo Webinars', //menu title
            'manage_options', //capabilities
            $this->config->plugin_webinars_page, //menu slug
            array($this, 'webinars_list'), //function
            $this->pluginUrl.$this->config->plugin_images_folder.'/webinar-br.png'
        );
        add_submenu_page($this->config->plugin_webinars_page, //parent slug
            'Webinar Teachers', //page title
            'Webinar Teachers', //menu title
            'manage_options', //capability
            $this->config->plugin_teachers_page, //menu slug
            array($this, 'webinar_teachers')); //function

        add_submenu_page($this->config->plugin_webinars_page, //parent slug
            'Webinar Teacher Categories', //page title
            'Webinar Teacher Categories', //menu title
            'manage_options', //capability
            $this->config->plugin_teacher_categories_page, //menu slug
            array($this, 'webinar_teacher_categories')); //function

        add_submenu_page($this->config->plugin_webinars_page, //parent slug
            'Email Templates', //page title
            'Email Templates', //menu title
            'manage_options', //capability
            $this->config->plugin_email_template_page, //menu slug
            array($this, 'show_email_tamplates')); //function
    }

    public function webinars_list(){
        tt_render_webinars_list_page($this->model->get_webinars_list());
    }

    /**
     * Get transient version
     *
     * When using transients with unpredictable names, e.g. those containing an md5
     * hash in the name, we need a way to invalidate them all at once.
     *
     * borrowed from WooCommerce
     * Raised in issue https://github.com/woothemes/woocommerce/issues/5777
     * Adapted from ideas in http://tollmanz.com/invalidation-schemes/
     *
     * @param  string  $group   Name for the group of transients we need to invalidate
     * @param  boolean $refresh true to force a new version
     * @since  1.7.0
     * @return string transient version based on time(), 10 digits
     */
    public function get_transient_version( $group, $refresh = false ) {
        $transient_name  = $group . '-transient-version';
        $transient_value = get_transient( $transient_name );

        if ( false === $transient_value || true === $refresh ) {
            $transient_value = time();
            set_transient( $transient_name, $transient_value );
        }
        return $transient_value;
    }

    /**
     * Add the search template
     *
     * @access public
     * @since 1.0
     * @return null
     */
    public function add_search() {
        $this->webinar_get_template_part( 'search', 'teacher' );
    }

    public function webinar_teacher_categories(){
        global $wpdb;
        if(!isset($_REQUEST['action'])) {
            $categories_table = str_replace('[WPDBPREFIX]', $wpdb->prefix, $this->config->plugin_teacher_categories_table);
            $GLOBALS['webinar_teacher_categories'] = $webinar_teacher_categories = $wpdb->get_results("SELECT * FROM " . $categories_table);
            $data = array();
            foreach ($webinar_teacher_categories as $category) {
                $data[] = (array)$category;
            }
            tt_render_teacher_categories_list_page($data);
        }else{
            if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'createcategory') {
                $saved = $this->model->save_category($_POST);
                if ($saved) {
                    wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&added=yes');
                } else {
                    wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&added=no');
                }
            }else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'updatecategory'){
                $updated = $this->model->update_category($_POST);
                if ($updated) {
                    wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&updated=yes');
                } else {
                    wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&updated=no');
                }
            }else if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'add' OR $_REQUEST['action'] == 'edit')){
                $data = array();
                if($_REQUEST['action'] == 'edit'){
                    $data = $this->model->get_category_date_by_id($_REQUEST['cat']);
                }
                tt_render_categories_form($data);
            }else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
                if(isset($_REQUEST['user']) && is_array($_REQUEST['user'])) {
                    $deleted = $this->model->delete_category_by_ids($_REQUEST['user']);
                }else {
                    $deleted = $this->model->delete_category_by_id($_REQUEST['cat']);
                }
                if ($deleted) {
                    wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&deleted=yes');
                } else {
                    wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&deleted=no');
                }
            }else if(isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'delete'){
                if(isset($_REQUEST['user']) && is_array($_REQUEST['user'])) {
                    $deleted = $this->model->delete_category_by_ids($_REQUEST['user']);
                    if ($deleted) {
                        wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&deleted=yes');
                    } else {
                        wp_redirect('?page=' . $this->config->plugin_teacher_categories_page . '&deleted=no');
                    }
                }
            }
        }
    }

    public function webinar_teachers(){
        if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') OR (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'delete')){
            if(isset($_REQUEST['user']) && is_array($_REQUEST['user'])) {
                foreach($_REQUEST['user'] as $id) {
                    $deleted = $this->model->delete_user_by_id($id);
                }
            }else{
                $deleted = $this->model->delete_user_by_id($_REQUEST['user']);
            }
            if ($deleted) {
                wp_redirect('?page=' . $this->config->plugin_teachers_page . '&deleted=yes');
            } else {
                wp_redirect('?page=' . $this->config->plugin_teachers_page . '&deleted=no');
            }
        }
        global $post, $webinar_users, $user;

        extract( array(
            'query_id' => 'webinar_teacher_listing',
            'role' => 'Teacher',
            'include' => '',
            'exclude' => '',
            'blog_id' => '',
            'number' => get_option( 'posts_per_page', 10 ),
            'order' => 'ASC',
            'orderby' => 'login',
            'meta_key' => '',
            'meta_value' => '',
            'meta_compare' => '=',
            'meta_type' => 'CHAR',
            'count_total' => true,
        ));

        $number = intval( $number );

        // We're outputting a lot of HTML, and the easiest way
        // to do it is with output buffering from PHP.
        ob_start();

        // Get the Search Term
        $search = ( isset( $_GET['as'] ) ) ? sanitize_text_field( $_GET['as'] ) : false ;

        // Get Query Var for pagination. This already exists in WordPress
        $page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' )  : 1;

        // Calculate the offset (i.e. how many users we should skip)
        $offset = ( $page - 1 ) * $number;

        // args
        $args = array(
            'query_id' => $query_id,
            'offset' => $offset,
            'number' => $number,
            'orderby' => $orderby,
            'order' => $order,
            'count_total' => $count_total,
            'role' => $role
        );
        $GLOBALS['webinar_users'] = $webinar_users = new WP_User_Query( $args );
        $data = array();
        foreach($webinar_users->get_results() as $user){
            $data[] = (array)$user->data;
        }
        tt_render_teachers_list_page($data);
    }

    /**
     * Get the template url
     * @access public
     * @since 1.3
     * @return string
     */
    function template_url() {
        if ( $this->template_url ) return $this->template_url;

        return $this->template_url = trailingslashit( apply_filters( 'webinar_template_url', 'webinar-teacher-listing' ) );
    }

    /**
     * Get template part
     *
     * @access public
     * @since 1.0
     * @param mixed $slug
     * @param string $name (default: '')
     * @return null
     */
    function webinar_get_template_part( $slug, $name = '' ) {
        $template = '';

        // Look in yourtheme/slug-name.php and yourtheme/simple-user-listing/slug-name.php
        if ( $name )
            $template = locate_template( array ( "{$slug}-{$name}.php", "{$this->template_url()}{$slug}-{$name}.php" ) );
        if ( !$template && $name && file_exists( $this->pluginPath . "/templates/{$slug}-{$name}.php" ) )
            $template = $this->pluginPath . "/templates/{$slug}-{$name}.php";

        // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/simple_user_listing/slug.php
        if ( !$template )
            $template = locate_template( array ( "{$slug}.php", "{$this->template_url()}{$slug}.php" ) );

        if ( $template )
            load_template( $template, false );

    }

    public function show_email_tamplates(){

        if(isset($_POST['update']) && isset($_GET['id'])){
            $emails = json_decode(file_get_contents( $this->pluginPath.'/emails.json' ), true);
            $emails2 = $emails;
            $new_template_text = $_POST['template_text'];
            $new_template_subject = $_REQUEST['template_subject'];
            $emails2['email_templates'][$_GET['id']]['body'] = $new_template_text;
            $emails2['email_templates'][$_GET['id']]['subject'] = $new_template_subject;
            file_put_contents( $this->pluginPath.'emails.json', json_encode($emails2));
            wp_redirect('admin.php?page='.$this->config->plugin_email_template_page.'&updated=yes');
            exit;
        }elseif(isset($_GET['id'])){
            $email = json_decode(file_get_contents( $this->pluginPath.'emails.json' ), true);
            $email_template_to_edit = stripslashes($email['email_templates'][$_GET['id']]['body']);
            $email_template_subject_to_edit = stripslashes($email['email_templates'][$_GET['id']]['subject']);
            $template_type = $_GET['id'];
            include $this->pluginPath."/".$this->config->plugin_views_folder."/admin/email_template.php";
        }else{
            $email = json_decode(file_get_contents( $this->pluginPath.'emails.json' ), true);
            include $this->pluginPath."/".$this->config->plugin_views_folder."/admin/email_template.php";
        }
    }

    function load_admin_script(){
        wp_enqueue_script( 'webinar-admin-script', $this->pluginUrl.$this->config->plugin_js_folder.'/admin_page.js', array(), '1.0.0', true );
    }
    function load_admin_style(){
        wp_enqueue_style( 'webinar-admin-style', $this->pluginUrl.$this->config->plugin_css_folder.'/admin_page.css');
    }
}