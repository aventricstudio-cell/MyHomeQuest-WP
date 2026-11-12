<?php

add_action('admin_post_save_custom_translations', 'wpestate_save_custom_translations');
add_action('wp_ajax_load_mo_strings', 'wpestate_ajax_load_mo_strings');
add_action('init', 'wpestate_load_custom_translations', 1);

function wpestate_load_custom_translations() {
    $locale = get_locale();
    $custom_mo_file = WPESTATE_PLUGIN_PATH . 'translations/wpresidence-core-' . $locale . '.mo';

    if (file_exists($custom_mo_file)) {
        // Load custom translations with higher priority
        load_textdomain('wpresidence-core', $custom_mo_file);
    }
}

function wpestate_find_original_po_file($locale = null) {
    $locale = $locale ?: get_locale();
    
    // Check common locations
    $possible_locations = array(
        WPESTATE_PLUGIN_PATH . 'languages/wpresidence-core.po',
        WPESTATE_PLUGIN_PATH . 'languages/wpresidence-core.mo',
        WP_LANG_DIR . '/plugins/wpresidence-core-' . $locale . '.mo',
        WP_CONTENT_DIR . '/languages/plugins/wpresidence-core-' . $locale . '.mo',
        WPESTATE_PLUGIN_PATH . 'languages/wpresidence-core.pot',
    );
    
    foreach ($possible_locations as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }
    
    return false;
}
    
/**
 * Parse .mo file to extract strings
 */
function wpestate_parse_mo_file($mo_file) {
    if (!file_exists($mo_file)) {
        return array();
    }
    
    $translations = array();
    $file_content = file_get_contents($mo_file);
    
    // .mo file format parsing (simplified)
    // This is a basic implementation - for production, consider using proper gettext libraries
    if (strlen($file_content) < 20) {
        return array();
    }
    
    // Check magic number
    $magic = unpack('V', substr($file_content, 0, 4))[1];
    if ($magic != 0x950412de && $magic != 0xde120495) {
        return array(); // Not a valid .mo file
    }
    
    // Get number of strings
    $num_strings = unpack('V', substr($file_content, 8, 4))[1];
    $orig_offset = unpack('V', substr($file_content, 12, 4))[1];
    $trans_offset = unpack('V', substr($file_content, 16, 4))[1];
    
    for ($i = 0; $i < $num_strings; $i++) {
        // Get original string info
        $orig_length = unpack('V', substr($file_content, $orig_offset + $i * 8, 4))[1];
        $orig_pos = unpack('V', substr($file_content, $orig_offset + $i * 8 + 4, 4))[1];
        
        // Get translation string info
        $trans_length = unpack('V', substr($file_content, $trans_offset + $i * 8, 4))[1];
        $trans_pos = unpack('V', substr($file_content, $trans_offset + $i * 8 + 4, 4))[1];
        
        // Extract strings
        $original = substr($file_content, $orig_pos, $orig_length);
        $translation = substr($file_content, $trans_pos, $trans_length);
        
        if (!empty($original)) {
            $translations[$original] = $translation;
        }
    }
    
    return $translations;
}
    
/**
 * Load existing custom translations
 */
function wpestate_load_existing_custom_translations($locale = null) {
    $locale = $locale ?: get_locale();

    if (!file_exists(WPESTATE_PLUGIN_PATH . 'translations/')) {
        wp_mkdir_p(WPESTATE_PLUGIN_PATH . 'translations/');
    }

    $custom_po_file = WPESTATE_PLUGIN_PATH . 'translations/wpresidence-core-' . $locale . '.po';
    
   if (file_exists($custom_po_file)) {
        $parsed_data = wpestate_parse_po_file($custom_po_file);

        // Convert to simple key-value array for compatibility
        $translations = array();
        foreach ($parsed_data as $entry) {
            $key = wpestate_create_translation_key($entry['original'], $entry['context']);
            $translations[$key] = $entry['translation'];
        }
        
        return $translations;
    }
    
    return array();
}

/**
 * Create a unique key for translations (handling context)
 */
function wpestate_create_translation_key($original, $context = '') {
    return empty($context) ? $original : $context . "\x04" . $original;
}
    
/**
 * Parse .po file
 */
function wpestate_parse_po_file($file_path) {
    if (!file_exists($file_path)) {
        return array();
    }
    
    $translations = array();
    $content = file_get_contents($file_path);
    
    // Split content into entries (separated by double newlines)
    $entries = preg_split('/\n\s*\n/', trim($content));
    
    foreach ($entries as $entry) {
        
        $entry = trim($entry);
        if (empty($entry) ) {
            continue; // Skip comments and empty entries
        }
        
        $translation_data = wpestate_parse_po_entry($entry);
        if ($translation_data) {
            $translations[] = $translation_data;
        }
    }
    
    return $translations;
}

