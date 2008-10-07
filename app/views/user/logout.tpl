<h2>Really Logout?</h2>
<?php if($changes): ?>
<p class="message error">You have unsaved changes!</p>
<input type="button" value="Logout Anyway" onclick="blackbird.logout();" />
<input type="button" value="Cancel" onclick="blackbird.closeLightbox();" />
<?php else: ?>
<input type="button" value="Logout" onclick="blackbird.logout();" />
<input type="button" value="Cancel" onclick="blackbird.closeLightbox();" />
<?php endif ?>