<div class="<?php if (is_front_page()): ?>home-menu-wrapper<?php else: ?>secondary-menu-wrapper<?php endif; ?>">
    <?php
        wp_nav_menu(
            array(
                'container_class' => 'pull-right',
                'menu' => 'secondary_page_top_nav',
                'menu_class'      => 'secondary-page-top-nav',
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',
                'before'          => '',
                'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>'
            )
        );
    ?>
</div>