function wpestate_parse_po_entry($entry) {
    $data = array(
        'context' => '',
        'original' => '',
        'plural' => '',
        'translation' => '',
        'plural_translations' => array(),
        'comments' => array(),
        'references' => array()
    );
    
    $lines = explode("\n", $entry);
    $current_field = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Handle comments and references
        if (preg_match('/^#:\s*(.+)/', $line, $matches)) {
            $data['references'][] = $matches[1];
            continue;
        }
        if (preg_match('/^#\s*(.*)/', $line, $matches)) {
            $data['comments'][] = $matches[1];
            continue;
        }
        
        // Handle msgctxt (context)
        if (preg_match('/^msgctxt\s+"(.*)"$/', $line, $matches)) {
            $current_field = 'context';
            $data['context'] = wpestate_unescape_po_string($matches[1]);
            continue;
        }
        
        // Handle msgid (original string)
        if (preg_match('/^msgid\s+"(.*)"$/', $line, $matches)) {
            $current_field = 'original';
            $data['original'] = wpestate_unescape_po_string($matches[1]);
            continue;
        }
        
        // Handle msgid_plural (plural form)
        if (preg_match('/^msgid_plural\s+"(.*)"$/', $line, $matches)) {
            $current_field = 'plural';
            $data['plural'] = wpestate_unescape_po_string($matches[1]);
            continue;
        }
        
        // Handle msgstr (translation)
        if (preg_match('/^msgstr(?:\[(\d+)\])?\s+"(.*)"$/', $line, $matches)) {
            if (isset($matches[1])) {
                // Plural translation
                $plural_index = intval($matches[1]);
                $data['plural_translations'][$plural_index] = wpestate_unescape_po_string($matches[2]);
            } else {
                // Regular translation
                $current_field = 'translation';
                $data['translation'] = wpestate_unescape_po_string($matches[2]);
            }
            continue;
        }
        
        // Handle continuation lines (multiline strings)
        if (preg_match('/^"(.*)"$/', $line, $matches)) {
            $continuation = wpestate_unescape_po_string($matches[1]);
            
            switch ($current_field) {
                case 'context':
                    $data['context'] .= $continuation;
                    break;
                case 'original':
                    $data['original'] .= $continuation;
                    break;
                case 'plural':
                    $data['plural'] .= $continuation;
                    break;
                case 'translation':
                    $data['translation'] .= $continuation;
                    break;
            }
        }
    }
    
    // Only return entries with non-empty original strings
    if (!empty($data['original'])) {
        return $data;
    }
    
    return null;
}
    
/**
 * Unescape .po string format
 */
function wpestate_unescape_po_string($string) {
    // Handle escaped characters
    $replacements = array(
        '\\"' => '"',
        '\\\\' => '\\',
        '\\n' => "\n",
        '\\r' => "\r",
        '\\t' => "\t"
    );
    
    return str_replace(array_keys($replacements), array_values($replacements), $string);
}
    
/**
 * AJAX handler for loading .mo strings
 */
