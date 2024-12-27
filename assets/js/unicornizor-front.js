jQuery(document).ready(function($){
    // Per ogni container, calcoliamo bounding box e settiamo width/height
    $('.unicornizor-container').each(function(){
        const $container = $(this);
        let maxRight = 0, maxBottom = 0;

        $container.find('.unicornizor-item').each(function(){
            const $item = $(this);
            const left = parseFloat($item.css('left')) || 0;
            const top  = parseFloat($item.css('top')) || 0;
            const w    = $item.outerWidth();
            const h    = $item.outerHeight();

            if(left + w > maxRight) {
                maxRight = left + w;
            }
            if(top + h > maxBottom) {
                maxBottom = top + h;
            }
        });

        // Applichiamo dimensioni minime
        $container.css({
            width:  maxRight + 'px',
            height: maxBottom + 'px'
        });
    });
});
