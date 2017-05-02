<?php
if (!class_exists('List_Table'))
{
    require_once( 'list-table.php' );
}

class Exercise_List_Table extends List_Table
{

    /**     * ***********************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     *
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     *
     * @var array
     * ************************************************************************ */
    var $users_data = null;

    function set_data($data)
    {
        $this->users_data = $data;
    }

    /**     * ***********************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     * ************************************************************************* */
    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'exercise', //singular name of the listed records
            'plural'   => 'exercises', //plural name of the listed records
            'ajax'     => false        //does this table support ajax?
        ));
    }

    /**     * ***********************************************************************
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
     * ************************************************************************ */
    function column_default($item, $column_name)
    {
        switch ($column_name)
        {
            case 'exercise_name':
                return $this->column_name($item, $column_name);
            case 'sub_chapter_id':
                return get_cat_name($item[$column_name]);
            case 'id':
                return $this->column_id($item, $column_name);
            case 'created_at':
                return date('m/d/Y', $item[$column_name]);
            case 'correct_alt':
            case 'answered_alt':
            case 'answer_alt':
                return str_replace('alt', '', $item[$column_name]);
            case 'exercise_name_detail':
            case 'tools':
            case 'exercise_type':
            case 'user_id':
            case 'exercise_id':
                return $item[$column_name];
            case 'percent_corr':
                return round($item[$column_name]).'%';
            case 'duration':
                return str_replace('|', '-', str_replace('-', ' ', $item[$column_name]));
            case 'corr_alternative':
                return str_replace('alt', '', $item[$column_name]);
            case 'duplicate':
                return $this->column_duplicate($item, $column_name);
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**     * ***********************************************************************
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
     * ************************************************************************ */
    function column_name($item, $column_name)
    {
        //Return the title contents
        return sprintf('<strong><a href="admin.php?page=mattevideo_exercise&id=' . $item['id'] . '&detail=true&orderby=created_at&order=desc">%1$s</a></strong>', $item[$column_name]
        );
    }


    function column_id($item, $column_name)
    {
        //Return the title contents
        return '<strong><a href="admin.php?page=create_exercise&id=' . $item['id'] . '">'.$item['id'].'</a></strong>';
    }

    function column_duplicate($item, $column_name)
    {
        //Return the title contents
        return sprintf('<a href="admin.php?page=mattevideo_exercise&id=' . $item['id'] . '&dup=true">%1$s</a>', 'make dup.'
        );
    }

    /**     * ***********************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     * ************************************************************************ */
    function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /* $1%s */ $this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
                /* $2%s */ $item['id']                //The value of the checkbox should be the record's id
        );
    }

    /**     * ***********************************************************************
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
     * ************************************************************************ */
    function get_columns()
    {
        $columns = array(
            'cb'               => '<input type="checkbox" />', //Render a checkbox instead of text
            'id'               => 'Exercise Id',
            'exercise_name'    => 'Exercise Name',
            'sub_chapter_id'   => 'Sub-Chapter',
            'corr_alternative' => 'Correct Alt',
            'duration'         => 'Duration',
            'tools'            => 'Tools',
            'exercise_type'    => 'Exercise Type',
            'duplicate'        => 'Duplicate',
            'percent_corr'     => '% Corr'
        );
        return $columns;
    }

    function get_detail_columns()
    {
        $columns = array(
            'created_at'            => 'Date',
            'user_id'               => 'User id',
            'exercise_id'           => 'Exercise id',
            'exercise_name_detail'  => 'Exercise name',
            'correct_alt'           => 'Correct alt',
            'answered_alt'          => 'Answered alt',
        );
        return $columns;
    }

    /**     * ***********************************************************************
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
     * ************************************************************************ */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'id'               => array('id', true), //true means it's already sorted
            'exercise_name'    => array('exercise_name', false),
            'corr_alternative' => array('corr_alternative', false),
            'duration'         => array('duration', false),
            'exercise_type'    => array('exercise_type', false),
            'sub_chapter_id'   => array('sub_chapter_id', false),
            'percent_corr'     => array('percent_corr', false),
            'tools'            => array('tools', false)
        );
        return $sortable_columns;
    }

    function get_sortable_detail_columns()
    {
        $sortable_columns = array(
            'created_at'            => array('created_at', false), //true means it's already sorted
            'user_id'               => array('user_id', false),
            'exercise_id'           => array('exercise_id', false),
            'exercise_name_detail'  => array('exercise_name_detail', false),
        );
        return $sortable_columns;
    }

    /**     * ***********************************************************************
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
     * ************************************************************************ */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function get_detail_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**     * ***********************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     * ************************************************************************ */
    function process_bulk_action()
    {
        //echo $this->current_action();exit;
        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action())
        {
            if(count($_REQUEST['exercise']) > 0) {
                deleteRecord('exercise');
            } else {
                wp_die('Items must be selected to perform delete action! <a href="/wp-admin/admin.php?page=mattevideo_exercise">Go back</a>');
            }
            //wp_die('Items deleted (or they would be if we had items to delete)!');
        } /*else if($this->current_action()){
            wp_die('Items must be selected to perform any bulk action! <a href="/wp-admin/admin.php?page=mattevideo_exercise">Go back</a>');
        }*/
    }

    /**     * ***********************************************************************
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
     * ************************************************************************ */
    function prepare_items()
    {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 10;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_columns();
        $hidden   = array();
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
        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
            $order   = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result  = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
        }

        usort($data, 'usort_reorder');


        /*         * *********************************************************************
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
         * ******************************************************************** */


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
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }


    function prepare_detail_items()
    {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 15;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_detail_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_detail_columns();


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
        //$this->process_bulk_action();


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
        function usort_reorder1($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'date'; //If no sort, default to title
            $order   = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result  = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
        }


        usort($data, 'usort_reorder1');


        /*         * *********************************************************************
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
         * ******************************************************************** */


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
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }


}

