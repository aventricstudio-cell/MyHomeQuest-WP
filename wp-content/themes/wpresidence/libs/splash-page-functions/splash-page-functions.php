<?php

if (!function_exists('wpestate_splash_page_header')):

    function wpestate_splash_page_header() {

        $spash_header_type = wpresidence_get_option('wp_estate_spash_header_type', '');

        if ($spash_header_type == 'image') {
            $image = esc_html(wpresidence_get_option('wp_estate_splash_image', 'url'));
            wpestate_header_image($image);
        } else if ($spash_header_type == 'video') {
            wpestate_video_header();
        } else if ($spash_header_type == 'image slider') {
            wpestate_splash_slider();
        }
    }

endif;


if (!function_exists('wpestate_splash_slider')):

    function wpestate_splash_slider() {
        $splash_slider_gallery = esc_html(wpresidence_get_option('wp_estate_splash_slider_gallery', ''));
        $splash_slider_transition = esc_html(wpresidence_get_option('wp_estate_splash_slider_transition', ''));

        $splash_slider_gallery_array = explode(',', $splash_slider_gallery);


        $slider = '<div id="splash_slider_wrapper" class="carousel slide" data-bs-ride="false" data-bs-interval="false">';
        $slider .= '<div class="carousel-inner h-100">';

        $i = 0;
        if (is_array($splash_slider_gallery_array)) {
            foreach ($splash_slider_gallery_array as $image_id) {
                $image_id = intval($image_id);
                if ($image_id != '' && $image_id != 0) {
                    $i++;
                    if ($i == 1) {
                        $class_active = ' active ';
                    } else {
                        $class_active = '  ';
                    }
                    $preview = wp_get_attachment_image_src($image_id, 'full');
                    $slider .= '<div class="carousel-item splash_slider_item h-100';
                    $slider .= $class_active . ' "  style="background-image:url(' . esc_url($preview[0]) . ');" >


                </div>';
                }
            }
        }

        $slider .= '</div>';

        $page_header_overlay_val = esc_html(wpresidence_get_option('wp_estate_splash_overlay_opacity', ''));
        $page_header_overlay_color = esc_html(wpresidence_get_option('wp_estate_splash_overlay_color', ''));
        $wp_estate_splash_overlay_image = esc_html(wpresidence_get_option('wp_estate_splash_overlay_image', 'url'));
        $page_header_title_over_image = stripslashes(esc_html(wpresidence_get_option('wp_estate_splash_page_title', '')));
        $page_header_subtitle_over_image = stripslashes(esc_html(wpresidence_get_option('wp_estate_splash_page_subtitle', '')));

        if (is_page_template('page-templates/splash_page.php')) {
            if ($page_header_overlay_color != '' || $wp_estate_splash_overlay_image != '') {
                $slider .= '<div class="wpestate_header_image_overlay" style="background-color:' . $page_header_overlay_color . ';opacity:' . $page_header_overlay_val . ';background-image:url(' . esc_url($wp_estate_splash_overlay_image) . ');"></div>';
            }
        }

        if ($page_header_overlay_color != '' || $wp_estate_splash_overlay_image != '') {
            $slider .= '<div class="wpestate_header_image_overlay" style="background-color:#' . $page_header_overlay_color . ';opacity:' . $page_header_overlay_val . ';background-image:url(' . esc_url($wp_estate_splash_overlay_image) . ');"></div>';
        }

        if ($page_header_title_over_image != '') {
            $slider .= '<div class="heading_over_image_wrapper" >';
            $slider .= '<h1 class="heading_over_image">' . $page_header_title_over_image . '</h1>';

            if ($page_header_subtitle_over_image != '') {
                $slider .= '<div class="subheading_over_image  exclude-rtl">' . $page_header_subtitle_over_image . '</div>';
            }

            $slider .= '</div>';
        }


        $slider .= '<button class="carousel-control-prev" type="button" data-bs-target="#splash_slider_wrapper" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">' . esc_html__('Previous', 'wpestate') . '</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#splash_slider_wrapper" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">' . esc_html__('Next', 'wpestate') . '</span>
        </button>';

        $slider .= '</div>';


        print trim($slider);
    }

endif;


