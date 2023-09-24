<?php
get_header();

while (have_posts()) : the_post();
    $nome = get_field('nome'); // Use get_field do ACF
    $idade = get_field('idade'); // Use get_field do ACF
    $parentesco = get_field('parentesco'); // Use get_field do ACF para obter os valores selecionados

    // Verifique se o campo 'Parentesco' não está vazio e é um array
    if (!empty($parentesco) && is_array($parentesco)) {
        ?>
        <div id="primary" class="content-area">
            <main id="main" class="site-main">

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <p>Nome: <?php echo esc_html($nome); ?></p>
                        <p>Idade: <?php echo esc_html($idade); ?></p>

                        <p>Parentesco 2:</p>
                        <ul>
                            <?php foreach ($parentesco as $parentesco_option) : ?>
                                <li><?php echo esc_html($parentesco_option); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div><!-- .entry-content -->

                    <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) {
                        comments_template();
                    }
                    ?>
                </article><!-- #post-<?php the_ID(); ?> -->

            </main><!-- #main -->
        </div><!-- #primary -->
        <?php
    }
endwhile;

get_footer();
