<?php
/**
 * Copyright (c) 2014 Bubuti
 * This file is part of Bubuti Donation.
 *
 * Bubuti Donation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bubuti Donation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Bubuti Donation.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

$current_act_id = get_post_meta( $post->ID, '_bubuti_act_sharing_id', true );
?>
<div class="sw-white-bg cf">
    <div class="sw-setting-label">
        <div class="sw-setting-name">Act ID <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
        <div class="sw-setting-desc">If no act number is entered, then the default act on the plugin settings page will show.</div>
    </div>
    <div class="sw-setting">
        <input name="bubuti-share-act-id" id="bubuti-share-act-id" type="text" placeholder="using default act" value="<?php echo $current_act_id; ?>" <?php echo ($current_act_id != '') ? 'class="input-checkmark"' : ''; ?>>
    </div>
</div>

<?php
$current_act_sharing_status = get_post_meta( $post->ID, '_bubuti_act_sharing_status', true );
if ( empty( $current_act_sharing_status ) ) { $current_act_sharing_status = 'disabled'; }
?>
<div class="sw-white-bg cf">
    <div class="sw-setting-label">
        <div class="sw-setting-name">Enable / Disable <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
        <div class="sw-setting-desc"></div>
    </div>
    <div class="sw-setting">
        <input name="act-enabled-yes" class="act-placement" type="checkbox" id="act-enabled-yes"<?php echo ( $current_act_sharing_status == 'enabled' ) ? 'checked="checked"' : '' ?>> <label for="act-enabled-yes">Enable on this post/page</label><br/>
        <input name="act-enabled-no" class="act-placement" type="checkbox" id="act-enabled-no"<?php echo ( $current_act_sharing_status == 'disabled' ) ? 'checked="checked"' : '' ?>> <label for="act-enabled-no">Disable on this post/page</label>
    </div>
</div>
