<?php
/** MILLDONE
 * Template Name: Contact Page
 * src: page-templates/contact_page.php
 * This template handles the display of the contact page in the WpResidence theme.
 * It shows company details, social links, and a contact form.
 *
 * @package WpResidence
 * @subpackage ContactPage
 * @since 1.0.0
 */

// Check for WpResidence Core Plugin
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin, Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

get_header();

$wpestate_options = get_query_var('wpestate_options');

// Retrieve company details from theme options
$company_details = array(
    'name'      => wpresidence_get_option('wp_estate_company_name', ''),
    'picture'   => wpresidence_get_option('wp_estate_company_contact_image', 'url'),
    'email'     => wpresidence_get_option('wp_estate_email_adr', ''),
    'mobile'    => wpresidence_get_option('wp_estate_mobile_no', ''),
    'telephone' => wpresidence_get_option('wp_estate_telephone_no', ''),
    'fax'       => wpresidence_get_option('wp_estate_fax_ac', ''),
    'skype'     => wpresidence_get_option('wp_estate_skype_ac', ''),
    'address'   => function_exists('icl_translate') 
                   ? icl_translate('wpestate', 'wp_estate_co_address_text', wpresidence_get_option('wp_estate_co_address'))
                   : wpresidence_get_option('wp_estate_co_address', '')
);

$company_details = array_map('esc_html', array_map('stripslashes', $company_details));

$agent_email = $company_details['email'];
$social_defaults = wpestate_return_social_links_icons();
?>

<div class="row wpresidence_page_content_wrapper">
    <?php get_template_part('templates/breadcrumbs'); ?>
    <div class="p-0 p04mobile wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?>">
        <?php get_template_part('templates/ajax_container'); ?>
        
        <?php while (have_posts()) : the_post(); ?>
            <div class="contact-wrapper row">    
                <div class="contact_page_company_details">
                    <div class="company_headline">   
                        <h3><?php echo esc_html($company_details['name']); ?></h3>
                        <div class='company_headlin_addr'><?php echo esc_html($company_details['address']); ?></div>
                        
                        <div class="header_social">
                            <?php
                            foreach ($social_defaults as $key => $value) {
                                if (isset($value['contact_option'])) {
                                    $link_value = wpresidence_get_option($value['contact_option'], '');
                                    if ($link_value != '') {
                                        echo '<a href="' . esc_url($link_value) . '" rel="noopener" >' . $value['icon'] . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>     
                    </div>   
         
                    <?php
                    $contact_details = array(
                        'Phone'   => $company_details['telephone'],
                        'Mobile'  => $company_details['mobile'],
                        'Email'   => $company_details['email'],
                        'Fax'     => $company_details['fax'],
                        'Skype'   => $company_details['skype']
                    );

                    foreach ($contact_details as $label => $value) {
                        if ($value) {
                            echo '<div class="agent_detail contact_detail"><span>' . esc_html__($label . ':', 'wpresidence') . '</span> ';
                            if ($label === 'Email') {
                                echo '<a href="mailto:' . antispambot( esc_attr( $value)) . '">' . antispambot( esc_html($value)) . '</a>';
                            } elseif ($label === 'Phone' || $label === 'Mobile') {
                                echo '<a href="tel:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
                            } else {
                                echo esc_html($value);
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
        
                <div class="company_headline_content">
                    <?php the_content(); ?>
                </div>
                
                <?php if ($company_details['picture'] != '') : ?>
                    <div class="contact_page_company_picture">
                        <img src="<?php echo esc_url($company_details['picture']); ?>" class="contact-comapany-logo img-responsive" alt="<?php esc_attr_e('company logo', 'wpresidence'); ?>" />
                    </div>
                <?php endif; ?>
                
            </div>    
           
            <div class="single-content contact-content">   
                <div class="agent_contanct_form wpestate_contact_form_parent"> 
                <?php  
                    $context = 'contact_page';
                    include(locate_template('templates/listing_templates/contact_form/property_page_contact_form.php'));
                ?>
               </div>
           </div>
           
        <?php endwhile; ?>
    </div>
  
    <?php include get_theme_file_path('sidebar.php'); ?>
</div>   
<?php get_footer(); ?>