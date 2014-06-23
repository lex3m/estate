$(function(){
    $("ul.dropdown li").hover(function(){
        $(this).addClass("hover");
        $('ul:first',this).css('visibility', 'visible');
    
    }, function(){
        $(this).removeClass("hover");
        $('ul:first',this).css('visibility', 'hidden');
    
    });
    $("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");


	
	$("ul.dropDownNav li").hover(function(){
        $(this).addClass("hover");
        $('ul:first',this).css('visibility', 'visible');
    
    }, function(){
        $(this).removeClass("hover");
        $('ul:first',this).css('visibility', 'hidden');
    
    });

	$("ul.dropDownNav li ul li:has(ul)").find("a:first").append(" &raquo; ");
	//$("ul.dropDownNav li:has(ul)").find("a:first").prepend(" &#9660;");
});