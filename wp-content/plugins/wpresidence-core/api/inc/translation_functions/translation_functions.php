<?php

/**
 * Filter translations to enable fallback between theme and plugin
 */
function wpresidence_translation_fallback($translation, $text, $domain) {
    static $in_filter = false;
    
    // Only run filter after init
    if (!did_action('init') || $in_filter) {
        return $translation;
    }
    
    $in_filter = true;
    
    // Only try fallback if we have no translation
    if ($translation === $text) {
        // Try the alternative domain
        $alt_domain = ($domain === 'wpresidence-core') ? 'wpresidence' : 'wpresidence-core';
        
        // Use get_translations_for_domain directly to prevent recursion
        $alt_translations = get_translations_for_domain($alt_domain);
        if ($alt_translations && $alt_translations->translate($text) !== $text) {
            $translation = $alt_translations->translate($text);
        }
    }
    
    $in_filter = false;
    return $translation;
}

// Add the filter after init to avoid early loading
function wpresidence_add_translation_fallback() {
    add_filter('gettext', 'wpresidence_translation_fallback', 10, 3);
}
add_action('init', 'wpresidence_add_translation_fallback', 20);