/** * *********************** REGISTER THE TEST PAGE ****************************
 * ******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
function _tt_add_menu_items()
{
    add_menu_page('Example Plugin List Table', 'List Table Example', 'activate_plugins', 'tt_list_test', 'tt_render_teachers_list_page');
}

//add_action('admin_menu', 'tt_add_menu_items');

/** * ************************** RENDER TEST PAGE ********************************
 * ******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function exercise_render_list_page($data)
{
    //Create an instance of our package class...
    $testListTable = new Exercise_List_Table();
    $testListTable->set_data($data['exercise']);
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    ?>
    <div class="wrap exercise">

        <?php
        if (isset($_GET['dup']))
        {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Exercise duplicated! <a href="' . admin_url('admin.php?page=create_exercise&id='.$_GET['dup']) . '" target="_blank">Edit</a>', 'success-messages'); ?></p>
            </div>
            <?php
        }
        else if (isset($_GET['dup']) && $_GET['dup'] == 'error')
        {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('Unable to duplicate exercise!', 'error-messages'); ?></p>
            </div>
        <?php
        }else if(isset($_GET['m']) && $_GET['m'] == 'deleted') { ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Exercise(s) deleted!', 'success-messages'); ?></p>
            </div>
            <?php
        }?>

        <div id="icon-users" class="icon32"><br/></div>
        <h1>Exercises: posts <a href="admin.php?page=create_exercise" class="page-title-action">Add Exercise</a>&nbsp;&nbsp;<a href="<?php echo admin_url( 'admin-post.php?action=exercises.csv' );?>" class="page-title-action">Export to csv</a></h1>
        Course: <?php
        $courses = array_reverse($data['listing_count']['course']);
        foreach ($courses as $course) {
            $query_string = (isset($_REQUEST['exercise_type'])?'&exercise_type='.$_REQUEST['exercise_type']:'');
            ?>
            <a class="filters<?php echo(((!isset($_REQUEST['course']) && $course['name'] == 'All') || (isset($_REQUEST['course']) && $_REQUEST['course'] == $course['name']))?' selected':'');?>" href="admin.php?page=mattevideo_exercise&course=<?php echo $course['name'],$query_string;?>"><?php echo strtoupper($course['name']) . '(' . $course['count'] . ')' ?></a>&nbsp;&nbsp;&nbsp;
            <?php
        }
        $query_string = (isset($_REQUEST['course'])?'&course='.$_REQUEST['course']:'');
        ?>
        <br/>
        Exercise type: <a class="filters<?php echo(!isset($_REQUEST['exercise_type']) || (isset($_REQUEST['exercise_type']) && $_REQUEST['exercise_type'] == 'All')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&exercise_type=All<?php echo $query_string;?>">All(<?php echo $data['listing_count']['tool_type_all']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <a class="filters<?php echo((isset($_REQUEST['exercise_type']) && $_REQUEST['exercise_type'] == 'wtools_calc')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&exercise_type=wtools_calc<?php echo $query_string;?>">wtools. calc(<?php echo $data['listing_count']['tool_no_cal']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <a class="filters<?php echo((isset($_REQUEST['exercise_type']) && $_REQUEST['exercise_type'] == 'wtools_text')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&exercise_type=wtools_text<?php echo $query_string;?>">wtools. text(<?php echo $data['listing_count']['tool_no_text']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <a class="filters<?php echo((isset($_REQUEST['exercise_type']) && $_REQUEST['exercise_type'] == 'tools_calc')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&exercise_type=tools_calc<?php echo $query_string;?>">tools. calc(<?php echo $data['listing_count']['tool_yes_cal']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <a class="filters<?php echo((isset($_REQUEST['exercise_type']) && $_REQUEST['exercise_type'] == 'tools_text')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&exercise_type=tools_text<?php echo $query_string;?>">tools. text(<?php echo $data['listing_count']['tool_yes_text']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <br />
        <?php $query_string .= (isset($_REQUEST['exercise_type'])?'&exercise_type='.$_REQUEST['exercise_type']:'');?>
        Year: <a class="filters<?php echo(!isset($_REQUEST['year']) || (isset($_REQUEST['year']) && $_REQUEST['year'] == 'All')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&year=All<?php echo $query_string;?>">All(<?php echo $data['listing_count']['year']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <?php
            $i = date('Y')-15;
            $max = date('Y');
            while ($i <= $max) {?>
               <a class="filters<?php echo((isset($_REQUEST['year']) && $_REQUEST['year'] == $i)?' selected':'');?>" href="admin.php?page=mattevideo_exercise&year=<?php echo $i,$query_string;?>"><?php echo $i;?>(<?php echo $data['listing_count'][$i]  ?>)</a>&nbsp;&nbsp;&nbsp;
             <?php $i++;}?>
        <br/>
        <?php $query_string .= (isset($_REQUEST['year'])?'&year='.$_REQUEST['year']:'');?>
        Term: <a class="filters<?php echo(!isset($_REQUEST['term']) || (isset($_REQUEST['term']) && $_REQUEST['term'] == 'All')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&term=All<?php echo $query_string;?>">All(<?php echo $data['listing_count']['term']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <a class="filters<?php echo((isset($_REQUEST['term']) && $_REQUEST['term'] == 'spring')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&term=spring<?php echo $query_string;?>">spring(<?php echo $data['listing_count']['spring']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <a class="filters<?php echo((isset($_REQUEST['term']) && $_REQUEST['term'] == 'autumn')?' selected':'');?>" href="admin.php?page=mattevideo_exercise&term=autumn<?php echo $query_string;?>">autumn(<?php echo $data['listing_count']['autumn']  ?>)</a>&nbsp;&nbsp;&nbsp;
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="users-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display(); ?>
        </form>

    </div>
    <?php
}

function exercise_render_detail_page($data, $alt1Data, $alt2Data, $alt3Data)
{
    //Create an instance of our package class...
    $testListTable = new Exercise_List_Table();
    $testListTable->set_data($data);
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_detail_items();
    ?>
    <div class="wrap exercise_detail">


        <div id="icon-users" class="icon32"><br/></div>
        <h1>Exercises: user data <a href="admin.php?page=mattevideo_exercise" class="page-title-action">Back to list</a></h1>
        <form id="users_search" method="get" action="">
            <?php
            $params = $_GET;
            $query_string = "";
            if(count($params) > 0){
                if(isset($params['user_id'])){
                    unset($params['user_id']);
                }
                $query_string .= "?".http_build_query($params, '' , "&");
            }
            ?>
            <div>Filter on single user id: <input id="user_id" type="text" name="user_id" value="<?php echo isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : ""; ?>" /><a href="<?php echo $_SERVER['PHP_SELF'].$query_string; ?>"><img class="cross_search hide_img" src="http://s3-us-west-2.amazonaws.com/mattevideoimages/exerciseimages/cross.png" /></a></div>
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
            <input type="hidden" name="detail" value="<?php echo $_REQUEST['detail'] ?>" />
            <input type="hidden" name="orderby" value="<?php echo $_REQUEST['orderby'] ?>" />
            <input type="hidden" name="order" value="<?php echo $_REQUEST['order'] ?>" />
        </form>
        <br><div>Number of entries who answered different alternatives: &nbsp;&nbsp; alt1(<?php echo $alt1Data[0]['COUNT(alt_choose)'];?>) &nbsp;&nbsp;alt2(<?php echo $alt2Data[0]['COUNT(alt_choose)'];?>) &nbsp;&nbsp;alt3(<?php echo $alt3Data[0]['COUNT(alt_choose)'];?>)</div><br />

        <form id="users-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display(); ?>
        </form>

    </div>
    <style>
        .alignleft.actions.bulkactions{
            display: none;
        }
    </style>
    <?php
}

