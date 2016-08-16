(function ($) {
    $.widget("ui.combobox", {
        options: {
            input_class: ''
        },

        _create:function () {
            var input,
                that = this,
                select = this.element.hide(),
                selected = select.children(":selected"),
                value = selected.val() ? selected.text() : "",
                wrapper = this.wrapper = $("<span>")
                    .addClass("ui-combobox")
                    .insertAfter(select);

            this.element.change(function(){
                // fuckin plugin!
                that.destroy();
                that._create();
            });

            function removeIfInvalid(element) {
                var value = $(element).val(),
                    matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(value) + "$", "i"),
                    valid = false;
                select.children("option").each(function () {
                    if ($(this).text().match(matcher)) {
                        this.selected = valid = true;
                        return false;
                    }
                });
                if (!valid) {
                    // remove invalid value, as it didn't match anything
                    $(element)
                        .val("")
                    /*
                     .attr( "title", value + " didn't match any item" )
                     .tooltip( "open" );*/
                    select.val("");
                    setTimeout(function () {
                        input.tooltip("close").attr("title", "");
                    }, 2500);
                    input.data("autocomplete").term = "";
                    return false;
                }
            }

            input = $("<input>")
                .appendTo(wrapper)
                .val(value)
                .attr("title", "")
                .addClass("ui-state-default ui-combobox-input")
                .autocomplete({
                    delay:0,
                    minLength:0,
                    source:function (request, response) {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                        response(select.children("option").map(function () {
                            var text = $(this).text();
                            if (this.value && ( !request.term || matcher.test(text) ))
                                return {
                                    label:text.replace(
                                        new RegExp(
                                            "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete.escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>"),
                                    value:text,
                                    option:this
                                };
                        }));
                    },
                    select:function (event, ui) {
                        ui.item.option.selected = true;
                        select.children("option").each(function () {
                            $(this).removeAttr('selected');
                        });
                        $(ui.item.option).attr('selected', 'selected');
                        select.change();
                        $(this).blur();
                        $(this).focus();
                        that._trigger("selected", event, {
                            item:ui.item.option
                        });
                    },
                    change:function (event, ui) {
// causing problems
//                    if ( !ui.item )
//                        return removeIfInvalid( this );
                    },
                    open:function () {
                        var height = input.autocomplete("widget")[0].offsetHeight;
                        height = height > 250 ? 250 : height;
                        input.autocomplete("widget").css('height', height + 'px').jScrollPane({nopane:true});
                        if( input.hasClass('round-drop-down') ){
                            input.css('background-image', 'url(' + baseUrl + '/images/select_bgHover.png)');
                        }
                    },
                    close:function(){
                        if( input.hasClass('round-drop-down') ){
                            input.css('background-image', 'url(' + baseUrl + '/images/select_bg.png)');
                        }
                    }
                })
                .addClass("ui-widget ui-widget-content ui-corner-left")
                .focus(function(){
                    if( $(that.element).val() == 0 ){
                        $(this).val('');
                    }
                    input.autocomplete("search", "");
                }).blur(function(){
                    if( $(that.element).val() == 0 ){
                        $(this).val($(that.element).find('option:eq(0)').text());
                    }
                });

            input.data("autocomplete")._renderItem = function (ul, item) {
                return $("<li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
            };

            input.addClass(this.options.input_class);

            var a = $("<a>")
                .attr("tabIndex", -1)
                /*.attr( "title", "Show All Items" )
                 .tooltip()*/
                .appendTo(wrapper);
                if( !input.hasClass('round-drop-down') && !input.hasClass('vat-drop-down') ){
                    a.button({
                        icons:{
                            primary:"ui-icon-triangle-1-s"
                        },
                        text:false
                    })
                }
                a.removeClass("ui-corner-all")
                .addClass("ui-corner-right ui-combobox-toggle")
                .click(function () {
                    // close if already visible
                    if (input.autocomplete("widget").is(":visible")) {
                        input.autocomplete("close");
                        removeIfInvalid(input);
                        return;
                    }

                    // work around a bug (likely same cause as #5265)
                    $(this).blur();

                    // pass empty string as value to search for, displaying all results
                    input.autocomplete("search", "");
                    input.focus();
                });

            input
                .tooltip({
                    position:{
                        of:this.button
                    },
                    tooltipClass:"ui-state-highlight"
                });
        },

        destroy:function () {
            this.wrapper.remove();
            this.element.show();
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);