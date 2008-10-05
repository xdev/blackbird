<p>Are you sure you would like to logout?</p>
<?php if($changes): ?>
<p class="error">You have unsaved changes, you should probably save em!</p>
<!-- insert contextual link here that closes and jumps to first open tab of changes? -->
<?php endif ?>
<input type="button" id="logout_yes" value="Yes" onclick="blackbird.logout();" />
<input type="button" id="logout_no" value="Cancel" onclick="blackbird.closeLightbox();" />