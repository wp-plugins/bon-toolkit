jQuery(function ($) {
    $('#wp-content-wrap .wp-editor-tabs').find('.wp-switch-editor').click(function () {
        var $$ = $(this);
        $('#wp-content-editor-container, #post-status-info').show();
        $('#bon-toolkit-builder').removeClass('closed').hide();
        $('#wp-content-wrap').removeClass('bon-active');
        $('#content-resize-handle').show()
    }).end().prepend($('<a id="content-bon-toolkit-builder" class="hide-if-no-js wp-switch-editor switch-bon-toolkit-builder">' + $('#bon-toolkit-builder h3.hndle span').html() + '</a>').click(function () {
        var $$ = $(this);
        var $el = $('#wp-content-wrap');
        var classes = $el.attr("class").split(" ").filter(function (item) {
            return item.indexOf("active") === -1 ? item : ""
        });
        $el.attr("class", classes.join(" "));
        $('#wp-content-editor-container, #post-status-info').hide();
        $('#bon-toolkit-builder').show().find('> .inside').show();
        $('#wp-content-wrap').addClass('bon-active');
        $(window).resize();
        $('#content-resize-handle').hide();
        return false
    }));
    $('#wp-content-editor-tools .wp-switch-editor').click(function () {
        var $$ = $(this);
        var p = $$.attr('id').split('-');
        $('#wp-content-wrap').addClass(p[1] + '-active')
    });

        // WordPress 4.1 changed the float of the tabs. Reorder them here.
        // After WP 4.3 is released we'll make the new ordering default
        if( $('body').hasClass('branch-4-1') || $('body').hasClass('branch-4-2') ) {
            $( '#wp-content-wrap .wp-editor-tabs #content-bon-toolkit-builder' )
                .appendTo( $( '#wp-content-wrap .wp-editor-tabs' ) );
        }
    $('#bon-toolkit-builder').insertAfter('#wp-content-editor-container').addClass('wp-editor-container').hide().find('.handlediv').remove().end().find('.hndle').remove().end().prepend($('#bon-builder-action'));
	$('.bon-builder-add-elem').click(function (e) {
        e.preventDefault();
        var select_val = $(this).val();
        if (select_val == '') {
            return false
        }
        var nonce_field = $('#bon_toolkit_builder_select_nonce').val();
        var loader = $(this).siblings('.ajax-loader');
        loader.fadeIn();
        $.post(bon_toolkit_builder_ajax.url, {
            action: 'bon_toolkit_builder',
            nonce: nonce_field,
            elem_type: select_val
        }, function (data) {
            var content = $(data);
            $('.bon-builder-selected-elem-wrap #bon-builder-selected-elements').append($(data).hide().fadeIn());
            loader.fadeOut()
        })
    });
    var elem_wrap = $('div#bon-builder-elements');
    var selected_elem_wrap = $('div#bon-builder-selected-elem-wrap');
    elem_wrap.on('click', 'div.action-delete-element', function () {
        var deleted_element = $(this).parents('.bon-builder-element-block');
        var answer = confirm('Are you sure to do this?');
        if (answer) {
            deleted_element.fadeOut('fast', function () {
                $(this).remove()
            })
        }
    });
    elem_wrap.on('click', 'div.action-add-size', function () {
        $(this).btAddSize()
    });
    elem_wrap.on('click', 'div.action-sub-size', function () {
        $(this).btSubSize()
    });
    selected_elem_wrap.find("#bon-builder-selected-elements").sortable({
        forcePlaceholderSize: true,
        placeholder: 'bon-builder-element-placeholder',
    });
    
});
(function ($) {
    $.fn.btSubSize = function () {
        var parents = $(this).parents('.bon-builder-element-block');
        var old_class = '';
        var allowed_size = $(parents).data('allowedsize');
        var can_sub_size = false;
        for (var i = allowed_size.length - 1; i > 0; i--) {
            var has_class = allowed_size[i].key;
            var text = formatText(allowed_size[i - 1].value);
            var new_class = allowed_size[i - 1].key;
            if (parents.hasClass(has_class)) {
                can_sub_size = true;
                old_class = has_class
            }
            if (can_sub_size) {
                if (i > 1) {
                    parents.removeClass(old_class).addClass(new_class);
                    parents.find(".bon-builder-element-size-ruler span").html(text);
                    parents.find(".bon-toolkit-builder-size").val(allowed_size[i - 1].key)
                } else if (i == 1) {
                    parents.removeClass(old_class).addClass(new_class);
                    parents.find(".bon-builder-element-size-ruler span").html(text);
                    parents.find(".bon-toolkit-builder-size").val(allowed_size[i - 1].key)
                }
                break
            }
        }
    }
})(jQuery);
(function ($) {
    $.fn.btAddSize = function () {
        var parents = $(this).parents('.bon-builder-element-block');
        console.log(parents);
        var can_add_size = false;
        var old_class = '';
        var allowed_size = $(parents).data('allowedsize');
        for (var i = 0; i < allowed_size.length - 1; i++) {
            var has_class = allowed_size[i].key;
            var text = formatText(allowed_size[i + 1].value);
            var new_class = allowed_size[i + 1].key;
            if (parents.hasClass(has_class)) {
                can_add_size = true;
                old_class = has_class
            }
            if (can_add_size) {
                if (i < allowed_size.length - 2) {
                    parents.removeClass(old_class).addClass(new_class);
                    parents.find(".bon-builder-element-size-ruler span").html(text);
                    parents.find(".bon-toolkit-builder-size").val(allowed_size[i + 1].key)
                } else if (i == allowed_size.length - 2) {
                    parents.removeClass(old_class).addClass(new_class);
                    parents.find(".bon-builder-element-size-ruler span").html(text);
                    parents.find(".bon-toolkit-builder-size").val(allowed_size[i + 1].key)
                }
                break
            }
        }
    }
})(jQuery);

function formatText(str) {
    n = str.split("/");
    n = parseInt(n[0]) / parseInt(n[1]);
    var multiplier = 100;
    a = n * multiplier;
    if (a % 1 === 0) {
        return a + '%'
    } else {
        return a.toFixed(2) + '%'
    }
}