if (!function_exists('wpestate_video_header')):

    function wpestate_video_header() {

        global $post;
        $paralax_header = wpresidence_get_option('wp_estate_paralax_header', '');
        if (isset($post->ID)) {
            if (is_page_template('page-templates/splash_page.php')) {
                $page_custom_video = esc_html(wpresidence_get_option('wp_estate_splash_video_mp4', 'url'));
                $page_custom_video_webm = esc_html(wpresidence_get_option('wp_estate_splash_video_webm', 'url'));
                $page_custom_video_ogv = esc_html(wpresidence_get_option('wp_estate_splash_video_ogv', 'url'));
                $page_custom_video_cover_image = esc_html(wpresidence_get_option('wp_estate_splash_video_cover_img', 'url'));
                $img_full_screen = 'no';
                $page_header_title_over_video = stripslashes(esc_html(wpresidence_get_option('wp_estate_splash_page_title', '')));
                $page_header_subtitle_over_video = stripslashes(esc_html(wpresidence_get_option('wp_estate_splash_page_subtitle', '')));
                $page_header_video_height = '';
                $page_header_overlay_color_video = esc_html(wpresidence_get_option('wp_estate_splash_overlay_color', ''));
                $page_header_overlay_val_video = esc_html(wpresidence_get_option('wp_estate_splash_overlay_opacity', ''));
                $wp_estate_splash_overlay_image = esc_html(wpresidence_get_option('wp_estate_splash_overlay_image', 'url'));
            } else {
                $page_custom_video = esc_html(get_post_meta($post->ID, 'page_custom_video', true));
                $page_custom_video_webm = esc_html(get_post_meta($post->ID, 'page_custom_video_webbm', true));
                $page_custom_video_ogv = esc_html(get_post_meta($post->ID, 'page_custom_video_ogv', true));
                $page_custom_video_cover_image = esc_html(get_post_meta($post->ID, 'page_custom_video_cover_image', true));
                $img_full_screen = esc_html(get_post_meta($post->ID, 'page_header_video_full_screen', true));
                $page_header_title_over_video = stripslashes(esc_html(get_post_meta($post->ID, 'page_header_title_over_video', true)));
                $page_header_subtitle_over_video = stripslashes(esc_html(get_post_meta($post->ID, 'page_header_subtitle_over_video', true)));
                $page_header_video_height = floatval(get_post_meta($post->ID, 'page_header_video_height', true));
                $page_header_overlay_color_video = esc_html(get_post_meta($post->ID, 'page_header_overlay_color_video', true));
                $page_header_overlay_val_video = esc_html(get_post_meta($post->ID, 'page_header_overlay_val_video', true));
                $wp_estate_splash_overlay_image = '';
            }


            if ($page_header_overlay_val_video == '') {
                $page_header_overlay_val_video = 1;
            }
            if ($page_header_video_height == 0) {
                $page_header_video_height = 580;
            }


            print '<div class="wpestate_header_video full_screen_' . $img_full_screen . ' parallax_effect_' . $paralax_header . '" style="';
            print ' height:' . $page_header_video_height . 'px; ';
            print '">';


            print '<video id="hero-vid" class="header_video" poster="' . $page_custom_video_cover_image . '" width="100%" height="100%" autoplay playsinline';
            if (wp_is_mobile()) {
                print ' controls ';
            }
            print' muted loop>
			<source src="' . esc_url($page_custom_video) . '" type="video/mp4" />
			<source src="' . esc_url($page_custom_video_webm) . '" type="video/webm" />
                        <source src="' . esc_url($page_custom_video_ogv) . '" type="video/ogg"/>

		</video>';

            if (is_page_template('page-templates/splash_page.php')) {
                if ($page_header_overlay_color_video != '' || $wp_estate_splash_overlay_image != '') {
                    print '<div class="wpestate_header_video_overlay" style="background-color:' . $page_header_overlay_color_video . ';opacity:' . $page_header_overlay_val_video . ';background-image:url(' . esc_url($wp_estate_splash_overlay_image) . ');"></div>';
                }
            }

            if ($page_header_overlay_color_video != '' || $wp_estate_splash_overlay_image != '') {
                print '<div class="wpestate_header_video_overlay" style="background-color:#' . $page_header_overlay_color_video . ';opacity:' . $page_header_overlay_val_video . ';background-image:url(' . esc_url($wp_estate_splash_overlay_image) . ');"></div>';
            }

            if ($page_header_title_over_video != '') {
                print '<div class="heading_over_video_wrapper" >';
                print '<h1 class="heading_over_video">' . $page_header_title_over_video . '</h1>';

                if ($page_header_subtitle_over_video != '') {
                    print '<div class="subheading_over_video">' . $page_header_subtitle_over_video . '</div>';
                }

                print '</div>';
            }


            print'</div>';
        }
    }

endif;


