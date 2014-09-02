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
jQuery(document).ready(function($) {
    jQuery('#bubuti-share-act-id').on('focus', function () {
        jQuery(this).css('color', '#585858');
    });
    jQuery('#bubuti-share-act-id').on('blur', function () {
        if (jQuery(this).val() !== '' && isInt(jQuery(this).val())) {
            jQuery(this).addClass('input-checkmark');
        } else {
            jQuery(this).removeClass('input-checkmark');
        }
    });

    jQuery('#bubuti-act-btn-color_input').on('click', function () {
        alert(jQuery('#bubuti-act-btn-color_input').val());
        if (jQuery('#bubuti-act-btn-color_input').val() == 'gray') {
            jQuery('#bubuti-act-btn-color_input').css('background-color', '#fff');
        } else {
            jQuery('#bubuti-act-btn-color_input').css('background-color', '#000');
        }
    });

    jQuery('.act-placement').on('click', function () {
        var selection = jQuery(this).attr('id');

        jQuery('.act-placement').attr('checked', false);
        jQuery(this).attr('checked', 'checked');

        jQuery('.sw-examples img').css('display', 'none');
        jQuery('.example-' + selection).toggle();
    });

    jQuery('#bubuti-act-btn-color').selectbox();

    jQuery('.sw-save-changes').on('click', function () {

        if ( jQuery('#bubuti-act-btn-color option:selected').attr('value') === 'default' ) {
            jQuery('#bubuti-act-btn-color_input').focus();
            alert('You must choose a color');
            return false;
        }

        var post_type_selected = false;
        jQuery('.act-place-post-type').each(function () {
            if (jQuery(this).attr('checked') === 'checked') {
                post_type_selected = true;
            }
        });
        if ( ! post_type_selected ) {
            jQuery('.act-place-post-type:first').focus();
            alert('Please select a post type');
            return false;
        }

        var button_placement_selected = false;
        jQuery('.act-placement').each(function () {
            if (jQuery(this).attr('checked') === 'checked') {
                button_placement_selected = true;
            }
        });
        if ( ! button_placement_selected ) {
            jQuery('.act-placement:first').focus();
            alert('Please select a button placement');
            return false;
        }

    });
});

function isInt(n) {
    return n % 1 === 0;
}

/*
 * jQuery selectbox plugin
 *
 * Copyright (c) 2007 Sadri Sahraoui (brainfault.com)
 * Licensed under the GPL license and MIT:
 *   http://www.opensource.org/licenses/GPL-license.php
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * The code is inspired from Autocomplete plugin (http://www.dyve.net/jquery/?autocomplete)
 *
 * Revision: $Id$
 * Version: 1.2
 *
 * Changelog :
 *  Version 1.2 By Guillaume Vergnolle (web-opensource.com)
 *  - Add optgroup support
 *  - possibility to choose between span or input as replacement of the select box
 *  - support for jquery change event
 *  - add a max height option for drop down list
 *  Version 1.1
 *  - Fix IE bug
 *  Version 1.0
 *  - Support jQuery noConflict option
 *  - Add callback for onChange event, thanks to Jason
 *  - Fix IE8 support
 *  - Fix auto width support
 *  - Fix focus on firefox dont show the carret
 *  Version 0.6
 *  - Fix IE scrolling problem
 *  Version 0.5
 *  - separate css style for current selected element and hover element which solve the highlight issue
 *  Version 0.4
 *  - Fix width when the select is in a hidden div   @Pawel Maziarz
 *  - Add a unique id for generated li to avoid conflict with other selects and empty values @Pawel Maziarz
 */
jQuery.fn.extend({
    selectbox: function(options) {
        return this.each(function() {
            new jQuery.SelectBox(this, options);
        });
    }
});


/* pawel maziarz: work around for ie logging */
if (!window.console) {
    var console = {
        log: function(msg) {
        }
    };
}
/* */

