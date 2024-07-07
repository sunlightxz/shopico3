<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

	
<div class="box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('Statements','freeio') ?></h3>
	<?php
	$user_id = WP_Freeio_User::get_user_id();
	$total_balance = WP_Freeio_Post_Type_Withdraw::get_freelancer_balance($user_id);
	$total_earning = isset($total_balance['total_earning']) ? $total_balance['total_earning'] : 0;
	$current_balance = isset($total_balance['current_balance']) ? $total_balance['current_balance'] : 0;
	$total_earning_pending = isset($total_balance['total_earning_pending']) ? $total_balance['total_earning_pending'] : 0;
	$withdrawn = isset($total_balance['withdrawn']) ? $total_balance['withdrawn'] : 0;
	?>
	<div class="wt-withdraw-user statistics row">
		<div class="col-12 col-xl-3 col-sm-6">
			<div class="inner-header">
				<div class="posted-services list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<span><?php esc_html_e('Net income', 'freeio'); ?></span>
						<div class="number-count"><?php echo WP_Freeio_Price::format_price($total_earning, true);?></div>
					</div>
					<div class="icon-wrapper">
						<div class="icon">
							<i class="flaticon-income"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-xl-3 col-sm-6">
			<div class="inner-header">
				<div class="posted-services list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<span><?php esc_html_e('Withdrawn', 'freeio'); ?></span>
						<div class="number-count"><?php echo WP_Freeio_Price::format_price($withdrawn, true);?></div>
					</div>
					<div class="icon-wrapper">
						<div class="icon">
							<i class="flaticon-withdraw"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-xl-3 col-sm-6">
			<div class="inner-header">
				<div class="posted-services list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<span><?php esc_html_e('Pending Clearance', 'freeio'); ?></span>
						<div class="number-count"><?php echo WP_Freeio_Price::format_price($total_earning_pending, true);?></div>
					</div>
					<div class="icon-wrapper">
						<div class="icon">
							<i class="flaticon-sandclock"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-xl-3 col-sm-6">
			<div class="inner-header">
				<div class="posted-services list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<span><?php esc_html_e('Available for withdrawal', 'freeio'); ?></span>
						<div class="number-count"><?php echo WP_Freeio_Price::format_price($current_balance, true);?></div>
					</div>
					<div class="icon-wrapper">
						<div class="icon">
							<i class="flaticon-price-tag"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php
	$minimum_withdraw_amount = wp_freeio_get_option('minimum_withdraw_amount', 50);
	if ( $current_balance > $minimum_withdraw_amount ) { ?>
		<div class="mb-5">
			<div class="wt-btnarea">
				<a href="#show-withdraw-form-<?php echo esc_attr($user_id); ?>" class="btn btn-theme btn-inverse btn-show-popup"><?php esc_html_e('Withdraw now', 'freeio'); ?></a>
			</div>

			<div id="show-withdraw-form-<?php echo esc_attr($user_id); ?>" class="view-proposal-description-wrapper mfp-hide">
				<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>
				<form id="withdraw-form" class="withdraw-form form-theme" method="post" action="?">
					<div class="row">
						<div class="col-12">
		                    <div class="form-group">
		                    	<label for="input-amount-id"><?php esc_html_e('Amount', 'freeio'); ?></label>
		                        <input id="input-amount-id" type="text" class="form-control" name="amount" placeholder="<?php esc_attr_e( 'Amount', 'freeio' ); ?>" value="<?php echo esc_attr($current_balance); ?>" required="required">
		                    </div><!-- /.form-group -->
		                </div>

		                <div class="col-12">
		                    <div class="form-group">
		                    	<label for="payout_method-id"><?php esc_html_e('Payout Method', 'freeio'); ?></label>
		                    	<select id="payout_method-id" class="form-control" name="payout_method" required="required">
		                    		<option value=""><?php esc_html_e('Select Payout Method', 'freeio'); ?></option>
		                    		<?php
									$payout_method = get_user_meta($user_id, 'payout_method', true);
									$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
									$withdraw_payout_methods = wp_freeio_get_option('withdraw_payout_methods', array('paypal', 'bacs', 'payoneer'));
									$i=0; foreach ($withdraw_payout_methods as $val) {
										if ( !empty($all_payout_methods[$val]) ) {
											$selected = '';
											if ( ($i == 0 && empty($payout_method)) || ($val == $payout_method ) ) {
												$selected = 'selected="selected"';
											}
										?>
											<option value="<?php echo esc_attr($val); ?>" <?php echo trim($selected); ?>><?php echo esc_html($all_payout_methods[$val]); ?></option>
										<?php
										$i++;
										}
									}
									?>
		                    	</select>
		                    </div><!-- /.form-group -->
		                </div>
		            </div>
		            <div class="clearfix">
		            	<button type="submit" class="btn btn-theme w-100"><?php esc_html_e('Withdraw money', 'freeio'); ?></button>
		            </div>
				</form>
			</div>
		</div>
	<?php } ?>
	
	<div class="inner-list">		
		
		<?php
		$user_id = WP_Freeio_User::get_user_id();
	    if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_vars = array(
		    'post_type' => 'earning',
		    'posts_per_page'    => get_option('posts_per_page'),
		    'paged'    			=> $paged,
		    'post_status' => array('publish', 'pending'),
		    'fields' => 'ids',
		    'author' => $user_id,
		);
		$withdraws = WP_Freeio_Query::get_posts($query_vars);

		if ( !empty($withdraws) && !empty($withdraws->posts) ) {
			?>
			<div class="table-responsive">
				<table class="job-table">
					<thead>
						<tr>
							<th class="date"><?php esc_html_e('Date', 'freeio'); ?></th>
							<th class="date"><?php esc_html_e('Type', 'freeio'); ?></th>
							<th class="date"><?php esc_html_e('Details', 'freeio'); ?></th>
							<th class="amount"><?php esc_html_e('Amount', 'freeio'); ?></th>
							<th class="status"><?php esc_html_e('Status', 'freeio'); ?></th>
						</tr>
					</thead>
					<?php foreach ($withdraws->posts as $post_id) {
						
						$amount = get_post_meta($post_id, WP_FREEIO_EARNING_PREFIX . 'freelancer_amount', true);
						$project_type = get_post_meta($post_id, WP_FREEIO_EARNING_PREFIX . 'project_type', true);

						?>

						<tr <?php post_class('statement-wrapper'); ?>>
							<td>
								<div class="date">
						            <?php echo get_the_time( get_option('date_format', 'd M, Y'), $post_id ); ?>
						        </div>
							</td>
							<td>
								<div class="type">
						            <?php
						            if ( $project_type == 'service' ) {
						            	esc_html_e('Service', 'freeio');
						            } else {
						            	esc_html_e('Project', 'freeio');
						            }
						            ?>
						        </div>
							</td>
							<td>
								<div class="amount">
						        	<?php the_title(); ?>
						        </div>
							</td>
							<td>
								<div class="amount price-wrapper">
						        	<?php echo WP_Freeio_Price::format_price($amount, true); ?>
						        </div>
							</td>
							<td>
								<div class="status">
						        	<?php
					        		$post_status = get_post_status($post_id);
					        		if ( $post_status == 'pending' ) {
					        			$classes = 'bg-pending';
					        		} elseif( $post_status == 'cancelled' ) {
					        			$classes = 'bg-cancelled';
					        		} else {
					        			$classes = 'bg-success';
					        		}

									$post_status_object = get_post_status_object( $post_status );
									?>
									<span class="badge <?php echo esc_attr($classes);?>">
										<?php
										if ( $post_status == 'publish' || $post_status == 'completed' ) {
											esc_html_e('Completed', 'freeio');
										} elseif ( !empty($post_status_object->label) ) {
											echo esc_html($post_status_object->label);
										} else {
											echo esc_html($post_status_object->post_status);
										}
										?>
									</span>
						        </div>
							</td>
						</tr>
						<?php
					}

					?>
				</table>
			</div>
			<?php WP_Freeio_Mixes::custom_pagination( array(
				'wp_query' => $withdraws,
				'max_num_pages' => $withdraws->max_num_pages,
				'prev_text'     => '<i class="ti-angle-left"></i>',
				'next_text'     => '<i class="ti-angle-right"></i>',
			));
		?>

		<?php } else { ?>
			<div class="not-found"><?php esc_html_e('No statement found.', 'freeio'); ?></div>
		<?php } ?>
		</div>
</div>