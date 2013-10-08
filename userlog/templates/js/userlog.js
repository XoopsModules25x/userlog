function isOneChecked($name) {
  // All <input> tags...
  var chx = document.getElementsByName($name);
  for (var i=0; i<chx.length; i++) {
    // If you have more than one checkbox group, also check the name attribute
    // for the one you want as in && chx[i].name == 'choose'
    // Return true from the function on first match of a checked item
    if (chx[i].type == 'checkbox' && chx[i].checked) {
      return true;
    } 
  }
  // End of the loop, return false
  return false;
}
// http://stackoverflow.com/questions/2617480/how-to-get-all-elements-which-name-starts-with-some-string
function preventSubmitEmptyInput($prefix) {
	var eles = [];
	var inputs = document.getElementsByTagName("input");
	for(var i = 0; i < inputs.length; i++) {
	    if(inputs[i].name.indexOf($prefix) == 0) {
			eles.push(inputs[i]);
		}
    }
	for(var i = 0; i < eles.length; i++) {
		if (eles[i].value =='') {
			eles[i].setAttribute('name', '');
		}
	}
	return true;
}
// START irmtfan - improve: add alt, title, id and innerHTML - recognize a IMG tag for src
function ToggleBlock(blockid, icon, src_expand, src_collapse, alt_expand, alt_collapse, class_expand, class_collapse)
{
	var Img_tag='IMG';
	var el=document.getElementById(blockid);
	if (el.className == class_expand)
	{
		el.className = class_collapse;
		if (icon.nodeName  == Img_tag) {
			icon.src = src_collapse;
		}
		icon.alt= alt_collapse;
		icon.id = findBaseName(src_collapse);
		SaveCollapsed(blockid, true);
	}
	else
	{
		el.className = class_expand;
		if (icon.nodeName == Img_tag) {
			icon.src = src_expand;
		}
		icon.alt= alt_expand;
		icon.id = findBaseName(src_expand);
		SaveCollapsed(blockid, false);
	}
	icon.title = icon.alt;
	if (icon.nodeName  != Img_tag){
		icon.innerHTML=icon.alt; // to support IE7&8 use innerHTML istead of textContent
	}
	document.getElementById(blockid + "text").innerHTML=icon.alt;	
}
// source: http://stackoverflow.com/questions/1991608/find-base-name-in-url-in-javascript
function findBaseName(url) {
    var fileName = url.substring(url.lastIndexOf('/') + 1);
    var dot = fileName.lastIndexOf('.');
    return dot == -1 ? fileName : fileName.substring(0, dot);
}
// END irmtfan - improve: add alt, title and innerHTML - recognize a IMG tag for src
function SaveCollapsed(objid, addcollapsed)
{
	var collapsed = GetCookie(toggle_cookie);
	var tmp = "";
	if (collapsed != null)
	{
		collapsed = collapsed.split(",");
		for (i in collapsed)
		{
			if (collapsed[i] != objid && collapsed[i] != "")
			{
				tmp = tmp + collapsed[i];
				tmp = tmp + ",";
			}
		}
	}

	if (addcollapsed)
	{
		tmp = tmp + objid;
	}

	expires = new Date();
	expires.setTime(expires.getTime() + (1000 * 86400 * 365));
	SetCookie(toggle_cookie, tmp, expires);
}

function SetCookie(name, value, expires)
{
	if (!expires)
	{
		expires = new Date();
	}
	document.cookie = name + "=" + escape(value) + "; expires=" + expires.toGMTString() +  "; path=/";
}

function GetCookie(name)
{
	cookie_name = name + "=";
	cookie_length = document.cookie.length;
	cookie_begin = 0;
	while (cookie_begin < cookie_length)
	{
		value_begin = cookie_begin + cookie_name.length;
		if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
		{
			var value_end = document.cookie.indexOf (";", value_begin);
			if (value_end == -1)
			{
				value_end = cookie_length;
			}
			return unescape(document.cookie.substring(value_begin, value_end));
		}
		cookie_begin = document.cookie.indexOf(" ", cookie_begin) + 1;
		if (cookie_begin == 0)
		{
			break;
		}
	}
	return null;
}