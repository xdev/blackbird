<?php

class _Controller extends Controller
{
		
	public function __construct($route,$loadmodel=true)
	{
		parent::__construct($route,$loadmodel);		
	}
	
	public function render()
	{
		//if we have a layout - should get more specific, if we have an html master layout
		if($this->layout_view){
			$this->prepUI();
		}
		
		return parent::render();
	}
	
	public function singulizer($str)
	{
		return preg_replace(
			array('/ies$/','/s$/'),
			array('y',''),
			$str
		);
	}
	
	public function titleCase($str)
	{
		return $this->nv_title_case(str_replace('_',' ',$str));
	}
	
	public function nv_title_case($str) {
		
		function nv_title_skip_dotted($matches) {
		    return preg_match('/[[:alpha:]] [.] [[:alpha:]]/x', $matches[0]) ? $matches[0] : ucfirst($matches[0]);
		}

	    // Edit this list to change what words should be lowercase
	    $small_words = "a an and as at but by en for if in of on or the to v[.]? via vs[.]?";
	    $small_re = str_replace(" ", "|", $small_words);

	    // Replace HTML entities for spaces and record their old positions
	    $htmlspaces = "/&nbsp;|&#160;|&#32;/";
	    $oldspaces = array();
	    preg_match_all($htmlspaces, $str, $oldspaces, PREG_OFFSET_CAPTURE);

	    // Remove HTML space entities
	    $words = preg_replace($htmlspaces, " ", $str);

	    // Split around sentance divider-ish stuff
	    $words = preg_split('/( [:.;?!][ ] | (?:[ ]|^)["“])/x', $words, -1, PREG_SPLIT_DELIM_CAPTURE);

	    for ($i = 0; $i < count($words); $i++) {

	        // Skip words with dots in them like del.icio.us
	        $words[$i] = preg_replace_callback('/\b([[:alpha:]][[:lower:].\'’(&\#8217;)]*)\b/x', 'nv_title_skip_dotted', $words[$i]);

	        // Lowercase our list of small words
	        $words[$i] = preg_replace("/\b($small_re)\b/ei", "strtolower(\"$1\")", $words[$i]);

	        // If the first word in the title is a small word, capitalize it
	        $words[$i] = preg_replace("/\A([[:punct:]]*)($small_re)\b/e", "\"$1\" . ucfirst(\"$2\")", $words[$i]);

	        // If the last word in the title is a small word, capitalize it
	        $words[$i] = preg_replace("/\b($small_re)([[:punct:]]*)\Z/e", "ucfirst(\"$1\") . \"$2\"", $words[$i]);
	    }

	    $words = join($words);

	    // Oddities
	    $words = preg_replace("/ V(s?)\. /i", " v$1. ", $words);                    // v, vs, v., and vs.
	    $words = preg_replace("/(['’]|&#8217;)S\b/i", "$1s", $words);               // 's
	    $words = preg_replace("/\b(AT&T|Q&A)\b/ie", "strtoupper(\"$1\")", $words);  // AT&T and Q&A
	    $words = preg_replace("/-ing\b/i", "-ing", $words);                         // -ing
	    $words = preg_replace("/(&[[:alpha:]]+;)/Ue", "strtolower(\"$1\")", $words);          // html entities

	    // Put HTML space entities back
	    $offset = 0;
	    for ($i = 0; $i < count($oldspaces[0]); $i++) {
	        $offset = $oldspaces[0][$i][1];
	        $words = substr($words, 0, $offset) . $oldspaces[0][$i][0] . substr($words, $offset + 1);
	        $offset += strlen($oldspaces[0][$i][0]);
	    }

	    return $words;
	}
	
	
	public function css()
	{
		// Default CSS files
		$this->css[] = 'reset.css';
		$this->css[] = 'prototype.css';
		$this->css[] = 'prototype_edit.css';
		$this->css[] = 'data.css';
		$this->css[] = 'imagebrowser.css';
		// Remove duplicates
		$files = array_unique($this->css);
		// Build links
		$r = '';
		foreach ($files as $filename) {
			$r .= "\r\t\t" . sprintf(
				'<link rel="stylesheet" href="%s" type="text/css" media="screen" charset="utf-8" />',
				BASE . 'assets/css/' . $filename
			);
		}
		return $r;
	}
	
	
	
	public function prepUI()
	{
				
		$tablesA = _ControllerFront::$session->getNavigation();
		
		$this->view(array('container'=>'ui_nav','view'=>'/_modules/ui_nav','data'=>array('tableA'=>$tablesA)));
		
		//do something here
		if(isset($this->route['table'])){
			$table = $this->route['table'];
			$tablename = $table;
		}else{
			$table = '';
			$tablename = '';
		}
		
		$this->view(array('container'=>'ui_breadcrumb','view'=>'/_modules/ui_breadcrumb','data'=>array('table'=>$table,'tablename'=>$tablename)));
		
		$this->view(array('container'=>'ui_session','view'=>'/_modules/ui_session','data'=>''));
	}
	
}