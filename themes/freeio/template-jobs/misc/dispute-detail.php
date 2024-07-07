<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

$rand = rand(0000,9999);
$dispute_id = isset($_GET['dispute_id']) ? $_GET['dispute_id'] : 0;
?>
<div class="box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('Dispute Details','freeio') ?></h3>
	<!-- List Disputes -->
	<div class="inner-list">
		<div class="inner-content">
			<div class="job-table-info-content-project">
				<?php
				$post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
				$post_type = get_post_type($post_id);
				if ( $post_type == 'project_proposal' ) {
					$project_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
					$project = get_post($project_id);
					if ( $project ) {
				?>
						<h5 class="dispute-name"><a href="<?php echo esc_url(get_permalink($project)); ?>"><?php echo get_the_title($project_id); ?></a></h5>
					<?php } ?>
				<?php } elseif ( $post_type == 'service_order' ) {
						$service_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
						$service = get_post($service_id);
						if ( $service ) {
					?>
							<h5 class="dispute-name"><a href="<?php echo esc_url(get_permalink($service)); ?>"><?php echo get_the_title($service_id); ?></a></h5>
						<?php } ?>

				<?php } ?>
			</div>
			<h3 class="dispute-title">
				<?php echo get_the_title($dispute_id); ?>
			</h3>
			<div class="listing-metas d-flex align-items-center flex-wrap">
				<div class="created">
					<i class="flaticon-30-days"></i><?php echo get_the_time( get_option('date_format'), $dispute_id ); ?>
				</div>
				<?php
				$resolved = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'resolved', true);
	    		if ( !$resolved ) {
	    			$classes = 'bg-pending';
	    		} else {
	    			$classes = 'bg-success';
	    		}
				?>
				<div class="status">
					<?php echo esc_html__('Status : ','freeio') ?>
					<div class="badge <?php echo esc_attr($classes);?>">
						<?php
							if ( $resolved ) {
								esc_html_e('Resolved', 'freeio');
							} else {
								esc_html_e('Ongoing', 'freeio');
							}
						?>
					</div>
				</div>
			</div>
			<div class="description-dispute">
				<?php echo wpautop(get_post_field('post_content', $dispute_id)); ?>
			</div>
		</div>
	</div>
	<?php
	$admin_response = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'admin_response', true);
	if ($admin_response) {
	?>
		<div class="inner-list">
			<h3 class="title-small"><?php esc_html_e('Admin Feedback', 'freeio'); ?></h3>
			<div class="admin_response">
				<?php
				echo wpautop($admin_response);
				?>
			</div>
		</div>
	<?php } ?>
	<div class="inner-list">
		<div id="messages" class="messages messages-service-history">
			<div id="messages-list" class="messages-list">
				<?php echo WP_Freeio_Post_Type_Dispute::list_dispute_messages($dispute_id); ?>
			</div>
			<?php if ( !$resolved ) { ?>
				<div class="dispute-message-form-wrapper">
					<form id="dispute-message-form-<?php echo esc_attr($dispute_id); ?>" method="post" action="?" class="dispute-message-form form-theme" action="" enctype="multipart/form-data">
			            <div class="form-group">
			                <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
			            </div><!-- /.form-group -->
			            
			            <input type="hidden" name="dispute_id" value="<?php echo esc_attr($dispute_id); ?>">
			            <button class="button btn btn-theme btn-outline" name="contact-form"><?php echo esc_html__( 'Send Message', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
			        </form>
				</div>
			<?php } ?>
		</div>
	</div>

</div>