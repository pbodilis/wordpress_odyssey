/*
 * based on the work of Rasul Bahmanziari (rasul.b@gmail.com / http://gahnevesht.name/)
 * I suspect he based his work on Julien Roumagnac (http://www.j-roumagnac.net/).
 *
 * social & rss icons created by Helen Gizi (http://www.onextrapixel.com/2012/02/28/freebies-black-white-minimal-social-icons-pack/)
 *
 * rewritten and improved to enhance CSS3 support and enjoy the new browsers capabalities rather than Javascript's
 * Pierre Bodilis
 * http://rataki.eu/
 * pierre.bodilis@rataki.eu
 * 
 */
var panelOut;
$(window).load(function () {
    // let's chose current color - same for all pages
    // Note that the body class is set by the php layer: it reads the cookie and then set the right class.
    $('.menu.but.color a').click(function() {
        setTheme(this.className, true);
        createCookie('odyssey_theme_color', this.className, 30);
    });

    var isAnImage = document.location.href.indexOf('x=') == -1;

    if (isAnImage) { // that's an image, let's load the image stuff and the panel
        // set the body to display image (remove overflow, ...)
        document.body.className += ' forImageDisplay';

        var fromComments = document.location.href.indexOf('comments') > -1;
        $('#thank_for_comment').toggleClass('show', fromComments);

        panelOut = fromComments || readCookie('odyssey_theme_panelVisibility') == '1';
        $('#panel').toggleClass('out', panelOut);
        $('#panel_handle').click(function (event) {
            togglePanel();
        });

        checkCommentForm();

        refreshElements();
        $('#photo_frame').fadeIn(400);

        if (imgPrevId == imgId) { $('.navLink.prev').css('display', 'none'); }
        if (imgNextId == imgId) { $('.navLink.next').css('display', 'none'); }

        keyboardNavigation();
    }

    easterEgg();
});
$(window).resize(function () {
    refreshElements();
});

function setTheme(className) {
    document.body.className = className;
}

function togglePanel() {
    $('#panel').toggleClass('out');
    panelOut = !panelOut;
    createCookie('odyssey_theme_panelVisibility', (panelOut ? '1' : '0'), 30);
}

function refreshElements() {
    setPanelHeight();
    setPhotoPositionAndInfo();
}
function setPanelHeight() {
    newPanelHeight = dE.clientHeight - $('#header').height();
    $('#panel_scroll').css('height', newPanelHeight);
    $('#panel_scroll').jScrollPane();
}
function setPhotoPositionAndInfo() {
    var borderWidth = 5; // check in css file #photo_frame for consistency
    var frameWidth, frameHeight, resizedWidth, resizedHeight, offsetHeight;
    
    var photoInfosHeight = $('#photo_infos').height();
    var displayHeightArea = dE.clientHeight - $('#header').height() - borderWidth * 2 - photoInfosHeight - 20;
    if (imageHeight < displayHeightArea) {
        resizedHeight = imageHeight;
        resizedWidth = imageWidth;
        if (imageHeight > displayHeightArea - photoInfosHeight / 2) { // let's see if we should elevate the image
            offsetHeight = photoInfosHeight / 2 - displayHeightArea + imageHeight;
        } else {
            offsetHeight = 0;
        }
    } else { // height smaller than the display area, let's resize the image
        resizedHeight = displayHeightArea;
        resizedWidth = resizedHeight * imageWidth / imageHeight;
        offsetHeight = photoInfosHeight / 2;
    }
    frameHeight = Math.round(resizedHeight);
    frameWidth = Math.round(resizedWidth);
    
    $('#photo_frame #img').css({
        'width': frameWidth,
        'height': frameHeight
    });
    $('#photo_frame').css({
        'margin-left': (dE.clientWidth - frameWidth  + $('#panel_handle').width()) / 2 - borderWidth,
        'margin-top': (dE.clientHeight - frameHeight + $('#header').height()) / 2 - borderWidth - offsetHeight
    });
}

function checkCommentForm() {
    $('#message').val(defaultValue).focus(function () {
        if ($(this).val() == defaultValue) {
            $(this).val('')
        }
    }).blur(function () {
        if ($(this).val() == '') {
            $(this).val(defaultValue)
        }
    });
    $('#comment_submit').click(function () {
        var name = $('#name');
        var email = $('#email');
        var message = $('#message');

        var nameNOK = name.val().length < 3;
        name.toggleClass('error', nameNOK);

        var emailNOK = !isValidEmailAddress(email.val());
        email.toggleClass('error', emailNOK);

        var messageNOK = message.val().length < 3 || message.val() == defaultValue;
        message.toggleClass('error', messageNOK);

        if (!emailNOK && !nameNOK && !messageNOK) {
            $('#comment_submit').val(sendingValue).attr('disabled', 'disabled');
            $('#form').submit();
        }
        return false;
    })
}
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(('[\w-\s]+')|([\w-]+(?:\.[\w-]+)*)|('[\w-\s]+')([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress)
}
function keyboardNavigation() {
    var txtFocus = false;
    $('input, textarea').focus(function() {
        txtFocus = true;
    });
    $('input, textarea').blur(function() {
        txtFocus = false;
    });
    $(document).keydown(function(e) {
        if (!txtFocus) { // if typing text, do not trigger events !
            switch(e.which){
                case 32:
                    togglePanel();
                    break;
                case 37:
                    if (imgPrevId != imgId) {
                        document.location = './index.php?showimage=' + imgPrevId;
                    }
                    break;
                case 39:
                    if (imgNextId != imgId) {
                        document.location = './index.php?showimage=' + imgNextId;
                    }
                    break;
            }
        }
    });
}


// cookies! yummy
function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

// yeah, easter egg :D
var easterEggTimeout;
function easterEgg() {
    if ( window.addEventListener ) {
        var kkeys = [];
        var keyWordPurple = '80,85,82,80,76,69'; // 'p' 'u' 'r' 'p' 'l' 'e'
        var keyWordZoidberg = '90,79,73,68,66,69,82,71'; // 'z' 'o' 'i' 'd' 'b' 'e' 'r' 'g'
        window.addEventListener('keydown', function(e) {
            kkeys.push( e.keyCode );
            if ( kkeys.toString().indexOf( keyWordPurple ) >= 0 ) {
                kkeys = [];

                $('#img').append('<div id="purple" class="easterEgg"></div>');
                easterEggTimeout = setTimeout('easterEggRun()', 500);

                var navigatorsProperties=['transitionend','OTransitionEnd','webkitTransitionEnd'];
                for (var i in navigatorsProperties) {
                    //We attach it to our box
                    document.getElementById('purple').addEventListener(navigatorsProperties[i],function() {
                        $('#purple').remove();
                    },false);
                }
            }
            if ( kkeys.toString().indexOf( keyWordZoidberg ) >= 0 ) {
                kkeys = [];

                $('body').append('<div id="zoidberg" class="easterEgg"></div>');
                easterEggTimeout = setTimeout('easterEggRun()', 500);
            }
        }, true);
    }
}
function easterEggRun() {
    $('.easterEgg').toggleClass('out', true);
    clearTimeout(easterEggTimeout);
}