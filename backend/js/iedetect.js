 // IE 10 only CSS properties
 var ie10Styles = [
     'msTouchAction',
     'msWrapFlow',
     'msWrapMargin',
     'msWrapThrough',
     'msOverflowStyle',
     'msScrollChaining',
     'msScrollLimit',
     'msScrollLimitXMin',
     'msScrollLimitYMin',
     'msScrollLimitXMax',
     'msScrollLimitYMax',
     'msScrollRails',
     'msScrollSnapPointsX',
     'msScrollSnapPointsY',
     'msScrollSnapType',
     'msScrollSnapX',
     'msScrollSnapY',
     'msScrollTranslation',
     'msFlexbox',
     'msFlex',
     'msFlexOrder'];

 var ie11Styles = [
     'msTextCombineHorizontal'];

 /*
  * Test all IE only CSS properties
  */
 var d = document;
 var b = d.body;
 var s = b.style;
 var ieVersion = null;
 var property;

 // Test IE10 properties
 for (var i = 0; i < ie10Styles.length; i++) {
     property = ie10Styles[i];

     if (s[property] != undefined) {
         ieVersion = "ie10";
         createEl("IE10 style found: " + property);
     }
 }

 // Test IE11 properties
 for (var i = 0; i < ie11Styles.length; i++) {
     property = ie11Styles[i];

     if (s[property] != undefined) {
         ieVersion = "ie11";
         createEl("IE11 style found: " + property);
     }
 }


/*if (ieVersion)
{

	var root = document.getElementsByTagName( 'html' )[0]; // '0' to assign the first (and only `HTML` tag)
	root.setAttribute( "class", "iehack" );
}
else {
	var root = document.getElementsByTagName( 'html' )[0]; // '0' to assign the first (and only `HTML` tag)
	root.setAttribute( "class", "iehack" );
	}*/

 if (ieVersion) 
 {
     b.className = ieVersion;
     $('html').html('Version: ' + ieVersion);
 } else {
     createEl('Not IE10 or 11.');
 }

 /*
  * Just a little helper to create a DOM element
  */
 function createEl(content) {
     el = d.createElement('div');
     el.innerHTML = content;
     b.appendChild(el);
 }