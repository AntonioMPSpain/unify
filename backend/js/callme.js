//===============================================
// SISTEMA DE POPUP ACTIVABLE/DESACTIVABLE.
//===============================================
//
//	Author: 
//		jEsuSdA
//	Twitter:
//		@jesusda
//	Author URL: 
//		jesusda.com
//	Ultima modificación:
//		2015/01/29




// Cargamos el popup al cargar la página.
window.onload=callme_test;


//===============================================
// FUNCIONES PARA MANEJO DEL POPUP
//===============================================


function callme_test()
// Esta función testea si existe una cookie indicando
// si el popup se debe mostrar o no.
// si la cookie no existe, el popup se muestra.
// si la cookie existe y está a off, el popup no se muestra. 
{
	var contador;
	off = cookieRead( "callme") ;
	//contador = parseInt(contador);
	if ( off != 1   )
	{
		callme_on();
	}
}
// fin callme_test

//·························································//

function callme_on()
// Esta función muestra el código html del popup
{

	var htmlcode = '<div id="callme">';
	htmlcode += '<p class="callme1">¿Necesitas ayuda?,</p>';
	htmlcode += '<p class="callme2"><a href="https://www.activatie.org/web/llama.php">Déjanos tu teléfono y <strong>Nosotros te llamamos</strong></a></p>';
	htmlcode += '<p class="callmeoff"><a href="javascript:void(0);" onclick="callme_off()" title="Clic para Ocultar">Cerrar</a></p>';
	htmlcode += '</div>';

	document.getElementById("extra").innerHTML= htmlcode ;
}
// fin callme_on

//·························································//

function callme_off()
// Esta función se ejecuta cuando se hace clic en el link "Cerrar"
// del popup.
// Al hacerse clic en el botón cerrar, se genera una cookie, que se guarda
// donde se indica que el popup debe ocultarse.
// La cookie tiene una vida de 7 días, de forma que, pasado ese tiempo
// el popup se vuelve a mostrar. 
{
	var htmlcode = "";
	document.getElementById("extra").innerHTML= htmlcode ;
	var off = 1;
	cookieWrite ( "callme" , off , 1);
}




//===============================================
// FUNCIONES PARA MANEJO DE COOKIES
//===============================================


function cookieDelete( cookie_name )
// función que, dado un nombre de una cookie, la elimina
// asignándole una fecha de expiración anterior a la actual.
{
	document.cookie = cookie_name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
// fin cookieDelete	
	
//·························································//
		
function cookieRead( cookie_name )
// función que, dado el nombre de una cookie, devuelve su valor
// según el método de w3schools (http://www.w3schools.com/js/js_cookies.asp)
//
// El nombre de la cookie se toma del parámetro de la función (cookie).
// Crea una variable (name) con el texto a buscar (cookie + "=").
// Separa document.cookie a través de ";" dentro de un array ca (ca = document.cookie.split(';')).
// Recorre el array ca  (i=0;i<ca.length;i++), y lee cada valor c=ca[i]).
// Si la cookie buscada es encontrada (c.indexOf(name) == 0), 
// devuelve su valor (c.substring(name.length,c.length).
// Si no se encuentra la cookie buscada, se devuelve una cadena vacía: "".
{
 	var name = cookie_name + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) 
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
	
}
// fin cookieRead
	
//·························································//

function cookieWrite( cookie_name , value , days )
// función que recibe el nombre de una cookie, su valor y el número
// de días en expirar y crea una cookie con esos valores.
{
	var expires;
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = "; expires=" + date.toGMTString();
	}
	else 
	{
		expires = "";
	}
	document.cookie = cookie_name + "=" + value + expires + "; path=/";
}
// fin cookieWrite


