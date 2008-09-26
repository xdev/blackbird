<?php

//main divs

//main tab

//surrounding form markup
//dump contents
//closing form markup
//target iform

//reference to inactive related content or something
print $this->fetchView('/_modules/ui_toolbar',array('controller'=>'record','mode'=>'insert','table'=>$this->table,'id'=>$id));
?>
<div id="bb_module">

<form id="form_main" name="form_main" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_main" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_main" name="form_target_main" class="related_iframe"></iframe>

</div>