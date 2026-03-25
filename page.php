<?php

get_header(); ?>

<main class="site-main">
    <div class="page-container">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <!-- Titre de la page -->
                <h1 class="page-title"><?php the_title(); ?></h1>

                <!-- Image mise en avant (si elle existe) -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="page-featured-image">
                        <?php the_post_thumbnail( 'full' ); ?>
                    </div>
                <?php endif; ?>

                <!-- Contenu de la page -->
                <div class="page-content">
                    <?php the_content(); ?>
                </div>

                <!-- Pagination du contenu (si la page utilise <!--nextpage-->) -->
                <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . __( 'Pages :', 'motaphoto' ),
                    'after'  => '</div>',
                ) );
                ?>

            </article>

            <!-- Commentaires (si activés sur la page) -->
            <?php if ( comments_open() || get_comments_number() ) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>

        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>
