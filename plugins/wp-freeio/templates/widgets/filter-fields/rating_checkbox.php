<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$number_style = isset($field['number_style']) ? $field['number_style'] : '';
$min_number = isset($field['min_number']) ? $field['min_number'] : 1;
$max_number = isset($field['max_number']) ? $field['max_number'] : 5;

$placeholder = !empty($field['placeholder']) ? $field['placeholder'] : sprintf(esc_html__('%s : Any', 'wp-freeio'), $field['name']);
?>
<div class="form-group form-group-<?php echo esc_attr($key); ?> <?php echo esc_attr($number_style); ?> <?php echo esc_attr(!empty($field['toggle']) ? 'toggle-field' : ''); ?> <?php echo esc_attr(!empty($field['hide_field_content']) ? 'hide-content' : ''); ?>">
    <?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
        <label class="heading-label">
            <?php echo trim($field['name']); ?>
            <?php if ( !empty($field['toggle']) ) { ?>
                <i class="fa fa-angle-down" aria-hidden="true"></i>
            <?php } ?>
        </label>
    <?php } ?>
    <div class="form-group-inner inner select-wrapper">
        <?php if ( !empty($field['icon']) ) { ?>
            <i class="<?php echo esc_attr( $field['icon'] ); ?>"></i>
        <?php } ?>

        <ul class="terms-list circle-check">
            <?php if ( $min_number <= $max_number ) {
                if ( $number_style == 'number' ) {
                    for ( $i = $min_number; $i <= $max_number; $i++ ) :
                        $checked = '';
                        if ( !empty($selected) ) {
                            if ( is_array($selected) ) {
                                if ( in_array($i, $selected) ) {
                                    $checked = ' checked="checked"';
                                }
                            } elseif ( $i == $selected ) {
                                $checked = ' checked="checked"';
                            }
                        }
                    ?>
                        <li class="list-item"><input id="<?php echo esc_attr($name).'-'.$i; ?>" type="checkbox" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr( $i ); ?>" <?php echo trim($checked); ?>>
                            <label for="<?php echo esc_attr($name).'-'.$i; ?>">
                                <?php echo esc_attr( $i ); ?> 
                                <?php
                                    if ( !empty($field['rating_suffix_'.$i]) ) {
                                        echo trim($field['rating_suffix_'.$i]);
                                    }
                                ?>
                            </label>
                        </li>
                    <?php endfor;
                } else {
                    for ( $i = $min_number; $i <= $max_number; $i++ ) :
                        $checked = '';
                        if ( !empty($selected) ) {
                            if ( is_array($selected) ) {
                                if ( in_array($i, $selected) ) {
                                    $checked = ' checked="checked"';
                                }
                            } elseif ( $i == $selected ) {
                                $checked = ' checked="checked"';
                            }
                        }
                    ?>
                        <li class="list-item"><input id="<?php echo esc_attr($name).'-'.$i; ?>" type="checkbox" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr( $i ); ?>+" <?php echo trim($checked); ?>>
                            <label for="<?php echo esc_attr($name).'-'.$i; ?>">
                                <?php
                                    if ( !empty($field['rating_suffix_'.$i]) ) {
                                        echo trim($field['rating_suffix_'.$i]);
                                    }
                                ?>
                                <?php echo esc_attr( $i ); ?>+
                            </label>
                        </li>
                    <?php endfor;
                }
            } ?>
        </ul>
    </div>
</div><!-- /.form-group -->