function wpestate_ajax_load_mo_strings() {
    check_ajax_referer('custom_translation_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $locale = sanitize_text_field($_POST['locale'] ?? get_locale());
    $text_domain = sanitize_text_field($_POST['text_domain'] ?? 'wpresidence-core');
    $custom_po_path = sanitize_text_field($_POST['custom_po_path'] ?? '');
    
    // Find and parse original .po file
    $original_po_file = '';
    $original_translations = array();
    
    if (!empty($custom_po_path) && file_exists($custom_po_path)) {
        // Use custom provided .po file path
        $original_po_file = $custom_po_path;
        $original_translations = wpestate_parse_po_file($original_po_file);
    } else {
        // Find default .po file
        $original_po_file = wpestate_find_original_po_file($locale);
        if ($original_po_file) {
            $original_translations = wpestate_parse_po_file($original_po_file);
        }
    }

    
    
    // Load existing custom translations
    $custom_translations = wpestate_load_existing_custom_translations($locale);
   
    // Combine translations
    $all_strings = array();
    foreach ($original_translations as $entry) {
        if (!empty(trim($entry['original']))) {
            $key = wpestate_create_translation_key($entry['original'], $entry['context']);
            
            // Determine display translation (use plural form if available for singular)
            $display_translation = $entry['translation'];
            if (empty($display_translation) && !empty($entry['plural_translations'][0])) {
                $display_translation = $entry['plural_translations'][0];
            }
            
            $all_strings[] = array(
                'original' => $entry['original'],
                'context' => $entry['context'],
                'plural' => $entry['plural'],
                'default_translation' => $display_translation,
                'custom_translation' => isset($custom_translations[$key]) ? $custom_translations[$key] : '',
                'has_custom' => isset($custom_translations[$key]) && !empty($custom_translations[$key]),
                'references' => implode(', ', $entry['references']),
                'comments' => implode(' | ', $entry['comments']),
                'type' => wpestate_determine_string_type($entry)
            );
        }
    }
    
    // Sort by original string for better organization
    usort($all_strings, function($a, $b) {
        return strcmp($a['original'], $b['original']);
    });
    
    wp_send_json_success(array(
        'strings' => $all_strings,
        'count' => count($all_strings),
        'original_file' => $original_po_file ? basename($original_po_file) : 'Not found',
        'original_file_path' => $original_po_file,
        'custom_dir' => WPESTATE_PLUGIN_PATH . 'translations/',
    ));
}

function wpestate_determine_string_type($entry) {
    if (!empty($entry['context']) && !empty($entry['plural'])) {
        return 'plural_context';
    } elseif (!empty($entry['plural'])) {
        return 'plural';
    } elseif (!empty($entry['context'])) {
        return 'context';
    } else {
        return 'simple';
    }
}
    
/**
 * Save custom translations
 */
function wpestate_save_custom_translations() {
    check_admin_referer('save_custom_translations_nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $custom_translations = $_POST['custom_translations'] ?? array();
    $locale = sanitize_text_field($_POST['locale'] ?? get_locale());
    $text_domain = sanitize_text_field($_POST['text_domain'] ?? 'wpresidence-core');
    
    // Filter out empty translations
    $filtered_translations = array();
    foreach ($custom_translations as $original => $translation) {
        $original = stripslashes($original);
        $translation = stripslashes($translation);
        
        if (!empty(trim($translation))) {
            $filtered_translations[$original] = $translation;
        }
    }
    
    // Generate .po file content
    $po_content = wpestate_generate_custom_po_content($filtered_translations, $locale);

    // Save custom .po file
    $custom_po_file = WPESTATE_PLUGIN_PATH . 'translations/wpresidence-core-' . $locale . '.po';
    file_put_contents($custom_po_file, $po_content);
    
    // Generate custom .mo file
    wpestate_generate_mo_file($custom_po_file);
    
    wp_redirect(admin_url('admin.php?page=wpresidence-post-type-control-translator&saved=1&count=' . count($filtered_translations)));
    exit;
}
    
/**
 * Generate custom .po file content
 */
function wpestate_generate_custom_po_content($translations, $locale) {
    $content = '# Custom translation overrides for wpresidence-core' . "\n";
    $content .= '# Generated on ' . date('Y-m-d H:i:s') . "\n";
    $content .= '# These translations override the default ones' . "\n";
    $content .= 'msgid ""' . "\n";
    $content .= 'msgstr ""' . "\n";
    $content .= '"Language: ' . $locale . '\n"' . "\n";
    $content .= '"Content-Type: text/plain; charset=UTF-8\n"' . "\n";
    $content .= '"X-Generator: Custom Translation Override\n"' . "\n";
    $content .= "\n";
    
    foreach ($translations as $original => $translation) {
        if (!empty(trim($original)) && !empty(trim($translation))) {
            $content .= 'msgid "' . addcslashes($original, '"\\') . '"' . "\n";
            $content .= 'msgstr "' . addcslashes($translation, '"\\') . '"' . "\n";
            $content .= "\n";
        }
    }
    
    return $content;
}
    
/**
 * Generate .mo file from .po file
 */
function wpestate_generate_mo_file($po_file) {
    $mo_file = str_replace('.po', '.mo', $po_file);
    
    // Try using msgfmt if available
    if (function_exists('exec') && !empty(shell_exec('which msgfmt'))) {
        exec("msgfmt -o '$mo_file' '$po_file' 2>/dev/null");
        return;
    }
    
    // Fallback: Basic .mo generation
    wpestate_create_mo_file($po_file, $mo_file);
}
    
/**
 * Basic .mo file creation
 */
function wpestate_create_mo_file($po_file, $mo_file) {
    $translations = wpestate_parse_po_file($po_file);

    if (empty($translations)) {
        return;
    }
    
    // Create basic .mo file structure
    $keys = array_keys($translations);
    $values = array_values($translations);
    
    // Calculate offsets
    $key_offsets = array();
    $value_offsets = array();
    $current_offset = 28 + (count($translations) * 16); // Header + index tables
    
    foreach ($keys as $key) {
        $key_offsets[] = array('length' => strlen($key), 'offset' => $current_offset);
        $current_offset += strlen($key) + 1;
    }
    
    foreach ($values as $value) {
        if ( is_array( $value ) )
            continue;

        $value_offsets[] = array('length' => strlen($value), 'offset' => $current_offset);
        $current_offset += strlen($value) + 1;
    }
    
    // Build .mo file
    $mo_data = pack('V', 0x950412de); // Magic number
    $mo_data .= pack('V', 0); // Version
    $mo_data .= pack('V', count($translations)); // Number of strings
    $mo_data .= pack('V', 28); // Offset of key table
    $mo_data .= pack('V', 28 + (count($translations) * 8)); // Offset of value table
    $mo_data .= pack('V', 0); // Hash table size
    $mo_data .= pack('V', 0); // Hash table offset
    
    // Key index
    foreach ($key_offsets as $offset_info) {
        $mo_data .= pack('V', $offset_info['length']);
        $mo_data .= pack('V', $offset_info['offset']);
    }
    
    // Value index
    foreach ($value_offsets as $offset_info) {
        $mo_data .= pack('V', $offset_info['length']);
        $mo_data .= pack('V', $offset_info['offset']);
    }
    
    // Keys
    foreach ($keys as $key) {
        $mo_data .= $key . "\0";
    }
    
    // Values
    foreach ($values as $value) {
        $mo_data .= $value . "\0";
    }
    
    file_put_contents($mo_file, $mo_data);
}

function wpresidence_ptc_page_translator() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'post-types';
    $current_locale = get_locale();
    $saved_count = isset($_GET['count']) ? intval($_GET['count']) : 0;

    settings_errors( 'wpresidence_ptc' );

    echo '<div class="wrap wpresidence2025-content-wrapper ">';
    echo '<div class="wpresidence2025-content-wrapper-header"> ' . esc_html__( 'WPResidence Translator', 'wpresidence-core' ) . '</div>';
    echo '<hr>';
    echo '<div class="wpresidence2025-content-wrapper-inside-box"> ';

        echo '<div class="wpresidence2025-content-container">';

            echo '<div class="wpresidence-nav-tab-wrapper nav-tab-wrapper">';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=post-types' ) . '" class="nav-tab ' . ( $active_tab === 'post-types' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Post Types', 'wpresidence-core' ) . '</a>';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=taxonomies' ) . '" class="nav-tab ' . ( $active_tab === 'taxonomies' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Taxonomies', 'wpresidence-core' ) . '</a>';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=plugins' ) . '" class="nav-tab ' . ( $active_tab === 'plugins' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Plugins', 'wpresidence-core' ) . '</a>';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=feedback' ) . '" class="nav-tab ' . ( $active_tab === 'feedback' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Feedback', 'wpresidence-core' ) . '</a>';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=import-locations' ) . '" class="nav-tab ' . ( $active_tab === 'import-locations' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Import Locations', 'wpresidence-core' ) . '</a>';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=white-label' ) . '" class="nav-tab ' . ( $active_tab === 'white-label' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'White Label', 'wpresidence-core' ) . '</a>';
            // echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control-license&tab=license' ) . '" class="nav-tab ' . ( $active_tab === 'license' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'WpResidence License', 'wpresidence-core' ) . '</a>';
            echo '</div>';
