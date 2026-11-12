<?php
/** MILLDONE
 * Property Description and Price Management for WpResidence Theme
 * src: templates/submit_templates/property_description.php
 * This file contains the code for managing property descriptions and prices in the WpResidence WordPress theme.
 * It allows users to input and edit property details such as title, description, price, and related labels.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 1.0.0
 */

// Helper function to get and escape submitted values
function wpestate_get_submitted_value($field, $get_listing_edit, $type = '') {
    $value = wpestate_submit_return_value($field, $get_listing_edit, $type);
    return stripslashes($value);
}

// Property Description Section
?>
<div class="profile-onprofile row">
    <div class="wpestate_dashboard_section_title"><?php esc_html_e('Property Description', 'wpresidence'); ?></div>
    <input type="hidden" name="is_user_submit" value="1">

    <div class="col-md-12">
        <label for="title"><?php esc_html_e('*Title (mandatory)', 'wpresidence'); ?></label>
        <input type="text" id="title" class="form-control" value="<?php echo esc_attr(wpestate_get_submitted_value('wpestate_title', $get_listing_edit)); ?>" name="wpestate_title">
    </div>

    <?php if (is_array($wpestate_submission_page_fields) && in_array('wpestate_description', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-12">
            <label for="description"><?php esc_html_e('Description', 'wpresidence'); ?></label>
            <?php
            $submit_description = wpestate_get_submitted_value('wpestate_description', $get_listing_edit);
            wp_editor(
                $submit_description,
                'description',
                array(
                    'textarea_rows' => 6,
                    'textarea_name' => 'wpestate_description',
                    'wpautop'       => true,
                    'media_buttons' => false,
                    'tinymce'       => false,
                    'quicktags'     => array("buttons" => "strong,em,block,ins,ul,li,ol,close"),
                )
            );
            ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Property Price Section
$price_fields = array(
    'property_price', 'property_label', 'property_label_before',
    'property_second_price', 'property_second_price_label', 'property_label_before_second_price',
    'property_year_tax', 'property_hoa', 'local_show_hide_price'
);

$show_price_section = is_array($wpestate_submission_page_fields) && 
                      count(array_intersect($price_fields, $wpestate_submission_page_fields)) > 0;

$local_show_hide_price = wpestate_submit_return_value('local_show_hide_price', $get_listing_edit, '');
$local_show_hide_price_check = ($local_show_hide_price == 'yes') ? ' checked="checked" ' : '';

if ($show_price_section) :
?>
<div class="profile-onprofile row">
    <div class="wpestate_dashboard_section_title"><?php esc_html_e('Property Price', 'wpresidence'); ?></div>

    <?php
    $currency_symbol = wpresidence_get_option('wp_estate_currency_symbol', '');
    
    foreach ($price_fields as $field) {
        if (is_array($wpestate_submission_page_fields) && in_array($field, $wpestate_submission_page_fields)) {
            $value = wpestate_get_submitted_value($field, $get_listing_edit, $field === 'property_price' ? 'numeric' : '');
            $type = in_array($field, array('property_price', 'property_second_price', 'property_year_tax', 'property_hoa')) ? 'number' : 'text';
            if ( $field == 'local_show_hide_price' )    {
                $type  = 'checkbox';
                $value = 'yes';
            }
            $step = $type === 'number' ? 'step="any"' : '';

            ?>
            <div class="col-md-6">
                <label for="<?php echo esc_attr($field); ?>">
                    <?php
                    switch ($field) {
                        case 'property_price':
                            echo esc_html__('Price in ', 'wpresidence') . esc_html($currency_symbol) . ' ' . esc_html__('(only numbers)', 'wpresidence');
                            break;
                        case 'property_label':
                            esc_html_e('After Price Label (ex: "/month")', 'wpresidence');
                            break;
                        case 'property_label_before':
                            esc_html_e('Before Price Label (ex: "from ")', 'wpresidence');
                            break;
                        case 'property_second_price':
                            echo esc_html__('Second Price in ', 'wpresidence') . esc_html($currency_symbol) . ' ' . esc_html__('(only numbers)', 'wpresidence');
                            break;
                        case 'property_second_price_label':
                            esc_html_e('After Second Price Label (ex: "/month")', 'wpresidence');
                            break;
                        case 'property_label_before_second_price':
                            esc_html_e('Before Second Price Label (ex: "from ")', 'wpresidence');
                            break;
                        case 'property_year_tax':
                            esc_html_e('Yearly Tax Rate', 'wpresidence');
                            break;
                        case 'property_hoa':
                            esc_html_e('Homeowners Association Fee (monthly)', 'wpresidence');
                            break;
                        // case 'local_show_hide_price':
                        //     esc_html_e('Hide/Show Price', 'wpresidence');
                        //     break;
                    }
                    ?>
                </label>
                <?php if ( $field === 'local_show_hide_price' ) { ?>
                    <input type="hidden" name="<?php echo esc_attr($field); ?>" value="no">
                    <input type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($field); ?>" name="<?php echo esc_attr($field); ?>" value="<?php echo esc_attr($value); ?>" <?php echo esc_attr($local_show_hide_price_check); ?>>
                    <label for="local_show_hide_price" class="wpestate_check_label"><?php esc_html_e('Hide/Show Price?', 'wpresidence'); ?></label>
                <?php } else { ?>
                <input type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($field); ?>" class="form-control" name="<?php echo esc_attr($field); ?>" value="<?php echo esc_attr($value); ?>" <?php echo $step; ?>>
                <?php } ?>
            </div>
            <?php
        }
    }
    ?>
</div>
<?php endif; ?>