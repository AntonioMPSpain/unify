/*------------------------------------*\
	SCRIPTS DE JS
\*------------------------------------*/
/*
Author: 
	jEsuSdA
Twitter:
	@jesusda
Author URL: 
	jesusda.com
Ultima modificación:
	09/10/2013

Descripción:
	Scripts de uso en la web
/*****************************************************************/

/*------------------------------------*\
	AÑADIR URL A FAVORITOS
\*------------------------------------*/
function bookmark(url, sitename)
{
  ns="Pulsa CTRL+D para añadir esta web a tus favoritos."
  if ((navigator.appName=='Microsoft Internet Explorer') &&
    (parseInt(navigator.appVersion)>=4))
  {
    window.external.AddFavorite(url, sitename);
  }
  else if (navigator.appName=='Netscape')
  {
    alert(ns);
  }
}

/*****************************************************************/
/*------------------------------------*\
	INICIAR FLEXISLIDER
\*------------------------------------*/

$(window).load(function() {
  $('#banners').flexslider({
    animation: "slide" ,
    animationLoop: "true" ,
    slideshow: "true" , 
    slideshowSpeed: "9000" ,
    pauseOnAction: "false", 
	pauseOnHover: "true", 
	useCSS: "true",
  });
  
$('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 100,
    itemMargin: 5,
    asNavFor: '#slider'
  });
   
  $('#slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 490,
    sync: "#carousel"
  });  
  
  
  
Shadowbox.init({
    handleOversize: "drag",
    modal: true
});  
  


/*****************************************************************/
/*------------------------------------*\
	BOTONES + Y - EN FORMULARIOS
\*------------------------------------*/


$(function()
{

        $("#add1").click(function(){
        var newQty = +($("#appendedInputButtons").val()) + 1;
        $("#appendedInputButtons").val(newQty);
        });

        $("#minus1").click(function(){
        var newQty = +($("#appendedInputButtons").val()) - 1;
        if(newQty < 0)newQty = 0;
        $("#appendedInputButtons").val(newQty);
        });


});



});
//fin window.load function


/*****************************************************************/
/*------------------------------------*\
	GOOGLE ANALYTICS
\*------------------------------------*/
var _gaq = [
	['_setAccount', 'UA-XXXXX-X'],
	['_trackPageview']
];

(function (d, t) {
	var g = d.createElement(t),
	s = d.getElementsByTagName(t)[0];
	g.src = ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g, s)
}(document, 'script'));





/*****************************************************************/
/*------------------------------------*\
	BOTÓN IR ARRIBA
\*------------------------------------*/


jQuery(document).ready(function() {
jQuery("#IrArriba").hide();
jQuery(function () {
jQuery(window).scroll(function () {
if (jQuery(this).scrollTop() > 200) {
jQuery('#IrArriba').fadeIn();
} else {
jQuery('#IrArriba').fadeOut();
}
});
jQuery('#IrArriba a').click(function () {
jQuery('body,html').animate({
scrollTop: 0
}, 800);
return false;
});
});

});


