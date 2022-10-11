<div class="wrap about__container">
    <div class="about__plugin__header">
        <div class="about__header-banner">
            <img src="<?php echo esc_url( plugins_url( '/assets/img/next-story.jpg', U_NEXT_STORY_PLUGIN_FILE ) ) ?>"
                 alt="uCAT - Next Story - 2.0.1">
        </div>
        <nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
            <a href="#about" class="nav-tab">About</a>
            <a href="#changelog" class="nav-tab">Changelog</a>
            <a href="#sponsoring" class="nav-tab">Sponsoring</a>
            <a href="#comingsoon" class="nav-tab">Coming Soon</a>
        </nav>
    </div>
	<?php
	$plugin_data = get_plugin_data( U_NEXT_STORY_PLUGIN_FILE );
	?>
    <div class="about__section" id="about">
        <div class="column">
            <h2>Next Story - arrow post navigation</h2>
            <p>
	            <?php echo $plugin_data['Description']; ?>
            </p>
            <p>
                <strong>Version:</strong>
	            <?php echo $plugin_data['Version']; ?>
            </p>
            <p>
                <strong>Requires WP:</strong>
		        <?php echo $plugin_data['RequiresWP']; ?>
            </p>
            <p>
                <strong>Requires PHP:</strong>
		        <?php echo $plugin_data['RequiresPHP']; ?>
            </p>
            <p>
                <strong>Project URL:</strong>
                <a href="<?php echo esc_url( $plugin_data['PluginURI'] ); ?>" target="_blank"><?php echo esc_url( $plugin_data['PluginURI'] ); ?></a>
            </p>

            <h4>Features of this Post Navigator:</h4>

            <ul>
                <li>SEO Optimized</li>
                <li>CSS3 Transitions</li>
                <li>Unlimited Color Scheme</li>
                <li>Effective Plugin to increase SERP</li>
                <li>No Coding knowledge Needed, Just plug& play.</li>
                <li>Feature where you can choose menu for displaying arrow navigation.</li>
                <li>Rules for displaying navigation links.</li>
                <li>Limit next/previous links to certain category</li>
                <li>Ability in admin area to modify %, that user should scroll to, to reveal the next story links.</li>
            </ul>
        </div>
        <div class="column" id="changelog">
            <h2><?php _e( 'Changelog', 'ucat-next-story' ); ?></h2>

            <h4>11/10/2022 - VERSION 2.0.1</h4>
            <ul>
                <li>WordPress 6.0.2 compatibility.</li>
                <li>Update Select2 version</li>
                <li>Settings Next/Previous links directly on page edit page</li>
                <li>Random Post</li>
            </ul>

            <h4>06/05/2022 - VERSION 2.0.0</h4>
            <ul>
                <li>WordPress 5.9.3 compatibility.</li>
                <li>Updated settings page</li>
                <li>Ability in admin area to modify %, that user should scroll to, to reveal the next story links.</li>
                <li>Limit next/previous links to certain category</li>
                <li>Added rules for displaying navigation links</li>
            </ul>

            <h4>16/02/2016 – VERSION 1.1.1</h4>
            <p>Feature added where you can choose menu for displaying arrow navigation.</p>

            <h4>04/02/2016 – VERSION 1.0.0</h4>
            <p>Plugin release. Operate all the basic functions.</p>

        </div>

        <div class="column" id="sponsoring">
            <h2><?php _e( 'Sponsoring', 'ucat-next-story' ); ?> <a href="https://www.patreon.com/olenkadark" target="_blank" class="button">Donate</a></h2>
            <p>If you or your company are using any of my projects, consider supporting me so I can continue my open source work.</p>
            <p>Let me try to sell you the idea of why <a href="https://www.patreon.com/olenkadark" target="_blank">sponsoring</a> would be a good idea.</p>
            <p>The reason why I created the Sponsor profile was that maintaining an Open Source project with as many scenarios and possibilities as Next Story is incredibly time-consuming.</p>
            <p>I still love this project very much and getting some sponsors would help me take the project to the next level, a couple of the goals I have if I can get some sponsors are:</p>
        </div>

        <div class="column" id="comingsoon">
            <h2><?php _e( 'Coming soon', 'ucat-next-story' ); ?></h2>
            <ul>
                <li>Display permanent image</li>
                <li>Navigate on subpages under the main page</li>
            </ul>

            <p>If you have any suggestions I would love to <a href="https://wordpress.org/support/plugin/ucat-next-story/" target="_blank">hear</a> them.</p>
        </div>
    </div>
</div>


<style>
    .about__header-banner {
        background: #646de7;
        text-align: center;
        padding: 20px;
    }
</style>
