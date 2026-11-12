<?php

/**
 * Performance testing suite for WPresidence API caching system
 */

function wpestate_api_test_performance_comparison($post_ids) {
    $results = [
        'cached' => [],
        'non_cached' => [],
        'difference' => [],
        'summary' => []
    ];
    
    print '<pre>Starting performance test for ' . count($post_ids) . ' posts</pre>';
    
    // First pass: Test non-cached version and build cache
    foreach ($post_ids as $post_id) {
        print "<pre>Testing non-cached access for post ID: $post_id</pre>";
        
        // Clear any existing cache first
        wpestate_api_clear_post_cache($post_id, 'estate_property');
        
        // Test non-cached version
        $start_time = microtime(true);
        $non_cached_data = wpestate_api_get_non_cached_property_data($post_id);
        $end_time = microtime(true);
        $non_cached_time = ($end_time - $start_time) * 1000;
        $results['non_cached'][$post_id] = $non_cached_time;
        
        print "<pre>Non-cached access time for post $post_id: $non_cached_time ms</pre>";
        
        // Build cache for next test
        wpestate_api_set_cache_post_data($post_id, 'estate_property');
    }
    
    // Add a small delay to ensure cache is fully written
    print '<pre>Waiting for cache to settle...</pre>';
    sleep(1);
    
    // Second pass: Test cached version
    foreach ($post_ids as $post_id) {
        print "<pre>Testing cached access for post ID: $post_id</pre>";
        
        $start_time = microtime(true);
        $cached_data = wpestate_api_get_cached_post_data($post_id, 'estate_property');
        $end_time = microtime(true);
        $cached_time = ($end_time - $start_time) * 1000;
        $results['cached'][$post_id] = $cached_time;
        
        $results['difference'][$post_id] = $results['non_cached'][$post_id] - $cached_time;
        
        print "<pre>Cached access time for post $post_id: $cached_time ms</pre>";
        print "<pre>Performance difference for post $post_id: " . $results['difference'][$post_id] . " ms</pre>";
    }
    
    // Calculate summary statistics
    $results['summary'] = wpestate_api_calculate_summary_statistics($results);
    
    print '<pre>Performance test completed</pre>';
    
    return $results;
}

function wpestate_api_get_non_cached_property_data($post_id) {
    $post_type_data = wpestate_api_get_cached_post_types_and_data();
    $property_data = $post_type_data['estate_property'];
    
    $data = [
        'ID' => $post_id,
        'title' => get_the_title($post_id),
        'description' => get_the_content($post_id),
        'excerpt' => get_the_excerpt($post_id),
        'media' => wpestate_generate_array_image_urls(wpestate_generate_property_slider_image_ids($post_id, true)),
        'terms' => [],
        'meta' => [],
        'custom_meta' => []
    ];
    
    // Get meta data
    foreach ($property_data['meta'] as $meta_key) {
        $data['meta'][$meta_key] = get_post_meta($post_id, $meta_key, true);
    }
    
    // Get custom meta data
    foreach ($property_data['custom_meta'] as $meta_key) {
        $data['custom_meta'][$meta_key] = get_post_meta($post_id, $meta_key, true);
    }
    
    // Get taxonomy terms
    foreach ($property_data['taxonomies'] as $taxonomy) {
        $data['terms'][$taxonomy] = wpestate_api_get_simplified_terms($post_id, $taxonomy);
    }
    
    return $data;
}

function wpestate_api_calculate_summary_statistics($results) {
    $cached_times = $results['cached'];
    $non_cached_times = $results['non_cached'];
    $differences = $results['difference'];
    
    return [
        'cached' => [
            'average' => array_sum($cached_times) / count($cached_times),
            'min' => min($cached_times),
            'max' => max($cached_times),
            'total' => array_sum($cached_times)
        ],
        'non_cached' => [
            'average' => array_sum($non_cached_times) / count($non_cached_times),
            'min' => min($non_cached_times),
            'max' => max($non_cached_times),
            'total' => array_sum($non_cached_times)
        ],
        'improvement' => [
            'average' => array_sum($differences) / count($differences),
            'percentage' => (1 - (array_sum($cached_times) / array_sum($non_cached_times))) * 100
        ]
    ];
}

function wpestate_api_run_performance_test($number_of_posts = 10) {
    // Get random property post IDs
    $args = array(
        'post_type' => 'estate_property',
        'posts_per_page' => $number_of_posts,
        'fields' => 'ids',
    );
    $post_ids = get_posts($args);
    
    print '<pre>Starting performance test with ' . count($post_ids) . ' posts</pre>';
    
    // Run the test
    $results = wpestate_api_test_performance_comparison($post_ids);
    
    // Display results
    wpestate_api_display_test_results($results);
}

function wpestate_api_display_test_results($results) {
    ?>
    <div class="wrap wpestate-performance-results">
        <h2>Cache Performance Test Results</h2>
        
        <div class="wpestate-summary-section">
            <h3>Summary Statistics</h3>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Cached (ms)</th>
                        <th>Non-cached (ms)</th>
                        <th>Improvement</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Average Time</td>
                        <td><?php echo number_format($results['summary']['cached']['average'], 2); ?></td>
                        <td><?php echo number_format($results['summary']['non_cached']['average'], 2); ?></td>
                        <td><?php echo number_format($results['summary']['improvement']['percentage'], 1); ?>%</td>
                    </tr>
                    <tr>
                        <td>Min Time</td>
                        <td><?php echo number_format($results['summary']['cached']['min'], 2); ?></td>
                        <td><?php echo number_format($results['summary']['non_cached']['min'], 2); ?></td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Max Time</td>
                        <td><?php echo number_format($results['summary']['cached']['max'], 2); ?></td>
                        <td><?php echo number_format($results['summary']['non_cached']['max'], 2); ?></td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Total Time</td>
                        <td><?php echo number_format($results['summary']['cached']['total'], 2); ?></td>
                        <td><?php echo number_format($results['summary']['non_cached']['total'], 2); ?></td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="wpestate-detailed-section">
            <h3>Detailed Results</h3>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Post ID</th>
                        <th>Cached Time (ms)</th>
                        <th>Non-cached Time (ms)</th>
                        <th>Difference (ms)</th>
                        <th>Improvement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['cached'] as $post_id => $cached_time): ?>
                        <?php 
                        $difference = $results['difference'][$post_id];
                        $improvement = ($difference / $results['non_cached'][$post_id]) * 100;
                        $color = $improvement > 0 ? '#28a745' : '#dc3545';
                        ?>
                        <tr>
                            <td><?php echo esc_html($post_id); ?></td>
                            <td><?php echo number_format($cached_time, 2); ?></td>
                            <td><?php echo number_format($results['non_cached'][$post_id], 2); ?></td>
                            <td><?php echo number_format($difference, 2); ?></td>
                            <td style="color: <?php echo $color; ?>">
                                <?php echo number_format($improvement, 1); ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <style>
            .wpestate-performance-results {
                margin: 20px 0;
                max-width: 1200px;
            }
            .wpestate-summary-section,
            .wpestate-detailed-section {
                margin: 20px 0;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .widefat td, .widefat th {
                padding: 12px 10px;
            }
        </style>
    </div>
    <?php
}

// Optional: Add this to test a specific set of post IDs
function wpestate_api_run_specific_performance_test($post_ids) {
    print '<pre>Starting performance test with specific post IDs: ' . implode(', ', $post_ids) . '</pre>';
    $results = wpestate_api_test_performance_comparison($post_ids);
    wpestate_api_display_test_results($results);
}