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
?>
<div class="wrap bubuti-share-settings">
    <h2>Bubuti Donation Plugin Options</h2>

    <form method="POST" action="">
        <?php wp_nonce_field( 'save-bubuti-donation-settings' ); ?>
        <input type="hidden" name="save-bubuti-donation-settings" value="1">
        <div class="sw-row-top cf">
            <div class="sw-label">Plugin Setup</div>
            <div class="sw-form-content first">
                <div class="sw-white-bg cf sw-bubuti-hideme">
                    <div class="sw-setting-label">
                        <div class="sw-setting-name">Setup Video <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
                        <div class="sw-setting-desc"></div>
                    </div>
                    <div class="sw-setting">
                        <!-- Video goes here -->
                    </div>
                </div>
            </div>

            <div class="sw-label"></div>
            <div class="sw-form-content">
                <div class="sw-white-bg cf">
                    <div class="sw-setting-label">
                        <div class="sw-setting-name" style="padding-top: 15px;">Create Account <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
                        <div class="sw-setting-desc"></div>
                    </div>
                    <div class="sw-setting sw-btn-bubuti">
                        <a class="sw-btn-bubuti-link" href="https://www.bubuti.com/join" target="_blank">Create Account on Bubuti.com</a>
                        <a class="sw-btn-bubuti-go" href="https://www.bubuti.com/join" target="_blank"><img src="<?php echo $this->url; ?>/img/arrow_go.png" alt="Go arrow"></a>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>

        <div class="sw-row cf">
            <div class="sw-label">Customization</div>
            <div class="sw-form-content first">
                <div class="sw-white-bg cf">
                    <div class="sw-setting-label">
                        <div class="sw-setting-name">Default Act ID <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
                        <div class="sw-setting-desc"></div>
                    </div>
                    <div class="sw-setting">
                        <input name="bubuti-share-act-id" id="bubuti-share-act-id" type="text" placeholder="paste default act here" value="<?php echo $this->settings['default_act_id']; ?>" <?php echo ($this->settings['default_act_id'] != '') ? 'class="input-checkmark"' : ''; ?>>
                    </div>
                </div>
            </div>

            <div class="sw-label"></div>
            <div class="sw-form-content">
                <div class="sw-white-bg cf">
                    <div class="sw-setting-label">
                        <div class="sw-setting-name">Button Color <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
                        <div class="sw-setting-desc"></div>
                    </div>
                    <div class="sw-setting sw-btn-bubuti">
                        <select name="bubuti-act-btn-color" id="bubuti-act-btn-color">
                            <option value="default">Choose Default Button Color</option>
                            <option value="blue"<?php echo ( $this->settings['button_color'] == 'blue' ) ? ' selected="selected"' : '' ?>>Blue</option>
                            <option value="gray"<?php echo ( $this->settings['button_color'] == 'gray' ) ? ' selected="selected"' : '' ?>>Gray</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="sw-label"></div>
            <div class="sw-form-content">
                <div class="sw-white-bg cf">
                    <div class="sw-setting-label">
                        <div class="sw-setting-name">Post Types <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
                        <div class="sw-setting-desc"></div>
                    </div>
                    <div class="sw-setting sw-btn-bubuti">
                        <?php $i = 0; foreach($registered_post_types as $post_type) { $i++; ?>
                            <input name="act-post-type[]" value="<?php echo $post_type; ?>" class="act-place-post-type" type="checkbox" id="act-place-post-type-<?php echo $i; ?>"<?php echo ( in_array($post_type, $this->settings['post_types']) ) ? ' checked="checked"' : '' ?>> <label for="act-place-post-type-<?php echo $i; ?>"><?php echo $post_type; ?></label><br/>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="sw-label"></div>
            <div class="sw-form-content">
                <div class="sw-white-bg cf">
                    <div class="sw-setting-label">
                        <div class="sw-setting-name">Button Placement <span class="sw-question"><a target="_blank" href="#">?</a></span></div>
                        <div class="sw-setting-desc"></div>
                    </div>
                    <div class="cf">
                        <div class="sw-setting sw-btn-bubuti sw-btn-placement">
                            <input name="act-placement-top-left" class="act-placement" type="checkbox" id="act-placement-top-left"<?php echo ( $this->settings['placement']['topLeft'] == TRUE ) ? ' checked="checked"' : '' ?>> <label for="act-placement-top-left">Top-Left</label><br/>
                            <input name="act-placement-top-center" class="act-placement" type="checkbox" id="act-placement-top-center"<?php echo ( $this->settings['placement']['topCenter'] == 1 ) ? ' checked="checked"' : '' ?>> <label for="act-placement-top-center">Top-Center</label><br/>
                            <input name="act-placement-bottom-left" class="act-placement" type="checkbox" id="act-placement-bottom-left"<?php echo ( $this->settings['placement']['bottomLeft'] == 1 ) ? ' checked="checked"' : '' ?>> <label for="act-placement-bottom-left">Bottom-Left</label><br/>
                            <input name="act-placement-bottom-center" class="act-placement" type="checkbox" id="act-placement-bottom-center"<?php echo ( $this->settings['placement']['bottomCenter'] == 1 ) ? ' checked="checked"' : '' ?>> <label for="act-placement-right">Bottom-Center</label>
                        </div>
                        <div class="sw-examples">
                            <img class="example-act-placement-top-left"<?php echo ( $this->settings['placement']['topLeft'] == 1 ) ? ' style="display: inline;" ' : '' ?>src="<?php echo $this->url; ?>/img/top-left.jpg">
                            <img class="example-act-placement-top-center" <?php echo ( $this->settings['placement']['topCenter'] == 1 ) ? ' style="display: inline;" ' : '' ?>src="<?php echo $this->url; ?>/img/top-center.jpg">
                            <img class="example-act-placement-bottom-left" <?php echo ( $this->settings['placement']['bottomLeft'] == 1 ) ? ' style="display: inline;" ' : '' ?>src="<?php echo $this->url; ?>/img/bottom-left.jpg">
                            <img class="example-act-placement-bottom-center" <?php echo ( $this->settings['placement']['bottomCenter'] == 1 ) ? ' style="display: inline;" ' : '' ?>src="<?php echo $this->url; ?>/img/bottom-center.jpg">
                        </div>
                    </div>
                </div>
                <div class="sw-white-bg">
                    <input class="sw-save-changes" type="submit" value="Save Changes">
                </div>
            </div>

        </div>
    </form>

</div>
