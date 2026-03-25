<?php

get_header(); ?>

<main class="site-main">
    <div class="single-container">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <!-- Titre de l'article -->
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <!-- Métadonnées : date, auteur, catégories, tags -->
                <div class="entry-meta">
                    <span class="entry-date">
                        <time datetime="<?php echo get_the_date( 'c' ); ?>">
                            <?php echo get_the_date( 'F j, Y' ); ?>
                        </time>
                    </span>
                    <span class="entry-author">
                        <?php the_author(); ?>
                    </span>
                    <?php if ( has_category() ) : ?>
                        <span class="entry-categories">
                            <?php the_category( ', ' ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( has_tag() ) : ?>
                        <span class="entry-tags">
                            <?php the_tags( '', ', ' ); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Image mise en avant (si elle existe) -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-featured-image">
                        <?php the_post_thumbnail( 'full' ); ?>
                    </div>
                <?php endif; ?>

                <!-- Contenu de l'article -->
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- Pagination du contenu (si l'article utilise <!--nextpage-->) -->
                <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . __( 'Pages :', 'motaphoto' ),
                    'after'  => '</div>',
                ) );
                ?>

            </article>

            <!-- Navigation précédent / suivant (articles de blog uniquement) -->
            <nav class="post-navigation">
                <?php
                the_post_navigation( array(
                    'prev_text' => '← %title',
                    'next_text' => '%title →',
                ) );
                ?>
            </nav>

            <!-- Commentaires -->
            <?php if ( comments_open() || get_comments_number() ) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>

        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>
