<?php
/** MILLDONE
 * Template for displaying single blog posts
 *
 * This template is responsible for displaying individual blog posts in the WPEstate theme.
 * It includes various components such as breadcrumbs, post content, meta information,
 * social sharing, comments, and related posts.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0
 * @version 2.0
 */

// Load the header template
get_header();

$phone = get_post_meta($post->ID, 'phone', true);
$email = get_post_meta($post->ID, 'email', true);
$linkedin = get_post_meta($post->ID, 'linkedin', true);
$website = get_post_meta($post->ID, 'website', true);
$facebook = get_post_meta($post->ID, 'facebook', true);
$instagram = get_post_meta($post->ID, 'instagram', true);
$youtube = get_post_meta($post->ID, 'youtube', true);

$projects = get_post_meta($post->ID, 'portfolio_projects', true);
$portfolio_description = get_post_meta($post->ID, 'portfolio_description', true);
$bio = get_post_meta($post->ID, 'bio', true);

// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {
    
    ?>
    


    <div class="agent-profile-container">
        <div class="agent-header">
            <div class="agent-header-left">
                <div class="re-profile-sec">
                <?php
                        $user_small_picture_id = get_the_author_meta('small_custom_picture', get_post_field('post_author', get_the_ID()));

                        // Set default profile picture if custom picture is not set
                        if (empty($user_small_picture_id)) {
                            $user_small_picture = [get_theme_file_uri('/img/default-user_1.png')];
                        } else {
                            $user_small_picture = wp_get_attachment_image_src($user_small_picture_id, 'agent_picture_thumb');
                        }

                        // Get profile image URL safely
                        $profile_image_url = is_array($user_small_picture) && !empty($user_small_picture) ? $user_small_picture[0] : '';
                    ?>

                    <div class="agent-image" style="background-image:url('<?php echo esc_url($profile_image_url); ?>')"></div>

                </div>
            <div>    
                </div>
            </div>
            <div class="agent-header-right">
                <h3><a href="#"><?php echo get_the_title(); ?></a></h3>
                <div class="agent-socials">
                    <a href="<?php echo $facebook; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo $website; ?>" target="_blank"><i class="fas fa-globe"></i></a>
                    <a href="<?php echo $linkedin; ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                    <a href="<?php echo $youtube; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="<?php echo $instagram; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
                <div class="agent-contact">
                    <div class="contact-item"><i class="fas fa-phone"></i> <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></div>
                    <div class="contact-item"><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></div>
                    <div class="contact-item"><i class="fas fa-globe"></i> <a href="<?php echo $website; ?>" target="_blank"><?php echo $website; ?></a></div>
                </div>
                <h4 class="prof-bio">Professional Bio</h4>
                <?php echo $bio; ?>
                

                
            </div>
        </div>

        <div class="agent-info">
            

            <div class="agent-description">
                <h4>About Me</h4>
                <?php echo $portfolio_description; ?>
            </div>
        </div>

        <div class="agent-projects">
            <h3>Projects</h3>

            <div class="projects-grid">
                <!-- <?php foreach ($projects as $project) { ?>
                    <div class="project-card">
                        <a href="<?php echo $project['site']; ?>" target="_blank">
                            <img src="<?php echo $project['images'][0]; ?>" alt="<?php echo $project['title']; ?>" class="project-image">
                        </a>
                        <div class="project-details">
                            <h4><a href="<?php echo $project['site']; ?>" target="_blank"><?php echo $project['title']; ?></a></h4>
                            <p>Website: <a href="<?php echo $project['site']; ?>" target="_blank"><?php echo $project['site']; ?></a></p>
                        </div>
                    </div>
                <?php } ?> -->

                <?php foreach ($projects as $project) { ?>
                    <div class="project-card">
                        <a href="<?php echo $project['images'][0]; ?>" data-fancybox="gallery-<?php echo md5($project['title']); ?>" data-caption="<?php echo $project['title']; ?>">
                            <img src="<?php echo $project['images'][0]; ?>" alt="<?php echo $project['title']; ?>" class="project-image">
                        </a>

                        <?php
                        // Show the rest of the images in the lightbox but hidden
                        for ($i = 1; $i < count($project['images']); $i++) {
                            echo '<a href="' . $project['images'][$i] . '" data-fancybox="gallery-' . md5($project['title']) . '" data-caption="' . $project['title'] . '" style="display:none;"></a>';
                        }
                        ?>

                        <div class="project-details">
                            <p>Site Title:<?php echo $project['title']; ?></a></p>
                            <p>Site Address <?php echo $project['site']; ?></a></p>
                        </div>
                    </div>
                <?php } ?>



                <!-- Fancybox CDN -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
                <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>





            </div>
        </div>
    </div>



    <style>
        .agent-profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .agent-header {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .agent-header-left {
            flex: 0 1 17%;
        }

        .agent-header-right {
            flex-grow: 1;
            flex-basis: 80%;
        }

        .agent-header-right h3 {
            margin-bottom: 7px;
        }

        .agent-image {
            width: 150px;
            height: 150px;
            background-size: cover;
            background-position: center;
            border-radius: 100px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .agent-socials {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .agent-socials a {
            font-size: 1.2rem;
            color: #666;
            transition: color 0.3s;
        }

        .agent-socials a:hover {
            color: #0073e6;
        }

        h4.prof-bio {
            margin-bottom: 6px;
            margin-top: 23px;
        }

        .agent-info h3 {
            margin-bottom: 1rem;
            font-size: 1.8rem;
            color: #222;
        }

        .agent-contact {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .contact-item i {
            margin-right: 0.5rem;
            color: #0073e6;
        }

        .contact-item a {
            color: #333;
            text-decoration: none;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        .agent-description {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .agent-projects h3 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #0073e6;
            display: inline-block;
            padding-bottom: 0.3rem;
        }

        .projects-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    max-width: 900px;   /* adjust as needed */
    margin: 0 auto;     /* center the grid */
    padding: 0 1rem; 
}

        .project-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
        }

        .project-image {
    width: 100%;
    aspect-ratio: 4 / 3; /* Automatically sets height relative to width */
    object-fit: cover;
    display: block;
}

        .project-details {
            padding: 1rem;
        }

        .project-details h4 {
            margin: 0 0 0.5rem;
            font-size: 1.1rem;
        }

        .project-details a {
            color: #0073e6;
            text-decoration: none;
            word-break: break-word;
        }

        .project-details a:hover {
            text-decoration: underline;
        }

        .re-profile-sec {
            display: flex;
            flex-direction: column;
        }

        .agent-profile-container {
            padding: 0;
        }

        /* Responsive Touch */
        @media (max-width: 768px) {
            .agent-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .projects-grid {
        grid-template-columns: 1fr;
    }

            .agent-image {
                width: 120px;
                height: 120px;
            }
        }

    </style>


<?php
}

// Load footer template
get_footer();
?>