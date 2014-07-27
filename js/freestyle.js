jQuery(document).ready(function($){
	$('#old').html(
		'<iframe style="background-color: #777; border: none;" src="http://domino.cbdweb.net/freestyle.nsf/indexwidget/infowidget?opendocument&' + 
		(window.location.search ? window.location.search.substr(1) + "&" : "") + 
		'referrer=' + encodeURI(document.referrer) + 
		'" height="500" width="282" frameborder="0;">'
	);
})(jQuery)