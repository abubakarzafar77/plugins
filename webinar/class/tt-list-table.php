<?php
if(!class_exists('List_Table')){
    require_once( 'list-table.php' );
}
class TT_List_Table extends List_Table {

    /** ************************************************************************
    * Normally we would be querying data from a database and manipulating that
    * for use in your list table. For this example, we're going to simplify it
    * slightly and create a pre-built array. Think of this as the data that might
    * be returned by $wpdb->query()
    *
    * In a real-world scenario, you would make your own custom query inside
    * this class' prepare_items() method.
    *
    * @var array
    **************************************************************************/
    var $users_data = null;

    function set_data($data){
        $this->users_data = $data;
    }


    /** ************************************************************************
    * REQUIRED. Set up a constructor that references the parent constructor. We
    * use the parent reference to set some default configs.
    ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'user',     //singular name of the listed records
            'plural'    => 'users',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }


    /** ************************************************************************
    * Recommended. This method is called when the parent class can't find a method
    * specifically build for a given column. Generally, it's recommended to include
    * one method for each column you want to render, keeping your package class
    * neat and organized. For example, if the class needs to process a column
    * named 'title', it would first see if a method named $this->column_title()
    * exists - if it does, that method will be used. If it doesn't, this one will
    * be used. Generally, you should try to use custom column methods as much as
    * possible.
    *
    * Since we have defined a column_title() method later on, this method doesn't
    * need to concern itself with any column with a name of 'title'. Instead, it
    * needs to handle everything else.
    *
    * For more detailed insight into how columns are handled, take a look at
    * WP_List_Table::single_row_columns()
    *
    * @param array $item A singular item (one full row's worth of data)
    * @param array $column_name The name/slug of the column to be processed
    * @return string Text or HTML to be placed inside the column <td>
        **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'user_login':
                return $this->column_title($item);
            case 'display_name':
            case 'user_email':
            case 'ID':
            return $item[$column_name];
            case 'webinar_user_id':
                return '<a href="/wp-admin/user-edit.php?user_id='.$item[$column_name].'" target="_blank">'.$item[$column_name].'</a>';
            case 'webinar_date_time':
                return date('F j, Y g:i a', strtotime($item[$column_name]));
            case 'job_post_url':
                return '<a href="'.home_url('studiekamerat?page=my_jobs&job_id='.$item['ID']).'" target="_blank">URL</a>';
            case 'webinar_url':
                return '<a href="'.home_url('studiekamerat?page=webinar&id='.$item['ID']).'" target="_blank">URL</a>';
            case 'teacher_url':
                if($item['webinar_teacher'] == 'all') {
                    return 'All teachers';
                }else{
                    return '<a href="' . home_url('studiekamerat?page=teacher&id=' . $item['webinar_teacher']) . '" target="_blank">URL</a>';
                }
            case 'webinar_status':
                return ucfirst($item[$column_name]);
            case 'name':
                return $this->column_title_categories($item);
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
    * Recommended. This is a custom column method and is responsible for what
    * is rendered in any column with a name/slug of 'title'. Every time the class
    * needs to render a column, it first looks for a method named
    * column_{$column_title} - if it exists, that method is run. If it doesn't
    * exist, column_default() is called instead.
    *
    * This example also illustrates how to implement rollover actions. Actions
    * should be an associative array formatted as 'slug'=>'link html' - and you
    * will need to generate the URLs yourself. You could even ensure the links
    *
    *
    * @see WP_List_Table::::single_row_columns()
    * @param array $item A singular item (one full row's worth of data)
    * @return string Text to be placed inside the column <td> (movie title only)
    **************************************************************************/
    function column_title($item){
        $del_url = 'users.php?action=delete&wp_http_referer=/wp-admin/admin.php?page='.$_REQUEST['page'].'&user=' . $item['ID'];
        //Build row actions
        $actions = array(
            //'profile_edit' => sprintf('<a href="?page=%1$s&mode=%2$s&user=%3$s">%4$s</a>', $_REQUEST['page'], 'edit', $item['ID'], 'Edit Profile'),
            'edit'      => sprintf('<a href="user-edit.php?user_id=%1$s&wp_http_referer=/wp-admin/admin.php?page=%2$s">Edit</a>',$item['ID'], $_REQUEST['page']),
            'delete'    => sprintf('<a class="submitdelete" href="?page=%1$s&user=%2$s&action=%3$s">Delete</a>', $_REQUEST['page'], $item['ID'], 'delete'),
            'view'      => sprintf('<a class="view" href="/studiekamerat?page=teacher&id=%1$s" target="_blank">View</a>', $item['ID'])
        );

        //Return the title contents
        return sprintf('%1$s <strong><a href="user-edit.php?user_id=%2$s&wp_http_referer=/wp-admin/admin.php?page=%3$s">%4$s</a></strong><br />%5$s',
            /*$1%s*/ get_avatar( $item['ID'], 32 ),
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $_REQUEST['page'],
            /*$4%s*/ $item['user_login'],
            /*$5%s*/ $this->row_actions($actions)
        );
    }


