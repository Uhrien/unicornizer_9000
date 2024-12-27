<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unicornizor_Admin {

    private $manager;

    public function __construct($manager) {
        $this->manager = $manager;
        add_action('admin_menu', array($this, 'add_admin_pages'));
    }

    public function add_admin_pages() {
        add_menu_page(
            'Unicornizor 9000',
            'Unicornizor 9000',
            'manage_options',
            'unicornizor-9000',
            array($this, 'render_admin_page'),
            'dashicons-art',
            80
        );
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $this->handle_actions();

        // Routing semplice
        if(isset($_GET['edit'])) {
            $this->render_edit_block_form( intval($_GET['edit']) );
        } else if(isset($_GET['new'])) {
            $this->render_edit_block_form(null);
        } else {
            $this->render_blocks_list();
        }
    }

    private function render_blocks_list() {
        $blocks = $this->manager->get_blocks();
        ?>
        <div class="wrap">
            <h1>Unicornizor 9000 - I miei blocchi</h1>
            <p>Crea nuovi "blocchi" di elementi e ottieni uno shortcode unico per ciascuno.</p>
            <a href="?page=unicornizor-9000&new=1" class="button button-primary">Crea Nuovo Blocco</a>

            <?php if(!empty($blocks)): ?>
                <table class="widefat striped" style="margin-top:20px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Blocco</th>
                            <th>Shortcode</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($blocks as $block_id => $block_data): ?>
                            <tr>
                                <td><?php echo esc_html($block_id); ?></td>
                                <td><?php echo isset($block_data['block_name']) ? esc_html($block_data['block_name']) : ''; ?></td>
                                <td>[unicornizor id="<?php echo esc_attr($block_id); ?>"]</td>
                                <td>
                                    <a class="button" href="?page=unicornizor-9000&edit=<?php echo $block_id; ?>">Modifica</a>
                                    <a class="button button-danger"
                                       href="?page=unicornizor-9000&delete=<?php echo $block_id; ?>"
                                       onclick="return confirm('Sicuro di voler eliminare questo blocco?');">Elimina</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nessun blocco definito.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_edit_block_form($block_id) {
        $editing = ($block_id !== null);
        $current_block = array('block_name'=>'','items'=>array());
        if($editing) {
            $tmp = $this->manager->get_block($block_id);
            if($tmp) {
                $current_block = $tmp;
            }
        }

        $items_json = json_encode($current_block['items']);
        ?>
        <div class="wrap unicornizor-admin-page">
            <h1><?php echo ($editing) ? 'Modifica Blocco' : 'Nuovo Blocco'; ?></h1>

            <form method="post" id="unicornizor-block-form">
                <?php wp_nonce_field('unicornizor_save_block','unicornizor_nonce'); ?>
                <?php if($editing): ?>
                    <input type="hidden" name="block_id" value="<?php echo $block_id; ?>" />
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th><label for="block_name">Nome del Blocco</label></th>
                        <td>
                            <input type="text" name="block_name" id="block_name"
                                   value="<?php echo esc_attr($current_block['block_name']); ?>" />
                        </td>
                    </tr>
                </table>

                <div class="unicornizor-config-wrapper" style="display:flex; gap:20px; margin-top:20px;">
                    <div class="unicornizor-preview-area" style="
                        position: relative;
                        border: 2px dashed #ccc;
                        width: 500px; height: 500px;
                        overflow: hidden;
                    ">
                        <!-- JS popola l'anteprima -->
                    </div>

                    <div class="unicornizor-sidebar-controls">
                        <h3>Elementi</h3>
                        <button type="button" class="button" id="unicornizor-add-item">Aggiungi Elemento</button>
                        <!-- Griglia 3xN -->
                        <div id="unicornizor-items-list" class="unicornizor-items-grid" style="margin-top:10px;"></div>
                    </div>
                </div>

                <input type="hidden" name="items_data" id="items_data" value="" />

                <p class="submit" style="margin-top:20px;">
                    <input type="submit" class="button button-primary"
                           value="<?php echo $editing ? 'Aggiorna Blocco' : 'Crea Blocco'; ?>" />
                </p>
            </form>

            <script>
              var unicornizorItems = <?php echo $items_json; ?> || [];
            </script>
        </div>
        <?php
    }

    private function handle_actions() {
        // Eliminazione
        if (isset($_GET['delete'])) {
            $block_id = intval($_GET['delete']);
            $this->manager->delete_block($block_id);
            ?>
            <div class="notice notice-success is-dismissible">
                <p>Blocco eliminato con successo.</p>
            </div>
            <?php
        }

        // Salvataggio
        if (!empty($_POST) && isset($_POST['block_name']) && check_admin_referer('unicornizor_save_block','unicornizor_nonce')) {
            $block_name = sanitize_text_field($_POST['block_name']);
            $items_data = isset($_POST['items_data']) ? stripslashes($_POST['items_data']) : '[]';
            $items      = json_decode($items_data, true);
            if(!is_array($items)) {
                $items = array();
            }

            $block_data = array(
                'block_name' => $block_name,
                'items'      => $items
            );

            if(isset($_POST['block_id']) && !empty($_POST['block_id'])) {
                $block_id = intval($_POST['block_id']);
                $this->manager->save_block($block_id, $block_data);
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>Blocco aggiornato con successo!</p>
                </div>
                <?php
            } else {
                $new_id = $this->manager->create_block($block_data);
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>Nuovo blocco creato (ID: <?php echo $new_id; ?>).</p>
                </div>
                <?php
            }
        }
    }
}
