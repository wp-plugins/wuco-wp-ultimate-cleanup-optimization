<?php $version = get_option('wuco_version'); ?>

<div class="wrap about-wrap wuco-wrap wuco-about-wrap">
    <header>
        <h1><?php printf(__('Welcome to WUCO %s', 'wuco'), $version); ?></h1>
        <div class="about-text"><?php _e('WUCO stands for <strong>WordPress Ultimate Cleanup & Optimization</strong> and is a powerful yet easy to use plugin that is designed to help you keep your WordPress site clean and shiny.', 'wuco'); ?></div>
        <p>Maybe add some buttons here</p>
        <div class="wp-badge wuco-badge"><?php printf(__('Version %s', 'wuco'), $version); ?></div>
    </header>

<!--    <h2 class="nav-tab-wrapper wuco-nav-tab-wrapper"></h2>-->
    <hr />

    <div class="changelog headline-feature">
        <h2>WordPress Ultimate Cleanup & Optimization</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam ut mi lorem. Nulla non leo non turpis dignissim congue nec vitae mi. Suspendisse magna est, lacinia quis pretium pharetra, tempus sit amet magna. Ut eu elit quis risus lobortis commodo. Morbi ipsum diam, imperdiet sed finibus vel, commodo id ante. Integer eros lectus, auctor ut efficitur varius, maximus vel orci. Pellentesque vitae ex in felis tempor interdum et a dui. Mauris ultrices accumsan lectus, sed efficitur libero egestas congue. </p>
    </div>

    <hr />

    <div class="changelog feature-list">
        <h2><?php _e('Key Feaures', 'wuco'); ?></h2>

        <div class="feature-section col three-col">

            <div>
                <h4>Lorem &amp; Ipsum</h4>
                <p>Etiam dolor magna, mattis fringilla dignissim sed, pretium at purus. Maecenas condimentum gravida dolor, cursus scelerisque lacus interdum vel.</p>
            </div>

            <div>
                <h4>Dolor Sit Amet</h4>
                <p>Duis ac magna a ipsum facilisis bibendum. Maecenas at metus non sapien pretium semper. Sed in nisl augue. Duis sollicitudin maximus ex id pretium.</p>
            </div>

            <div class="last-feature">
                <h4>Nulla non leo</h4>
                <p>Quisque feugiat sagittis iaculis. Maecenas pharetra eleifend massa a tristique. Proin luctus elit felis, vel aliquet est imperdiet at.</p>
            </div>

            <div>
                <h4>Lorem &amp; Ipsum</h4>
                <p>Etiam dolor magna, mattis fringilla dignissim sed, pretium at purus. Maecenas condimentum gravida dolor, cursus scelerisque lacus interdum vel.</p>
            </div>

            <div>
                <h4>Dolor Sit Amet</h4>
                <p>Duis ac magna a ipsum facilisis bibendum. Maecenas at metus non sapien pretium semper. Sed in nisl augue. Duis sollicitudin maximus ex id pretium.</p>
            </div>

            <div class="last-feature">
                <h4>Nulla non leo</h4>
                <p>Quisque feugiat sagittis iaculis. Maecenas pharetra eleifend massa a tristique. Proin luctus elit felis, vel aliquet est imperdiet at.</p>
            </div>
        </div>

    </div>

    <hr />

    <div class="return-to-dashboard">
        <a href="<?php echo admin_url('admin.php?page=wuco'); ?>"><?php _e('Go to Plugin Dashboard', 'wuco'); ?> &rarr;</a>
    </div>
</div>