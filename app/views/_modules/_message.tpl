<?php $id = substr(sha1(rand(0,100)),10) ?>
<p class="message <?= $class ?>" style="display:none" id="message_<?= $id ?>"><?= $message ?><a href="#" onclick="new Effect.Fade($(this.up()),{duration:0.5}); return false;">close</a></p>
<script type="text/javascript">
	new Effect.Appear($('message_<?= $id ?>'),{duration:1.0});
	// /new Effect.Fade($('message_<?= $id ?>'),{duration:2.0,delay:5.0});
</script>