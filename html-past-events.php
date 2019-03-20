<?php
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}
global $wpdb, $current_user;

$current_user = wp_get_current_user();
$userID                 =   $current_user->ID;
$user_login             =   $current_user->user_login;

$no_of_events   =  '9';
$paged          = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'      =>  'event',
    'author'         =>  $userID,
    'paged'          => $paged,
    'posts_per_page' => $no_of_events,
    'post_status'    => 'publish',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => array(
        'relation' => 'AND',
        array(
            'key' => '_event_start_date',
            'value' => date("Y-m-d"),
            'compare' => '<',
            'type' => 'DATE'
        ) 
    )
);

if( isset ( $_GET['keyword'] ) ) {
    $keyword = trim( $_GET['keyword'] );
    if ( ! empty( $keyword ) ) {
        $args['s'] = $keyword;
    }
}

$events_qry = new WP_Query($args);
?>

<div class="event-content-area">

    <?php
    if (isset($_GET['edit_event']) && !empty($_GET['edit_event'])) {
    ?>
    <div class="md-head"><?php echo esc_html__('EDIT EVENT', TEMPNAME);?></div>
    <?php
    } else {
    ?>
	<div class="md-head"><?php echo esc_html__('PAST EVENTS', TEMPNAME);?> <?php get_template_part( 'template-parts/user-default', 'view' ); ?></div>   
    <?php
    }
    ?>
	<div id="profile_message" class="heroes_messages message"></div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php
            if (isset($_GET['edit_event']) && !empty($_GET['edit_event'])) {
                get_template_part('template-parts/dashboard_event_edit');
            } else {
            ?>
			<div class="my-profile-search">
				<div class="profile-top-left">
					<form method="get" action="<?php echo esc_url(home_url($_SERVER['REQUEST_URI']));?>">
                        <div class="single-input-search">
                        	<input type="hidden" name="user-action" value="<?php echo esc_attr($_GET['user-action']);?>">
                        	<input type="hidden" name="user" value="<?php echo esc_attr($_GET['user']);?>">
                            <input class="form-control" name="keyword" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '';?>" placeholder="<?php echo esc_html__('Search events', TEMPNAME); ?>" type="text">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
				</div>
			</div>

			<div class="my-event-listing">
				<?php if( $events_qry->have_posts() ) { ?>
					<div class="row grid-row">
						<?php

                        while ($events_qry->have_posts()): $events_qry->the_post();
                        	$post_meta_data     = get_post_custom(get_the_ID());

                        	get_template_part('template-parts/dashboard_event_unit');
                    	endwhile;
                    	?>
					</div>
                <?php
	            } else {
	                print '<h4>'.esc_html__('You don\'t have any past events yet!').'</h4>';
	            }?>

	            <hr>

	            <!--start Pagination-->
                <?php heroes_pagination( $events_qry->max_num_pages, $range = 2 ); ?>
                <!--start Pagination-->
			</div>
            <?php
            }
            ?>
		</div>
	</div>
	
</div>