    function column_title_categories($item){
        $del_url = '?page='.$_REQUEST['page'].'&action=delete&cat=' . $item['ID'];
        //Build row actions
        $actions = array(
            //'profile_edit' => sprintf('<a href="?page=%1$s&mode=%2$s&user=%3$s">%4$s</a>', $_REQUEST['page'], 'edit', $item['ID'], 'Edit Profile'),
            'edit'      => sprintf('<a href="?cat=%1$s&page=%2$s&action=edit">Edit</a>',$item['ID'], $_REQUEST['page']),
            'delete'    => sprintf('<a class="submitdelete" href="%1$s">Delete</a>', wp_nonce_url($del_url, 'deletecategory_nounce', 'deletecategory_nounce')),
        );

        //Return the title contents
        return sprintf('<strong><a href="?cat=%1$s&page=%2$s&action=edit">%3$s</a></strong><br />%4$s',
            /*$1%s*/ $item['ID'],
            /*$2%s*/ $_REQUEST['page'],
            /*$3%s*/ $item['name'],
            /*$4%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
    * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
    * is given special treatment when columns are processed. It ALWAYS needs to
    * have it's own method.
    *
    * @see WP_List_Table::::single_row_columns()
    * @param array $item A singular item (one full row's worth of data)
    * @return string Text to be placed inside the column <td> (movie title only)
    **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
    * REQUIRED! This method dictates the table's columns and titles. This should
    * return an array where the key is the column slug (and class) and the value
    * is the column's title text. If you need a checkbox for bulk actions, refer
    * to the $columns array below.
    *
    * The 'cb' column is treated differently than the rest. If including a checkbox
    * column in your table you must create a column_cb() method. If you don't need
    * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
    *
    * @see WP_List_Table::::single_row_columns()
    * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
    **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'user_login'     => 'Username',
            'display_name'    => 'Name',
            'user_email'  => 'E-Mail'
        );
        return $columns;
    }

    function get_columns_categories(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'name'     => 'Name'
        );
        return $columns;
    }

    function get_columns_webinars(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'ID'     => 'Webinar ID',
            'webinar_date_time'  => 'Date',
            'webinar_user_id'=>'Student ID',
            'job_post_url'=>'Job post url',
            'webinar_url'=>'Webinar url',
            'teacher_url'=>'Teacher url',
            'webinar_status'=>'Status'
        );
        return $columns;
    }


    /** ************************************************************************
    * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
    * you will need to register it here. This should return an array where the
    * key is the column that needs to be sortable, and the value is db column to
    * sort by. Often, the key and value will be the same, but this is not always
    * the case (as the value is a column name from the database, not the list table).
    *
    * This method merely defines which columns should be sortable and makes them
    * clickable - it does not handle the actual sorting. You still need to detect
    * the ORDERBY and ORDER querystring variables within prepare_items() and sort
    * your data accordingly (usually by modifying your query).
    *
    * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
    **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'user_login'     => array('user_login',false),     //true means it's already sorted
            'display_name'    => array('display_name',false),
            'user_email'  => array('user_email',false)
        );
        return $sortable_columns;
    }


    function get_sortable_columns_categories() {
        $sortable_columns = array(
            'name'     => array('name',false),     //true means it's already sorted
        );
        return $sortable_columns;
    }

    function get_sortable_columns_webinars() {
        $sortable_columns = array(
            'ID'            => array('ID', true),
            'webinar_date'     => array('webinar_date',false),
            'webinar_user_id'    => array('webinar_user_id',false),
            'webinar_status'  => array('webinar_status',false)
        );
        return $sortable_columns;
    }


    /** ************************************************************************
    * Optional. If you need to include bulk actions in your list table, this is
    * the place to define them. Bulk actions are an associative array in the format
    * 'slug'=>'Visible Title'
    *
    * If this method returns an empty value, no bulk action will be rendered. If
    * you specify any bulk actions, the bulk actions box will be rendered with
    * the table automatically on display().
    *
    * Also note that list tables are not automatically wrapped in <form> elements,
        * so you will need to create those manually in order for bulk actions to function.
        *
        * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
        **************************************************************************/
        function get_bulk_actions() {
            $actions = array(
                'delete'    => 'Delete'
            );
            return $actions;
        }


        /** ************************************************************************
        * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
        * For this example package, we will handle it in the class to keep things
        * clean and organized.
        *
        * @see $this->prepare_items()
        **************************************************************************/
        function process_bulk_action() {

            //Detect when a bulk action is being triggered...
            if( 'delete'===$this->current_action() ) {
                wp_die('Items deleted (or they would be if we had items to delete)!');
            }

        }


        /** ************************************************************************
        * REQUIRED! This is where you prepare your data for display. This method will
        * usually be used to query the database, sort and filter the data, and generally
        * get it ready to be displayed. At a minimum, we should set $this->items and
        * $this->set_pagination_args(), although the following properties and methods
        * are frequently interacted with here...
        *
        * @global WPDB $wpdb
        * @uses $this->_column_headers
        * @uses $this->items
        * @uses $this->get_columns()
        * @uses $this->get_sortable_columns()
        * @uses $this->get_pagenum()
        * @uses $this->set_pagination_args()
        **************************************************************************/
        function prepare_items() {
            global $wpdb; //This is used only if making any database queries

            /**
            * First, lets decide how many records per page to show
            */
            $per_page = 5;


            /**
            * REQUIRED. Now we need to define our column headers. This includes a complete
            * array of columns to be displayed (slugs & titles), a list of columns
            * to keep hidden, and a list of columns that are sortable. Each of these
            * can be defined in another method (as we've done here) before being
            * used to build the value for our _column_headers property.
            */
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();


            /**
            * REQUIRED. Finally, we build an array to be used by the class for column
            * headers. The $this->_column_headers property takes an array which contains
            * 3 other arrays. One for all columns, one for hidden columns, and one
            * for sortable columns.
            */
            $this->_column_headers = array($columns, $hidden, $sortable);


            /**
            * Optional. You can handle your bulk actions however you see fit. In this
            * case, we'll handle them within our package just to keep things clean.
            */
            $this->process_bulk_action();


            /**
            * Instead of querying a database, we're going to fetch the example data
            * property we created for use in this plugin. This makes this example
            * package slightly different than one you might build on your own. In
            * this example, we'll be using array manipulation to sort and paginate
            * our data. In a real-world implementation, you will probably want to
            * use sort and pagination data to build a custom query instead, as you'll
            * be able to use your precisely-queried data immediately.
            */
            $data = $this->users_data;


            /**
            * This checks for sorting input and sorts the data in our array accordingly.
            *
            * In a real-world situation involving a database, you would probably want
            * to handle sorting by passing the 'orderby' and 'order' values directly
            * to a custom query. The returned data will be pre-sorted, and this array
            * sorting technique would be unnecessary.
            */
            function usort_reorder($a,$b){
                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
                return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
            }
            usort($data, 'usort_reorder');


            /***********************************************************************
            * ---------------------------------------------------------------------
            * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
            *
            * In a real-world situation, this is where you would place your query.
            *
            * For information on making queries in WordPress, see this Codex entry:
            * http://codex.wordpress.org/Class_Reference/wpdb
            *
            * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
            * ---------------------------------------------------------------------
            **********************************************************************/


            /**
            * REQUIRED for pagination. Let's figure out what page the user is currently
            * looking at. We'll need this later, so you should always include it in
            * your own package classes.
            */
            $current_page = $this->get_pagenum();

            /**
            * REQUIRED for pagination. Let's check how many items are in our data array.
            * In real-world use, this would be the total number of items in your database,
            * without filtering. We'll need this later, so you should always include it
            * in your own package classes.
            */
            $total_items = count($data);


            /**
            * The WP_List_Table class does not handle pagination for us, so we need
            * to ensure that the data is trimmed to only the current page. We can use
            * array_slice() to
            */
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



            /**
            * REQUIRED. Now we can add our *sorted* data to the items property, where
            * it can be used by the rest of the class.
            */
            $this->items = $data;


            /**
            * REQUIRED. We also have to register our pagination options & calculations.
            */
            $this->set_pagination_args( array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
            }

        function prepare_items_categories() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns_categories();
        $hidden = array();
        $sortable = $this->get_sortable_columns_categories();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->users_data;


        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder_categories($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder_categories');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


        function prepare_items_webinars() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns_webinars();
        $hidden = array();
        $sortable = $this->get_sortable_columns_webinars();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->users_data;


        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder_webinars($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ID'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder_webinars');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }


        }





        /** ************************ REGISTER THE TEST PAGE ****************************
        *******************************************************************************
        * Now we just need to define an admin page. For this example, we'll add a top-level
        * menu item to the bottom of the admin menus.
        */
        function tt_add_menu_items(){
            add_menu_page('Example Plugin List Table', 'List Table Example', 'activate_plugins', 'tt_list_test', 'tt_render_teachers_list_page');
        }
        //add_action('admin_menu', 'tt_add_menu_items');


        /** *************************** RENDER TEST PAGE ********************************
        *******************************************************************************
        * This function renders the admin page and the example list table. Although it's
        * possible to call prepare_items() and display() from the constructor, there
        * are often times where you may need to include logic here between those steps,
        * so we've instead called those methods explicitly. It keeps things flexible, and
        * it's the way the list tables are used in the WordPress core.
        */
        function tt_render_teachers_list_page($data){
            //Create an instance of our package class...
            $testListTable = new TT_List_Table();
            $testListTable->set_data($data);
            //Fetch, prepare, sort, and filter our data...
            $testListTable->prepare_items();
            ?>
            <div class="wrap">

                <?php if(isset($_GET['updated']) && $_GET['updated'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Teacher updated successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['updated']) && $_GET['updated'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Teacher was not updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['deleted']) && $_GET['deleted'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Teacher deleted successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['deleted']) && $_GET['deleted'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Teacher was not deleted.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['added']) && $_GET['added'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Teacher created successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['added']) && $_GET['added'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Unable to create Teacher.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }?>

                <div id="icon-users" class="icon32"><br/></div>
                <h1>List Teachers <a href="user-new.php?role=teacher&wp_http_referer=/wp-admin/admin.php?page=<?php $_REQUEST['page'];?>" class="page-title-action">Add New</a></h1>

                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="users-filter" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <!-- Now we can render the completed list table -->
                    <?php $testListTable->display() ?>
                </form>

            </div>
<?php
        }
        /** *************************** RENDER TEST PAGE ********************************
         *******************************************************************************
         * This function renders the admin page and the example list table. Although it's
         * possible to call prepare_items() and display() from the constructor, there
         * are often times where you may need to include logic here between those steps,
         * so we've instead called those methods explicitly. It keeps things flexible, and
         * it's the way the list tables are used in the WordPress core.
         */
        function tt_render_teacher_categories_list_page($data){
            global $webinar_config;
            //Create an instance of our package class...
            $testListTable = new TT_List_Table();
            $testListTable->set_data($data);
            //Fetch, prepare, sort, and filter our data...
            $testListTable->prepare_items_categories();
            ?>
            <div class="wrap">

                <?php if(isset($_GET['updated']) && $_GET['updated'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Category updated successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['updated']) && $_GET['updated'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Category was not updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['deleted']) && $_GET['deleted'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Category deleted successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['deleted']) && $_GET['deleted'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Category was not deleted.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['added']) && $_GET['added'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Category created successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['added']) && $_GET['added'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Unable to create category.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }?>

                <div id="icon-users" class="icon32"><br/></div>
                <h1>Teachers Categories List <a href="?page=<?php echo $webinar_config->plugin_teacher_categories_page;?>&action=add" class="page-title-action">Add New</a></h1>

                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="users-filter" method="post">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <!-- Now we can render the completed list table -->
                    <?php $testListTable->display() ?>
                </form>

            </div>
    <?php
        }

        function tt_render_categories_form($data){?>
            <div class="wrap">

                <div id="icon-users" class="icon32"><br/></div>
                <h1>Teachers Categories <?php echo ucfirst($_REQUEST['action'])?></h1>
                <p>Create a brand new teacher category and add them to this site.</p>

                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="createcategory" name="createcategory" method="post" novalidate="novalidate">
                    <?php if(empty($data)){?>
                        <input name="action" type="hidden" value="createcategory" />
                        <?php wp_nonce_field( "createcategory_nounce", "createcategory_nounce" ) ?>
                    <?php }else{?>
                        <input name="action" type="hidden" value="updatecategory" />
                        <input type="hidden" name="ID" value="<?php echo $data['ID'];?>" />
                        <?php wp_nonce_field( "updatecategory_nounce", "updatecategory_nounce" ) ?>
                    <?php }?>

                    <table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="name">
                                Category Name
                                <span class="description">(required)</span>
                            </label>
                        </th>
                        <td>
                            <input name="name" type="text" id="name" value="<?php echo ((!empty($data))?$data['name']:'');?>" class="regular-text" aria-required="true" autocapitalize="none" autocorrect="off" style="width: 25em;">
                        </td>
                    </tr>
                    </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" name="createcategory" id="createcategorysub" class="button button-primary" value="<?php echo ucfirst($_REQUEST['action'])?> Category" />
                    </p>
                </form>
            </div>

<?php
        }

        function tt_render_webinars_list_page($data){
            global $webinar_config;
            //Create an instance of our package class...
            $testListTable = new TT_List_Table();
            $testListTable->set_data($data);
            //Fetch, prepare, sort, and filter our data...
            $testListTable->prepare_items_webinars();
            ?>
            <div class="wrap">

                <?php if(isset($_GET['updated']) && $_GET['updated'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Category updated successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['updated']) && $_GET['updated'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Category was not updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['deleted']) && $_GET['deleted'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Category deleted successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['deleted']) && $_GET['deleted'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Category was not deleted.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['added']) && $_GET['added'] == 'yes'){?>
                    <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Category created successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }else if(isset($_GET['added']) && $_GET['added'] == 'no'){?>
                    <div id="message" class="error notice notice-error is-dismissible below-h2"><p>Unable to create category.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
                <?php }?>

                <div id="icon-users" class="icon32"><br/></div>
                <h1>Teachers Categories List <a href="?page=<?php echo $webinar_config->plugin_teacher_categories_page;?>&action=add" class="page-title-action">Add New</a></h1>

                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="users-filter" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <!-- Now we can render the completed list table -->
                    <?php $testListTable->display() ?>
                </form>

            </div>
            <?php
        }