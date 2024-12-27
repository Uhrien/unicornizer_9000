<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unicornizor_Shortcodes {

    private $manager;

    public function __construct($manager) {
        $this->manager = $manager;
        add_shortcode('unicornizor', array($this, 'render_block'));
    }

    public function render_block($atts) {
        $a = shortcode_atts(array('id' => ''), $atts);
        if(!$a['id']) return '';

        $block_id = $a['id'];
        $block_data = $this->manager->get_block($block_id);
        if(!$block_data) {
            return '<!-- Blocco non trovato -->';
        }

        $items = isset($block_data['items']) ? $block_data['items'] : array();

        ob_start(); ?>
        <div class="unicornizor-container" style="display:inline-block; position:relative; background:transparent; overflow:visible;">
            <?php foreach($items as $idx => $item):
                $icon = $this->map_icon($item['type']);
                $size = isset($item['size']) ? intval($item['size']) : 50;
                $posX = isset($item['posX']) ? intval($item['posX']) : 10;
                $posY = isset($item['posY']) ? intval($item['posY']) : 10;
                $anim = isset($item['animation']) ? $item['animation'] : '';
            ?>
            <span class="unicornizor-item <?php echo $anim ? 'anim-' . esc_attr($anim) : ''; ?>"
                  style="position:absolute; left:<?php echo $posX; ?>px; top:<?php echo $posY; ?>px; font-size:<?php echo $size; ?>px;">
                <?php echo esc_html($icon); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function map_icon($type) {
        switch($type) {
            case 'hearts':   return '❤️';
            case 'unicorn':  return '🦄';
            case 'rainbow':  return '🌈';
            case 'cupcake':  return '🧁';
            case 'cloud':    return '☁️';
            case 'flower':   return '🌸';
            case 'star2':    return '🌟';
            case 'cat':      return '🐱';  // Aggiunta di esempio
            case 'ghost':    return '👻';  // Aggiunta di esempio
            case 'music':    return '🎵';  // Aggiunta di esempio
            default:         return '⭐';  // default
        }
    }
}
