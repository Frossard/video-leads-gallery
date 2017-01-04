jQuery(function(){
    jQuery('.delete').click(function(e){
        e.preventDefault();
        
        var url = jQuery(this).attr('href');
        var text = jQuery(this).data('text');
        var r = confirm(text);

        if (r){
            window.location.href = url;
        }
    });
});
