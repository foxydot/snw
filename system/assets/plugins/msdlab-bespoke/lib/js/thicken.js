jQuery(document).ready(function($) {
    $('a > img').each(function(){
        var link = $(this).parent('a');
        var href = link.attr('href');
        //console.log('href: ' + href);
        if(href.indexOf('.png') > 1 || href.indexOf('.jpg') > 1 || href.indexOf('.jpeg') > 1 || href.indexOf('.svg') > 1){
            link.attr("data-fancybox","gallery");
        }
    });
});