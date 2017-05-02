<?php
/**
 * Template Name: Map Quiz Page
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();
global $post;
?>
<div id="container" class="default container">

    <div id="content" class="<?php echo(is_user_logged_in() ? 'custom_margin' : '') ?>" role="standar">

        <?php
        /* Run the loop to output the page.

         * If you want to overload this in a child theme then include a file

         * called loop-page.php and that will be used instead.

         */

        get_template_part('loop', 'page-notitle');
        ?>



    </div><!-- #content -->

    <?php
    //	get frontpage boxes articles because thats whats supposed to be shown below

    getTeaserPosts();
    ?>	
    <script>
        $("#pfbc").prev('h4').addClass('my-custom-h4');
    </script>
</div>
</div>

<?php
get_sidebar();

get_footer();
?>