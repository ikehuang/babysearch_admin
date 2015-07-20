//  Andy Langton's show/hide/mini-accordion @ http://andylangton.co.uk/jquery-show-hide

// this tells jquery to run the function below once the DOM is ready
$(document).ready(function() {

    // choose text for the show/hide link - can contain HTML (e.g. an image)
    var showText='顯示';
    var hideText='隱藏';

    // initialise the visibility check
    var is_visible = false;

    // append show/hide links to the element directly preceding the element with a class of "toggle"
    $('.toggle').prev().append(' <a href="#" class="toggleLink">'+hideText+'</a>');

    // hide all of the elements with a class of 'toggle'
    $('.toggle').show();

    // capture clicks on the toggle links
    $('a.toggleLink').click(function() {

        // switch visibility
        is_visible = !is_visible;

        // change the link text depending on whether the element is shown or hidden
        if ($(this).text()==showText) {
            $(this).text(hideText);
            $(this).parent().next('.toggle').slideDown('slow');
        } else {
            $(this).text(showText);
            $(this).parent().next('.toggle').slideUp('slow');
        }

        // return false so any link destination is not followed
        return false;

    });
});

// make sure the $ is pointing to JQuery and not some other library
(function($){
    // add a new method to JQuery

    $.fn.equalHeight = function() {
       // find the tallest height in the collection
       // that was passed in (.column)
        tallest = 0;
        this.each(function(){
            thisHeight = $(this).height();
            if( thisHeight > tallest)
                tallest = thisHeight;
        });

        // set each items height to use the tallest value found
        this.each(function(){
            $(this).height(tallest);
        });
    }
})(jQuery);