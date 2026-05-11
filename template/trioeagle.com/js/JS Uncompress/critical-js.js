function preventSubmitBeforeJSLoaded(obj){
    event.preventDefault(); 
    alert('Please wait until the web fully loaded.');
}

function addEventListenerByClass(className, event, fn) {
    var list = document.getElementsByClassName(className);
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].addEventListener(event, fn, false);
    }
}
function removeEventListenerByClass(className, event, fn) {
    var list = document.getElementsByClassName(className);
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].removeEventListener(event, fn, false);
    }
}

addEventListenerByClass('prevent-form-submit', 'submit', preventSubmitBeforeJSLoaded); 