jQuery.SelectBox = function(selectobj, options) {

    var opt = options || {};
    opt.inputType = opt.inputType || "input";
    opt.inputClass = opt.inputClass || "selectbox";
    opt.containerClass = opt.containerClass || "selectbox-wrapper";
    opt.hoverClass = opt.hoverClass || "current";
    opt.currentClass = opt.currentClass || "selected";
    opt.groupClass = opt.groupClass || "groupname"; //css class for group
    opt.maxHeight = opt.maxHeight || 200; // max height of dropdown list
    opt.loopnoStep = opt.loopnoStep || false; // to remove the step in list moves loop
    opt.onChangeCallback = opt.onChangeCallback || false;
    opt.onChangeParams = opt.onChangeParams || false;
    opt.debug = opt.debug || false;

    var elm_id = selectobj.id;
    var active = 0;
    var inFocus = false;
    var hasfocus = 0;
    //jquery object for select element
    var $select = jQuery(selectobj);
    // jquery container object
    var $container = setupContainer(opt);
    //jquery input object
    var $input = setupInput(opt);
    // hide select and append newly created elements
    $select.hide().before($input).before($container);


    init();

    $input
    .click(function(){
        if (!inFocus) {
            $container.toggle();
        }
    })
    .focus(function(){
        if ($container.not(':visible')) {
            inFocus = true;
            $container.show();
        }
    })
    .keydown(function(event) {
        switch(event.keyCode) {
            case 38: // up
                event.preventDefault();
                moveSelect(-1);
                break;
            case 40: // down
                event.preventDefault();
                moveSelect(1);
                break;
            //case 9:  // tab
            case 13: // return
                event.preventDefault(); // seems not working in mac !
                jQuery('li.'+opt.hoverClass).trigger('click');
                break;
            case 27: //escape
              hideMe();
              break;
        }
    })
    .blur(function() {
        if ($container.is(':visible') && hasfocus > 0 ) {
            if(opt.debug) console.log('container visible and has focus');
        } else {
            // Workaround for ie scroll - thanks to Bernd Matzner
            if((jQuery.browser.msie && jQuery.browser.version.substr(0,1) < 8) || (jQuery.browser.safari && !/chrome/.test(navigator.userAgent.toLowerCase()))) {
                if(document.activeElement.getAttribute('id').indexOf('_container')==-1){
                    hideMe();
                } else {
                    $input.focus();
                }
            } else {
                hideMe();
            }
        }
    });

    function hideMe() {
        hasfocus = 0;
        $container.hide();
    }

    function init() {
        $container.append(getSelectOptions($input.attr('id'))).hide();
        var width = $input.css('width');
        if($container.height() > opt.maxHeight){
            $container.width(parseInt(width)+parseInt($input.css('paddingRight'))+parseInt($input.css('paddingLeft')));
            $container.height(opt.maxHeight);
        } else $container.width(width);
    }

    function setupContainer(options) {
        var container = document.createElement("div");
        $container = jQuery(container);
        $container.attr('id', elm_id+'_container');
        $container.addClass(options.containerClass);
            $container.css('display', 'none');

        return $container;
    }

    function setupInput(options) {
        var input,
            $input;

        if(opt.inputType == "span"){
            input = document.createElement("span");
            $input = jQuery(input);
            $input.attr("id", elm_id+"_input");
            $input.addClass(options.inputClass);
            $input.attr("tabIndex", $select.attr("tabindex"));
        } else {
            input = document.createElement("input");
            $input = jQuery(input);
            $input.attr("id", elm_id+"_input");
            $input.attr("type", "text");
            $input.addClass(options.inputClass);
            $input.attr("autocomplete", "off");
            $input.attr("readonly", "readonly");
            $input.attr("tabIndex", $select.attr("tabindex")); // "I" capital is important for ie
            // $input.css("width", $select.css("width"));
            }
        return $input;
    }

    function moveSelect(step) {
        var lis = jQuery("li", $container);
        if (!lis || lis.length === 0) return false;
        // find the first non-group (first option)
        firstchoice = 0;
        while(jQuery(lis[firstchoice]).hasClass(opt.groupClass)) firstchoice++;
        active += step;
            // if we are on a group step one more time
            if(jQuery(lis[active]).hasClass(opt.groupClass)) active += step;
        //loop through list from the first possible option
        if (active < firstchoice) {
            ( opt.loopnoStep ? active = lis.size()-1 : active = lis.size() );
        } else if (opt.loopnoStep && active > lis.size()-1) {
            active = firstchoice;
        } else if (active > lis.size()) {
            active = firstchoice;
        }
            scroll(lis, active);
        lis.removeClass(opt.hoverClass);

        jQuery(lis[active]).addClass(opt.hoverClass);
    }

    function scroll(list, active) {
            var el = jQuery(list[active]).get(0);
            list = $container.get(0);

        if (el.offsetTop + el.offsetHeight > list.scrollTop + list.clientHeight) {
            list.scrollTop = el.offsetTop + el.offsetHeight - list.clientHeight;
        } else if(el.offsetTop < list.scrollTop) {
            list.scrollTop = el.offsetTop;
        }
    }

    function setCurrent() {
        var li = jQuery("li."+opt.currentClass, $container).get(0);
        var ar = (''+li.id).split('_');
        var el = ar[ar.length-1];
        if (opt.onChangeCallback){
                $select.get(0).selectedIndex = jQuery('li', $container).index(li);
                opt.onChangeParams = { selectedVal : $select.val() };
            opt.onChangeCallback(opt.onChangeParams);
        } else {
            $select.val(el);
            $select.change();
        }
        if(opt.inputType == 'span') $input.html(jQuery(li).html());
        else $input.val(jQuery(li).html());
        return true;
    }

    // select value
    function getCurrentSelected() {
        return $select.val();
    }

    // input value
    function getCurrentValue() {
        return $input.val();
    }

    function getSelectOptions(parentid) {
        var select_options = [];
        var ul = document.createElement('ul');
        select_options = $select.children('option');
        if(select_options.length === 0) {
            var select_optgroups = [];
            select_optgroups = $select.children('optgroup');
            for(x=0;x<select_optgroups.length;x++){
                select_options = jQuery("#"+select_optgroups[x].id).children('option');
                var li = document.createElement('li');
                li.setAttribute('id', parentid + '_' + jQuery(this).val());
                li.innerHTML = jQuery("#"+select_optgroups[x].id).attr('label');
                li.className = opt.groupClass;
                ul.appendChild(li);
                select_options.each(function() {
                    var li = document.createElement('li');
                    li.setAttribute('id', parentid + '_' + jQuery(this).val());
                    li.innerHTML = jQuery(this).html();
                    if (jQuery(this).is(':selected')) {
                        $input.html(jQuery(this).html());
                        jQuery(li).addClass(opt.currentClass);
                    }
                    ul.appendChild(li);
                    jQuery(li)
                    .mouseover(function(event) {
                        hasfocus = 1;
                        if (opt.debug) console.log('over on : '+this.id);
                        jQuery(event.target, $container).addClass(opt.hoverClass);
                    })
                    .mouseout(function(event) {
                        hasfocus = -1;
                        if (opt.debug) console.log('out on : '+this.id);
                        jQuery(event.target, $container).removeClass(opt.hoverClass);
                    })
                    .click(function(event) {
                        var fl = jQuery('li.'+opt.hoverClass, $container).get(0);
                        if (opt.debug) console.log('click on :'+this.id);
                        jQuery('li.'+opt.currentClass, $container).removeClass(opt.currentClass);
                        jQuery(this).addClass(opt.currentClass);
                        setCurrent();
                        $select.get(0).blur();
                        hideMe();
                    });
                });
            }
        } else select_options.each(function() {
            var li = document.createElement('li');
            li.setAttribute('id', parentid + '_' + jQuery(this).val());
            li.innerHTML = jQuery(this).html();
            if (jQuery(this).is(':selected')) {
                $input.val(jQuery(this).html());
                jQuery(li).addClass(opt.currentClass);
            }
            ul.appendChild(li);
            jQuery(li)
            .mouseover(function(event) {
                hasfocus = 1;
                if (opt.debug) console.log('over on : '+this.id);
                jQuery(event.target, $container).addClass(opt.hoverClass);
            })
            .mouseout(function(event) {
                hasfocus = -1;
                if (opt.debug) console.log('out on : '+this.id);
                jQuery(event.target, $container).removeClass(opt.hoverClass);
            })
            .click(function(event) {
                var fl = jQuery('li.'+opt.hoverClass, $container).get(0);
                if (opt.debug) console.log('click on :'+this.id);
                jQuery('li.'+opt.currentClass, $container).removeClass(opt.currentClass);
                jQuery(this).addClass(opt.currentClass);
                setCurrent();
                $select.get(0).blur();
                hideMe();
            });
        });
        return ul;
    }



};
