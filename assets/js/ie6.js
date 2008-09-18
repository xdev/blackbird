/* $Id$ */

/* Let's help IE/Win learn how to use hover states */
startList = function() {
	
	// Navigation menu dropdown
	if ($("nav")) {
		navRoot = $("nav");
		var iMax = navRoot.childNodes.length;
		for (i=0; i<iMax; i++) {
			node = navRoot.childNodes[i];
			if (node.nodeName=="LI") {
				node.onmouseover=function() {
					this.className+=" hover";
				}
				node.onmouseout=function() {
					this.className=this.className.replace(" hover", "");
				}
			}
		}
	}
	
	// Data Grid Record highlighter
	if ($$('.records')) {
		var datagrid = $$('.records');
		var iMax = datagrid.length;
		for (i=0;i<iMax;i++) {
			navRoot = datagrid[i];
			var iiMax = navRoot.childNodes.length;
			for (ii=0; ii<iiMax; ii++) {
				node = navRoot.childNodes[ii];
				if (node.nodeName=="TR") {
					node.onmouseover=function() {
						this.className+=" hover";
					}
					node.onmouseout=function() {
						this.className=this.className.replace(" hover", "");
					}
				}
			}
		}
	}
	
}
window.onload=startList;