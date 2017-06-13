var gUrl;
function getUrl() {
    var a = window.location.href;
    gUrl = getQuerystring('url', a);
    //if (checkURL(gUrl)) { //alert('good  ' + gUrl); } 
    	//else { alert('enter url with pdf file'); }
}

function getQuerystring(key, default_) {
    if (default_ == null) default_ = "";
    key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
    var qs = regex.exec(window.location.href);
    if (qs == null)
        return default_;
    else{
		//alert(qs[1]);
        return qs[1];
	}
}
function checkURL(url) {
    return (url.match(/\.(pdf)$/) != null);
}