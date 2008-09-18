/* $Id$ */

startList = function() {
	if (document.all && document.getElementById) {
		var navRoot = document.getElementById("nav");
		var iMax = navRoot.childNodes.length;
		for (i=0; i<iMax; i++) {
			var node = navRoot.childNodes[i];
			if (node.nodeName=="LI") {
				node.onmouseover=function() {
					this.className+=" over";
				}
				node.onmouseout=function() {
					this.className=this.className.replace(" over", "");
				}
			}
		}
	}
}
window.onload=startList;


if ((navigator.userAgent.indexOf('MSIE') != -1)
  && (navigator.userAgent.indexOf('Win') != -1))
  {
  document.write(
  	"<style>#nav li a, #nav li a:visited{ width: 120px; height: 20px;} #nav li ul li a, #nav li ul li a:visited { width: 115px; }</style>"
  );
}