(function(a){
	a.fn.extend( {easySelectBox : function(b) {
		var c = {
			className : "easy-select-box",
			speed : 0
		};
		var b = a.extend(c, b);
		return this.each(function() {
			var j = b;
			var k = a(this);
			if ("select" != k[0].nodeName.toLowerCase()) {
				return false
			}
			var d = k.children("option");
			var h = null;
			var i = k.val();
			var g = "easy-select-box-disp";
			var e = 0;
			var f = "";
			a.each(d, function(l, m) {
				f += '<li><a style="cursor:pointer;" rel="' + a(m).val()
						+ '">' + a(m).text() + "</a></li>";
				if (i == a(m).val()) {
					e = l
				}
			});
			f = '<div class="' + j.className
					+ '"><a class="' + g + '" style="cursor:pointer;">'
					+ d.eq(e).text() + "</a><ul>" + f
					+ "</ul></div>";
			h = a(f).insertAfter(k);
			k.hide();
			h.data("o", j);
			easySelectRegistry = a(document).data("easySelect");
			if (easySelectRegistry == null) {
				easySelectRegistry = new Array()
			}
			easySelectRegistry.push(h);
			a(document).data("easySelect",easySelectRegistry);
			h.click(function(l) {
				if (a(h).children("ul").is(":visible")) {
					a(h).children("ul").slideUp(j.speed);
					$(".easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg.png)");
					$(".popup .easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg2.png)");
					$(".invoice .easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg3.png)");
					$(".invoice-small .easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg4.png)");
					h.css("z-index", 998)
				} else {
					easySelectRegistry = a(document).data("easySelect");
					if (easySelectRegistry != null) {
						a.each(easySelectRegistry, function() {
							opts = a(this).data("o");
							if( opts ){
								a(this).children("ul").slideUp(opts.speed);
								a(this).css("z-index",998)
							}
						})
					}
					a(h).children("ul").slideDown(j.speed);
					$(".easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bgHover.png)");
					$(".popup .easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg2.png)");
					$(".invoice .easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg3.png)");
					$(".invoice-small .easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg4.png)");
					h.css("z-index", 999)
				}
				l.stopPropagation();
				return false
			});
			a(document).click(function() {
				easySelectRegistry = a(document).data("easySelect");
				if (easySelectRegistry != null) {
					a.each(easySelectRegistry, function(){
						if (a(this).children("ul").is(":visible")) {
							opts = a(this).data("o");
							//$(".easy-select-box-disp").css("background-image","url(" + baseUrl + "/images/select_bg.png)");
							a(this).children("ul").slideUp(opts.speed)
						}
					})
				}
			});
			
			k.change(function(){
				h.children("." + g).html($(this).children(':selected').text());
			});
			
			h.children("ul").children("li").click(function() {
				k.children("option").removeAttr("selected");
				k.find("option").eq(a(this).index()).attr("selected","selected")
				k.change()
			})
		})
	}
	})
})(jQuery);