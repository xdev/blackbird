

/* Let's help IE/Win learn how to use hover states */
startList = function() {
	
	// Navigation menu dropdown
	if ($("nav")) {
		navRoot = $("nav");
		for (i=0; i<navRoot.childNodes.length; i++) {
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
	if (document.getElementsByClassName('records')) {
		var datagrid = document.getElementsByClassName('records');
		for (i=0;i<datagrid.length;i++) {
			navRoot = datagrid[i];
			for (ii=0; ii<navRoot.childNodes.length; ii++) {
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