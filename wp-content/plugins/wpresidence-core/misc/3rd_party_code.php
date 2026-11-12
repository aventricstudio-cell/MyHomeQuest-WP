<?php
function wpestate_core_add_to_footer() {
  $ga = wpresidence_get_option('wp_estate_google_analytics_code', '');
  if (!empty($ga)) {
      $ga = esc_js($ga); // Use esc_js for JavaScript output
      wp_enqueue_script('google-analytics', "https://www.googletagmanager.com/gtag/js?id=$ga", array(), null, true);
      wp_add_inline_script('google-analytics', "
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '$ga');
      ");
  }
}

