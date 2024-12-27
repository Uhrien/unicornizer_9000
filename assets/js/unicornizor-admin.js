jQuery(document).ready(function($){

    const $previewArea = $('.unicornizor-preview-area');
    const $itemsList   = $('#unicornizor-items-list');
    const $addItemBtn  = $('#unicornizor-add-item');
    const $itemsData   = $('#items_data');

    // items definito in <script> var unicornizorItems = ...
    let items = (typeof unicornizorItems !== 'undefined') ? unicornizorItems : [];

    function renderUI() {
        $itemsList.empty();
        $previewArea.empty();

        items.forEach((item, index) => {
            // Creiamo i controlli
            const $ctrl = createItemControls(item, index);
            $itemsList.append($ctrl);

            // Creiamo anteprima
            const $prev = createPreviewItem(item, index);
            $previewArea.append($prev);
        });

        // Aggiorniamo hidden field
        $itemsData.val(JSON.stringify(items));
    }

    /**
     * Crea la "card" di controllo, disposta in griglia 3 col.
     */
    function createItemControls(item, index) {
        const $c = $(`
        <div class="unicornizor-item-control unicornizor-card" style="padding:10px; border:2px solid #ddd; margin:5px; border-radius:8px; background: linear-gradient(135deg, #ffe1ff, #e1ffe8);">
            <p><strong>Elemento #${index+1}</strong></p>
            <label>Tipo:
                <select class="unicornizor-type-select">
                    <option value="stars">⭐ Stelline</option>
                    <option value="hearts">❤️ Cuoricini</option>
                    <option value="unicorn">🦄 Unicorni</option>
                    <option value="rainbow">🌈 Arcobaleni</option>
                    <option value="cupcake">🧁 Cupcake</option>
                    <option value="cloud">☁️ Nuvolette</option>
                    <option value="flower">🌸 Fiorellini</option>
                    <option value="star2">🌟 Stellina 2</option>
                    <option value="cat">🐱 Gattini</option>
                    <option value="ghost">👻 Fantasmini</option>
                    <option value="music">🎵 Musicali</option>
                </select>
            </label>
            <br/>
            <label>Dimensione (px):
                <input type="range" min="10" max="200" class="unicornizor-size-slider" />
                <span class="size-label"></span>
            </label>
            <br/>
            <label>Animazione:
                <select class="unicornizor-anim-select">
                    <option value="">Nessuna</option>
                    <option value="floatUp">Float Up</option>
                    <option value="floatDown">Float Down</option>
                    <option value="rotate">Rotate</option>
                    <option value="pulse">Pulse</option>
                    <option value="bounce">Bounce</option>
                    <option value="shake">Shake</option>
                    <option value="flip">Flip</option>
                    <option value="swing">Swing</option>
                    <option value="tada">Tada</option>
                    <option value="wobble">Wobble</option>
                    <option value="jello">Jello</option>
                </select>
            </label>
            <br/>
            <button type="button" class="button unicornizor-remove-item">Rimuovi</button>
        </div>
        `);

        // Reference
        const $typeSel   = $c.find('.unicornizor-type-select');
        const $sizeSli   = $c.find('.unicornizor-size-slider');
        const $sizeLabel = $c.find('.size-label');
        const $animSel   = $c.find('.unicornizor-anim-select');
        const $remBtn    = $c.find('.unicornizor-remove-item');

        // Set value
        $typeSel.val(item.type || 'stars');
        $sizeSli.val(item.size || 50);
        $sizeLabel.text(item.size || 50);
        $animSel.val(item.animation || '');

        // Eventi
        $typeSel.on('change', function(){
            item.type = $(this).val();
            updatePreviewItem(index);
        });
        $sizeSli.on('input', function(){
            const val = parseInt($(this).val(), 10);
            item.size = val;
            $sizeLabel.text(val);
            updatePreviewItem(index);
        });
        $animSel.on('change', function(){
            item.animation = $(this).val();
            updatePreviewItem(index);
        });
        $remBtn.on('click', function(){
            items.splice(index, 1);
            renderUI();
        });

        // Hover highlight inverso: passare col mouse su questa card => evidenzia il .preview-item
        $c.on('mouseenter', function(){
            const $prevItem = $previewArea.find('.unicornizor-preview-item').eq(index);
            $prevItem.addClass('highlighted');
            $c.addClass('highlighted-card');
        }).on('mouseleave', function(){
            const $prevItem = $previewArea.find('.unicornizor-preview-item').eq(index);
            $prevItem.removeClass('highlighted');
            $c.removeClass('highlighted-card');
        });

        return $c;
    }

    function createPreviewItem(item, index) {
        const iconChar = getIconChar(item.type);
        const animClass = item.animation ? 'anim-' + item.animation : '';
        const sizeVal = item.size || 50;
        let x = (item.posX !== undefined) ? item.posX : 10;
        let y = (item.posY !== undefined) ? item.posY : 10;

        const $el = $(`
            <span class="unicornizor-preview-item ${animClass}" style="
                position:absolute;
                cursor:move;
                font-size:${sizeVal}px;
            ">${iconChar}</span>
        `).data('index', index);

        $el.css({ left: x, top: y });

        // Draggable
        $el.draggable({
            containment: "parent",
            stop: function(e, ui) {
                item.posX = ui.position.left;
                item.posY = ui.position.top;
                $itemsData.val(JSON.stringify(items));
            }
        });

        // Hover highlight: passare col mouse => evidenzio la card corrispondente
        $el.on('mouseenter', function(){
            $el.addClass('highlighted');
            // Trova la card corrispondente
            const $ctrl = $itemsList.find('.unicornizor-item-control').eq(index);
            $ctrl.addClass('highlighted-card');
        }).on('mouseleave', function(){
            $el.removeClass('highlighted');
            const $ctrl = $itemsList.find('.unicornizor-item-control').eq(index);
            $ctrl.removeClass('highlighted-card');
        });

        return $el;
    }

    function updatePreviewItem(index) {
        const item = items[index];
        const $el = $previewArea.find('.unicornizor-preview-item').eq(index);

        // Update icon
        $el.text(getIconChar(item.type));
        // Update size
        $el.css('font-size', item.size + 'px');

        // Remove old anim- classes
        $el.removeClass(function(i, className){
            return (className.match(/(^|\s)anim-\S+/g) || []).join(' ');
        });
        // Add new anim
        if(item.animation) {
            $el.addClass('anim-' + item.animation);
        }

        // Salva
        $itemsData.val(JSON.stringify(items));
    }

    function getIconChar(type) {
        switch(type) {
            case 'hearts':   return '❤️';
            case 'unicorn':  return '🦄';
            case 'rainbow':  return '🌈';
            case 'cupcake':  return '🧁';
            case 'cloud':    return '☁️';
            case 'flower':   return '🌸';
            case 'star2':    return '🌟';
            case 'cat':      return '🐱';
            case 'ghost':    return '👻';
            case 'music':    return '🎵';
            default:         return '⭐';
        }
    }

    $addItemBtn.on('click', function(){
        items.push({
            type: 'stars',
            size: 50,
            posX: 10,
            posY: 10,
            animation: ''
        });
        renderUI();
    });

    renderUI();
});
