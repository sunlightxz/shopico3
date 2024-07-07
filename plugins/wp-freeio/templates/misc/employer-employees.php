<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = WP_Freeio_User::get_user_id();
$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$users = WP_Freeio_Query::get_employer_employees($employer_id, array(
    'post_per_page' => get_option('posts_per_page'),
    'paged' => $paged
));

wp_enqueue_script('jquery-ui-autocomplete');
?>

<div class="employer-employees-member">
	<div class="employer-employees-list">
		<h3><?php esc_html_e('Employees', 'wp-freeio'); ?></h3>
		<div class="employer-employees-list-inner">
	        <?php
	        	if ( !empty($users) ) {
		        	$employee_style = apply_filters('wp-freeio-employee-inner-list-team', 'inner-list-team');
		            foreach ($users as $user) {
		            	echo WP_Freeio_Template_Loader::get_template_part( 'employees-styles/'.$employee_style, array('userdata' => $user) );
		            }
	            }  else { ?>

				<div class="not-found"><?php esc_html_e('No employees found.', 'wp-freeio'); ?></div>

			<?php }  ?>
	    </div>
	    
	    <?php
		    if ( !empty($users) ) {
		    $all_users = WP_Freeio_Query::get_employer_employees($employer_id, array(
			    'post_per_page' => -1,
			    'paged' => 1,
			    'fields' => 'ids'
			));
			$count_users = !empty($all_users) ? count($all_users) : 0;
			$max_num_pages = ceil($count_users/get_option('posts_per_page'));
		    WP_Freeio_Mixes::custom_pagination2( array(
				'prev_text'     => __( 'Previous page', 'wp-freeio' ),
				'next_text'     => __( 'Next page', 'wp-freeio' ),
				'per_page' 		=> get_option('posts_per_page'),
				'current' 		=> $paged,
				'max_num_pages' => $max_num_pages,
			));
		} ?>
	</div>
	<!-- Form list -->
	<div class="employer-employee-form-wrapper">
		<h3><?php esc_html_e('Add Employee', 'wp-freeio'); ?></h3>
		
		<form action="" method="get" class="employer-add-employees-form">
			
			<div class="form-group">
				<label for="register-username"><?php esc_html_e('Username', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="text" class="form-control" name="username" id="register-username" placeholder="<?php esc_attr_e('Enter Username','wp-freeio'); ?>">
			</div>
			<div class="form-group">
				<label for="register-email"><?php esc_html_e('Email', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="text" class="form-control" name="email" id="register-email" placeholder="<?php esc_attr_e('Enter Email','wp-freeio'); ?>">
			</div>
			<div class="form-group">
				<label for="password"><?php esc_html_e('Password', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="password" class="form-control" name="password" id="password" placeholder="<?php esc_attr_e('Enter Password','wp-freeio'); ?>">
			</div>
			<div class="form-group">
				<label for="confirmpassword"><?php esc_html_e('Confirm Password', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="<?php esc_attr_e('Re-enter Password','wp-freeio'); ?>">
			</div>

			<div class="form-group">
				<button class="search-submit btn btn-sm btn-theme" name="submit"><?php echo esc_html__( 'Add Employee', 'wp-freeio' ); ?></button>
				<input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-employer-add-employee-nonce' )); ?>">
			</div>
		</form>
	</div>
</div>