?>
        <div class="wrap">
            <h1>Custom Translation Override</h1>
            <p>Create custom translations that override the default plugin translations. Your custom translations are saved separately and take priority over the original files.</p>
            
            <?php if (isset($_GET['saved'])): ?>
                <div class="notice notice-success">
                    <p><strong>Success!</strong> Saved <?php echo $saved_count; ?> custom translations. They will be loaded automatically.</p>
                </div>
            <?php endif; ?>
            
            <div class="translation-controls" style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                <h3>Load Translations</h3>
                <table class="form-table">
                    <tr>
                        <th><label for="text_domain">Text Domain:</label></th>
                        <td>
                            <input type="text" id="text_domain" value="wpresidence-core" />
                            <p class="description">The text domain of the plugin you want to customize</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="locale">Target Locale:</label></th>
                        <td>
                            <input type="text" id="locale" value="<?php echo esc_attr($current_locale); ?>" />
                            <p class="description">Locale code (e.g., en_US, fr_FR, de_DE)</p>
                        </td>
                    </tr>
                </table>
                <button type="button" id="load-translations" class="button button-primary">Load Existing Translations</button>
                <span id="load-status" style="margin-left: 10px;"></span>
            </div>
            
            <div id="translations-info" style="display: none; margin: 20px 0; padding: 10px; background: #e7f3ff; border-left: 4px solid #0073aa;">
                <p><strong>Custom translations directory:</strong> <code id="custom-dir-path"></code></p>
                <p><strong>Original file:</strong> <span id="original-file"></span></p>
            </div>
            
            <div id="translations-container" style="display: none;">
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('save_custom_translations_nonce'); ?>
                    <input type="hidden" name="action" value="save_custom_translations" />
                    <input type="hidden" name="locale" id="form_locale" value="<?php echo esc_attr($current_locale); ?>" />
                    <input type="hidden" name="text_domain" id="form_text_domain" value="wpresidence-core" />

                    <div style="margin: 20px 0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3>Translation Strings: <span id="strings-count">0</span></h3>
                            <p>Only fill in the strings you want to customize. Empty fields will use the default translation.</p>
                        </div>
                        <div>
                            <label><input type="checkbox" id="show-only-custom"> Show only customized strings</label>
                            <button type="submit" class="button button-primary" style="margin-left: 15px;">Save Custom Translations</button>
                        </div>
                    </div>
                    
                    <div id="strings-list"></div>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var allStrings = [];
            
            $('#load-translations').click(function() {
                var $button = $(this);
                var $status = $('#load-status');
                
                $button.prop('disabled', true);
                $status.html('<span class="spinner is-active" style="float: none; margin: 0;"></span> Loading...');
                
                $.post(ajaxurl, {
                    action: 'load_mo_strings',
                    locale: $('#locale').val(),
                    text_domain: $('#text_domain').val(),
                    nonce: '<?php echo wp_create_nonce('custom_translation_nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        allStrings = response.data.strings;
                        displayStrings(allStrings);
                        $('#strings-count').text(response.data.count);
                        $('#custom-dir-path').text(response.data.custom_dir);
                        $('#original-file').text(response.data.original_file);
                        $('#translations-container, #translations-info').show();
                        $('#form_locale').val($('#locale').val());
                        $('#form_text_domain').val($('#text_domain').val());
                        $status.html('<span style="color: green;">✓ Loaded ' + response.data.count + ' strings</span>');
                    } else {
                        $status.html('<span style="color: red;">✗ Error loading translations</span>');
                    }
                    $button.prop('disabled', false);
                });
            });
            
            $('#show-only-custom').change(function() {
                var showOnlyCustom = $(this).is(':checked');
                var filteredStrings = showOnlyCustom ? 
                    allStrings.filter(function(string) { return string.has_custom; }) : 
                    allStrings;
                displayStrings(filteredStrings);
            });
            
            function displayStrings(strings) {
                var html = '<table class="wp-list-table widefat striped"><thead><tr>';
                html += '<th style="width: 30%;">Original String</th>';
                html += '<th style="width: 30%;">Default Translation</th>';
                html += '<th style="width: 35%;">Your Custom Translation</th>';
                html += '<th style="width: 5%;">Status</th>';
                html += '</tr></thead><tbody>';
                
                strings.forEach(function(string, index) {
                    var statusClass = string.has_custom ? 'custom-override' : 'default-only';
                    var statusText = string.has_custom ? 'Custom' : 'Default';
                    
                    html += '<tr class="' + statusClass + '">';
                    html += '<td><strong>' + escapeHtml(string.original) + '</strong></td>';
                    html += '<td>' + escapeHtml(string.default_translation) + '</td>';
                    html += '<td><textarea name="custom_translations[' + escapeHtml(string.original) + ']" rows="2" cols="40" placeholder="Enter custom translation...">' + escapeHtml(string.custom_translation) + '</textarea></td>';
                    html += '<td><span class="status-badge status-' + statusClass + '">' + statusText + '</span></td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                $('#strings-list').html(html);
            }
            
            function escapeHtml(text) {
                var div = document.createElement('div');
                div.textContent = text || '';
                return div.innerHTML;
            }
        });
        </script>
        
        <style>
        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-custom-override {
            background: #d4edda;
            color: #155724;
        }
        .status-default-only {
            background: #f8f9fa;
            color: #6c757d;
        }
        .custom-override {
            background-color: #f8fff8;
        }
        .wp-list-table textarea {
            width: 100%;
            min-height: 50px;
            font-family: inherit;
        }
        .wp-list-table th {
            font-weight: 600;
        }
        </style>
<?php

        echo '</div>';
    echo '</div>';
    echo '</div>';
}