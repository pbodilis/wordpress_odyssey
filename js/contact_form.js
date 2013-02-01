/* 
 * contact_form.php - Pixelpost Add-on
 * 
 * Javascript is put in here because input field is not allowed on page by XHTML 1.0
 * 
 */

$(document).ready(function(){
    $("span#javascriptreq").remove();
    $.get("token.php",function(txt){
        $("form#contact_form").append('<input type="hidden" name="ts" value="'+txt+'" />');
    })
})