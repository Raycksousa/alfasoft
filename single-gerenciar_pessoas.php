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

                        <p>Parentesco:</p>
                        <ul>
                            <?php foreach ($parentesco as $parentesco_option) : ?>
                                <li>
                                    <?php echo esc_html($parentesco_option); ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="parentesco_a_excluir" value="<?php echo esc_attr($parentesco_option); ?>">
                                        <input type="submit" name="excluir_parentesco" value="Excluir">
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Adicione o formulário aqui -->
                        <form method="post" action="">
                            <label for="parentesco">Parentesco:</label>
                            <select name="parentesco" id="parentesco">
                                <option value="pai">Pai</option>
                                <option value="mae">Mãe</option>
                                <option value="filho">Filho</option>
                                <!-- Adicione mais opções de parentesco conforme necessário -->
                            </select>

                            <label for="pessoa_existente">Escolha uma pessoa existente:</label>
                            <select name="pessoa_existente" id="pessoa_existente">
                                <?php
                                // Consulta para obter todas as postagens que possuem o campo 'nome' preenchido
                                $args = array(
                                    'post_type' => 'gerenciar_pessoas', // Substitua 'sua_post_type' pelo tipo de postagem relevante
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'nome', // Substitua 'nome' pelo nome do seu campo ACF
                                            'compare' => 'EXISTS', // Verifique se o campo existe
                                        ),
                                    ),
                                );

                                $query = new WP_Query($args);

                                if ($query->have_posts()) {
                                    while ($query->have_posts()) : $query->the_post();
                                        $pessoa_id = get_the_ID();
                                        $pessoa_nome = get_field('nome'); // Substitua 'nome' pelo nome do seu campo ACF
                                        echo "<option value='$pessoa_id'>$pessoa_nome</option>";
                                    endwhile;
                                    wp_reset_postdata();
                                }
                                ?>
                            </select>

                            <input type="submit" name="adicionar_parentesco" value="Adicionar Parentesco">
                        </form>
                    </div><!-- .entry-content -->

                    <?php
                    // Processamento do formulário
                    if (isset($_POST['adicionar_parentesco'])) {
                        $parentesco_selecionado = sanitize_text_field($_POST['parentesco']);
                        $pessoa_existente_id = intval($_POST['pessoa_existente']);

                        // Verifique se a pessoa existe no sistema (você pode adicionar uma verificação adicional)

                        // Obtenha o ID da pessoa atual
                        $pessoa_id = get_the_ID();

                        // Obtenha os parentescos existentes (caso já haja algum)
                        $parentescos_existentes = get_field('parentesco', $pessoa_id);

                        // Adicione o novo parentesco à matriz de parentescos existentes
                        $parentescos_existentes[] = $parentesco_selecionado;

                        // Atualize o campo 'parentesco' da pessoa com os novos valores
                        update_field('parentesco', $parentescos_existentes, $pessoa_id);

                        // Redirecione de volta para a página de dados da pessoa após adicionar o parentesco
                        wp_redirect(get_permalink($pessoa_id));
                        exit;
                    }

                    // Processamento da exclusão de parentesco
                    if (isset($_POST['excluir_parentesco'])) {
                        $parentesco_a_excluir = sanitize_text_field($_POST['parentesco_a_excluir']);

                        // Obtenha o ID da pessoa atual
                        $pessoa_id = get_the_ID();

                        // Obtenha os parentescos existentes
                        $parentescos_existentes = get_field('parentesco', $pessoa_id);

                        // Encontre a chave do parentesco a ser excluído
                        $chave_excluir = array_search($parentesco_a_excluir, $parentescos_existentes);

                        // Remova o parentesco da matriz
                        if ($chave_excluir !== false) {
                            unset($parentescos_existentes[$chave_excluir]);
                        }

                        // Atualize o campo 'parentesco' da pessoa com os valores atualizados
                        update_field('parentesco', array_values($parentescos_existentes), $pessoa_id);

                        // Redirecione de volta para a página de dados da pessoa após excluir o parentesco
                        wp_redirect(get_permalink($pessoa_id));
                        exit;
                    }

                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) {
                        comments_template();
                    }
                    ?>
                </article><!-- #post-<?php the_ID(); ?> -->

            </main><!-- #main -->
        </div><!-- #primary -->

        <style>
            /* Coloque aqui o seu CSS personalizado para esta postagem */
            /* Exemplo: */
            .entry-content {
                background-color: #f9f9f9;
                padding: 20px;
                border-radius: 5px;
                margin-top: 20px;
            }

            .entry-content p {
                margin: 0;
            }

            .entry-content ul {
                list-style: none;
                padding: 0;
            }

            .entry-content ul li {
                margin-bottom: 5px;
                font-weight: bold;
                color: #0073e6;
            }

            .entry-content ul li form {
                display: inline;
            }
        </style>
        <?php
    }
endwhile;

?>
