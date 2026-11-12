<div class="splash_page_widgets_wrapper">
    <?php if (is_active_sidebar('splash-page_bottom-left-widget-area')): ?>
        <div class="splash-left-widget">
            <ul class="xoxo">
                <?php dynamic_sidebar('splash-page_bottom-left-widget-area'); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (is_active_sidebar('splash-page_bottom-right-widget-area')): ?>
        <div class="splash-right-widget">
            <ul class="xoxo">
                <?php dynamic_sidebar('splash-page_bottom-right-widget-area'); ?>
            </ul>
        </div>
    <?php endif; ?>
</div>