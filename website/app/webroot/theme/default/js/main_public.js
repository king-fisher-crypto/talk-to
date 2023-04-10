var phoneboxnotationview = 1;

var nxMain = {
	tuto_close: 0,
	widget_agent_offset: 0,
    hideScene: !1,
    flashmsgs: !0,
    activity_timer: 0,
    busyagents: 0,
    busyagents_interval: 7e3,
    autoRefresh: !0,
	limitexpert: 20,
	limitagents: 35,
	limitreview: 40,
	limitnb:1,
    debug: !1,
    lastTerm: "",
    agentListTimer: 0,
    agentListTimerRefreshInterval: 4e4,
    agentListFilters: {
        id_category: 0,
		categories: [],
        media: [],
        term: "",
        term_novalue: "",
        orderby: "",
        filterby: "",
        page: 1
    },
    translate: {
        subscribe1: "Veuillez saisir votre numéro de téléphone.",
        subscribe2: "Veuillez renseigner l'indicatif pays de votre numéro de téléphone."
    },
    nxPopup: 0,
    initSubscription: function() {
        var t = $("#UserSubscribeForm");
        t.submit(function() {
            return "" != t.find("#UserPhoneNumber").val() && "" == t.find("#UserIndicatifPhone").val() ? (alert(nxMain.translate.subscribe2), !1) : "" == t.find("#UserPhoneNumber").val() && "" != t.find("#UserIndicatifPhone").val() ? (alert(nxMain.translate.subscribe1), !1) : !0
        })
    },
    getURLParameter: function(t) {
        for (var i = window.location.search.substring(1), e = i.split("&"), n = 0; n < e.length; n++) {
            var a = e[n].split("=");
            if (a[0] == t) return a[1]
        }
        return !1
    },
    tooltipShow: function(t) {
        t.tooltip("show"), nxMain.tooltipHide(t)
    },
    tooltipHide: function(t) {
        clearTimeout(nxMain.tt_timer), nxMain.tt_timer = setTimeout(function() {
            $(".hover_tooltip").tooltip("hide")
        }, 2500)
    },
    init: function() {
        this.initSubscription(), $(".nxselect").each(function() {
            nxMain.nxSelectify($(this))
        }), $(".table_products").click(function() {
            $(".hover_tooltip").tooltip({
                trigger: "manual"
            }).on("click", nxMain.tooltipShow($(".hover_tooltip")))
        }), this.initModals(), this.initMenu(), this.initAgentListFilters(), this.initCategorySearch(), this.initChat(), this.initEmail(), this.initPhone(), this.initProductsLogin(), this.initActivity(), this.debug && this.initDebug(), this.initFlashMsg();
        var t = this.getURLParameter("filter");
        t && ("chat" == t ? $("label[for=sf_media_chat]").click() : "phone" == t ? $("label[for=sf_media_phone]").click() : "email" == t && $("label[for=sf_media_email]").click())
    },
    initFlashMsg: function() {
        if (!this.flashmsgs) return !1;
        var t = $("div.alert");
        if (t.length) {
            t.find(".close").remove();
            var i = t.html(),
                e = t.attr("class");
            t.remove(), $("body").append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title" id="myModalLabel"><img src="' + t.attr("data-logo") + '" alt="' + t.attr("data-site") + '" title="' + t.attr("data-site") + '" style="height: 35px !important; margin-right: 10px;">  </h4></div><div class="modal-body"><div class="' + e + '" style="font-size:16px; margin:0">' + i + "</div><div></div>"), $("#myModal").modal({
                backdrop: !0,
                keyboard: !0,
                show: !0
            })
        }
    },
    hideCmsSubScene: function() {
        $("#cms_container").hide(), this.hideScene = !0
    },
    hideCmsCategory: function() {
        $("#cat_description").hide(), this.hideScene = !0
    },
	hideTutoCategory: function() {
        $("#tuto_container").hide();
		nxMain.tuto_close = 1;
    },

    initActivity: function() {
        nxMain.activity_timer = setTimeout(function() {
            nxMain.iA_refreshAgentsCounter(); nxMain.iA_refreshAgentsPhoneBox();
        }, nxMain.busyagents_interval)
    },
    iA_refreshAgentsCounter: function() {
        nxMain.ajaxRequest("/home/ajaxactivity", {}, function(t) {
            nxMain.busyagents != t.busy_agents && ($("#box_subscribe span.txt_orange").html(t.busy_agents), nxMain.busyagents = t.busy_agents), nxMain.activity_timer = setTimeout(function() {
                nxMain.iA_refreshAgentsCounter();nxMain.iA_refreshAgentsPhoneBox();
            }, nxMain.busyagents_interval)
        })
    },
	iA_refreshAgentsPhoneBox: function() {
        nxMain.ajaxRequest("/phones/hassession", {}, function(t) {
			if($('.phonebox').size() == 0 && t.html != ''){
				phoneboxnotationview =1;
				$('.phoneboxnote').remove();
			}
			$('.phonebox').remove();
			$('body').append(t.html);
			$('body').find('.phonebox .name').html(t.phone_com_title);
			$('body').find('.phonebox .cb_time_left').html(t.phone_com_time);
			$('.phonebox').draggable();
			

			if($('.phoneboxnote').size() == 0 && phoneboxnotationview ==1 && t.phone_note_title != ''){
				nxMain.ajaxRequest("/phones/hasclientnotes", {}, function(t) {
					$('.phoneboxnote').remove();
					$('body').append(t.html);
					$('body').find('.phoneboxnote .name').html(t.phone_note_title);
					$('body').find('.phoneboxnote .content').val(t.phone_note_text);
					//$('body').find('.phoneboxnote #phone_note_birthday').val(t.phone_note_birthday);
					$('body').find('.phoneboxnote #phone_note_birthday_day option[value="'+t.phone_note_birthday_day+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_month option[value="'+t.phone_note_birthday_month+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_year option[value="'+t.phone_note_birthday_year+'"]').prop('selected', true);
					
					$('body').find('.phoneboxnote #phone_note_sexe option[value="'+t.phone_note_sexe+'"]').prop('selected', true);
					$('body').find('.phoneboxnote .nx_openlightbox_note').attr('param',t.phone_id_client);					
					//$('body').find('.phoneboxnote .nx_openlightbox_note').remove();
					if(t.phone_note_text == 'Notes impossibles.'){
						$('body').find('.phoneboxnote .content').prop('readonly', true);
					}else{
						$('body').find('.phoneboxnote .content').prop('readonly', false);
					}
					$('body').find('.phoneboxnote #phone_note_call').val(t.phone_note_call);
					$('body').find('.phoneboxnote #phonenoteagent').val(t.phone_note_agent);
					$('.phoneboxnote').draggable();
				});
			}
		})
    },
    initModals: function() {
        $(".nx_openinlightbox").unbind("click").click(function() {
            return nxMain.openUrlInModal($(this).attr("href")), !1
        }), $(".nx_openlightbox").unbind("click").click(function() {
            return nxMain.openModal($(this).attr("href"), $(this).attr("param")), !1
        }), $(".nx_openlightboxinterne").unbind("click").click(function() {
            return nxMain.openModal(linklinkval($(this).find('.linklink').html()), $(this).attr("param")), !1
        })
    },
    initMenu: function() {
        $(window).scroll(function() {
            $(this).scrollTop() <= 50 ? $(".container_fixed").removeClass("fixed_effect") : $(".container_fixed").addClass("fixed_effect")
        }), $("#navigation ul .parent_title").unbind("hover").hover(function() {
            $(".sub_cat").hide(), $("span.parent_title").removeClass("selected"), $(this).parent("li").find(".sub_cat").each(function() {
                $(this).parent("li").find("span.parent_title").addClass("selected"), $(this).show()
            })
        }), $(".sub_cat").unbind("mouseleave").mouseleave(function() {
            $(this).hide(), $("span.parent_title").removeClass("selected")
        }), $("#track_header2").unbind("mouseleave").mouseleave(function() {
            $(".sub_cat").hide(), $("span.parent_title").removeClass("selected")
        })
    },
    initEmail: function() {
        $(".nx_emailbox").unbind("click").click(function() {
            var t = $(this).attr("href");
            return nxMain.ajaxRequest(t, {}, function(i) {
                i["return"] === !1 ? ($("#myModal").remove(), $("body").append(i.html), $("#myModal").modal({
                    backdrop: !0,
                    keyboard: !0,
                    show: !0
                })) : i["return"] === !0 && (document.location.href = t)
            }), !1
        }),
		$(document).find(".nx_emailboxinterne").unbind("click").click(function() {
            var t = linklinkval($(this).find('.linklink').html());
            return nxMain.ajaxRequest(t, {}, function(i) {
                i["return"] === !1 ? ($("#myModal").remove(), $("body").append(i.html), $("#myModal").modal({
                    backdrop: !0,
                    keyboard: !0,
                    show: !0
                })) : i["return"] === !0 && (document.location.href = t)
            }), !1
        })
    },
    initProductsLogin: function() {},
    initPhone: function() {
        /*$(".nx_phonebox").unbind("click").click(function() {
            if (undefined != $(this).attr("href")) var t = $(this).attr("href");
            else var t = $(this).find(".ae_phone_href").html();
            return nxMain.ajaxRequest(t, {
                id: $(this).find(".ae_phone_param").html()
            }, function(t) {
                t["return"] === !0 ? ($("#myModal").remove(), $("body").append(t.html), $("#myModal").modal({
                    backdrop: !0,
                    keyboard: !0,
                    show: !0
                })) : t["return"] === !1 && (void 0 !== t.msg && alert(t.msg), document.location.reload())
            }), !1
        })*/
    },
    initDebug: function() {
        $('<div id="debug_nx"></div>').appendTo("body")
    },
    refreshFilters: function() {
        if (this.debug) {
            var t = "filters:<br/>";
            for (i in nxMain.agentListFilters)
                if ("[object Array]" === Object.prototype.toString.call(nxMain.agentListFilters[i])) {
                    t += "--- " + i + ":<br/>";
                    for (j in nxMain.agentListFilters[i]) t += " ------ " + j + ": " + nxMain.agentListFilters[i][j] + "<br/>"
                } else t += "--- " + i + ":" + nxMain.agentListFilters[i] + "<br/>";
            $("#debug_nx").html(t)
        }
    },
    getFiltersPostDatas: function() {
        var t = new nxMain.cloneObject(nxMain.agentListFilters);
        t.ajax_for_agents = 1;
		if($("#numPage").val() != ''){
			var i = $("#numPage").val();
		}else{
			var i = 1;
		}
		
		t.limitAgents = nxMain.limitagents;

		return t.page = i, t.categories = nxMain.agentListFilters.categories, nxMain.debug && (t.debug = 1), t.term = $("form#filters_form input[name=sf_term]").val(), t.term_novalue == t.term && (t.term = ""), "" == t.term && "" != $("#filters_form_mobile #sf_term_mobile").val() && (t.term = $("#filters_form_mobile #sf_term_mobile").val()), t
    },
    eventsAgentListInit: function() {
        this.initModals(), this.initChat(), this.initEmail(), this.initPhone(), $(".nxtooltip").tooltip(), $("#agents_list .ae_presentation").click(function() {
            var t = $(this).parents(".ae_txts").find(".ae_pseudo a").attr("href");
            document.location.href = t
        }), $(".aeb_audio .icon-expert-audio").click(function() {
            nxMain.showPresentation($(this).find(".agent_audio_url").html(), $(this).find(".agent_audio_audio").html(), $(this).find(".agent_audio_pseudo").html())
        }), $(".icon-link").click(function() {

            var tt = $(this).parent().find(".icon_url").html();
			if($(this).parent().find(".icon_url").hasClass('action-box-alerte'))
			nxMain.openUrlInModal(tt);
			else{
				$(this).parent().find(".icon_url").attr('href',tt);
				$(this).parent().find(".icon_url").click();
			}
        })
    },
    showPresentation: function(t, i, e) {
        $("#myModal").remove(), nxMain.ajaxRequest(t, {
            audio: i,
            pseudo: e
        }, function(t) {
            $("body").append(t), $("#myModal").modal({
                backdrop: !0,
                keyboard: !0,
                show: !0
            }), $("#myModal").on("hidden.bs.modal", function() {
                $("#myModal").remove()
            })
        }, "html")
    },
    initChat: function() {
       /* $(".nx_chatbox").unbind("click").click(function() {
            return $("#myModal").modal("hide"), nxMain.ajaxRequest($(this).attr("href"), {}, function(t) {
                if (t["return"] === !1) switch (t.typeError) {
                    case "login":
                        $("#myModal").remove(), $("body").append(t.value), $("#myModal").modal({
                            backdrop: !0,
                            keyboard: !0,
                            show: !0
                        });
                        break;
                    case "noCustomer":
                    case "create":
                        alert(t.value);
                        break;
                    case "noCredit":
                    case "chat":
                        nxMain.openModal(t.value, t.param);
                        break;
                    case "missParam":
                    case "noAgent":
                        document.location.href = t.value
                } else t["return"] === !0 && void 0 !== t.session && nx_chat.init(t.url, t.session, t.otherUrl, t.lastIdMsg, t.lastIdEvent)
            }), !1
        })*/
    },
    initCategorySearch: function() {
        var t = $("form#filters_form"),
            i = $("form#filters_form_mobile");
        nxMain.agentListFilters.term_novalue = t.find("input[name=sf_term_novalue]").val(), nxMain.eventsAgentListInit(), t.find("input[name=sf_media]").change(function() {
          /*  nxMain.agentListFilters.media.splice(0,nxMain.agentListFilters.media.length);//patch empty filtre media*/
		    if ($(this).is(":checked") === !0) nxMain.agentListFilters.media.push($(this).val());
            else {
                var t = $(this).val();
                nxMain.agentListFilters.media.splice(nxMain.agentListFilters.media.indexOf(t), 1)
            }
            $("#numPage").attr("value", 1), nxMain.refreshFilters(), nxMain.callAjaxQuery(!0)
        }), i.find("#sf_orderby_mobile").change(function() {
            "phone" == $(this).val() || "email" == $(this).val() || "chat" == $(this).val() ? nxMain.agentListFilters.media.push($(this).val()) : nxMain.agentListFilters.orderby = $(this).val(), $("#numPage").attr("value", 1), nxMain.refreshFilters(), nxMain.callAjaxQuery(!0)
        }), t.find("#sf_orderby").change(function() {
            var t = $(this).attr("id").replace("sf_", "");
            "orderby" == t && "default" != $(this).val() && (nxMain.agentListFilters[t] = $(this).val()), nxMain.refreshFilters(), nxMain.callAjaxQuery(!0)
        }), t.find("#sf_filterby").change(function() {
            var t = $(this).attr("id").replace("sf_", "");
            nxMain.agentListFilters[t] = $(this).val(), $("#numPage").attr("value", 1), nxMain.refreshFilters(), nxMain.callAjaxQuery(!0)
        }), i.find("select").change(function() {
            var t = $(this).attr("id").replace("sf_", "").replace("_mobile", "");
            nxMain.agentListFilters[t] = $(this).val(), $("#numPage").attr("value", 1), nxMain.refreshFilters(), nxMain.callAjaxQuery(!0)
        }), $("form#filters_form").submit(function() {
            var i = t.find("input[name=sf_term]").val();
            return "" != nxMain.lastTerm || i != nxMain.agentListFilters.term_novalue && "" != i ? (nxMain.lastTerm = i, nxMain.agentListFilters.term = i, nxMain.refreshFilters(), $("#numPage").attr("value", 1), clearTimeout(nxMain.agentListTimer), nxMain.callAjaxQuery(!0), !1) : ($(".tooltip_noterm").tooltip("show"), setTimeout(function() {
                $(".tooltip_noterm").tooltip("hide")
            }, 2e3), !1)
        }), $("#filters_form_mobile").submit(function() {
            var t = i.find("#sf_term_mobile").val();
            return nxMain.lastTerm = t, nxMain.agentListFilters.term = t, nxMain.refreshFilters(), $("#numPage").attr("value", 1), clearTimeout(nxMain.agentListTimer), nxMain.callAjaxQuery(!0), !1
        }),
		$(document).find(".filtre-category a").click(function() {	
				if($(this).hasClass('active')){
					nxMain.agentListFilters.categories.splice(nxMain.agentListFilters.categories.indexOf($(this).html()), 1);
				}else{
					nxMain.agentListFilters.categories.push($(this).html());
				}
				$('html, body').animate({
					scrollTop: $("#search_filters").offset().top -90
				}, 800);
			
				nxMain.refreshFilters(), nxMain.callAjaxQuery(!0);	
			
			}),
		$(document).find(".filtre-addy a").click(function() {	
				var domclic = $(this);
				$(document).find(".filtre-addy a").each(function() {
					if($(this).find(".rel").html() != domclic.find(".rel").html())
					$(this).removeClass('active');
				});
			
			    nxMain.agentListFilters['orderby'] = '';
			    nxMain.agentListFilters['filterby'] = '';
				$("#numPage").val('1');
				
				if(!domclic.hasClass('active')){
					 var tt = domclic.find(".type").html();
					//console.log(tt);
					nxMain.agentListFilters[tt] = domclic.find(".rel").html();
					//console.log(nxMain.agentListFilters[tt]);
				}
				$('html, body').animate({
					scrollTop: $("#search_filters").offset().top -90
				}, 800);
			
				nxMain.refreshFilters(), nxMain.callAjaxQuery(!0);	
			
			})
    },
    initAgentListFilters: function() {
        var t = $("form#filters_form");
		nxMain.agentListFilters.id_category = $(document).find('#category_id_inf').html();
        t.find("input[name=sf_media]").each(function() {
            $(this).is(":checked") === !0 && nxMain.agentListFilters.media.push($(this).val())
        }), t.find("select").each(function() {
            var t = $(this).attr("id").replace("sf_", "");
			("orderby" == t && "default" != $(this).val() || "filterby" == t && "allagents" != $(this).val()) && (nxMain.agentListFilters[t] = $(this).val())
        })
		, $(document).find(".filtre-addy a").each(function() {
            var domclic = $(this);
			if(domclic.hasClass('active')){
					 var tt = domclic.find(".type").html();
					nxMain.agentListFilters[tt] = domclic.find(".rel").html();
				}
        })
    },
    callAjaxQuery: function(t) {
		
		nxMain.agentListFilters.id_category = $(document).find('#category_id_inf').html();
        clearTimeout(nxMain.agentListTimer), void 0 != t && 1 == t  && $("#agents_list").prepend( "<div class=\"loading\"><div class=\"loader\"><i class=\"fa fa-spinner\"></i><span class=\"sr-only\">Chargement...</span></div></div><!--loader END-->" ) , nxMain.ajaxRequest($("form#filters_form").attr("action"), nxMain.getFiltersPostDatas(), function(i) {
            $("#agents_list").html(i.html), $("#tuto").html(i.tuto),  nxMain.autoRefresh && (nxMain.agentListTimer = setTimeout(function() {
				
                nxMain.ajaxUpdateAgentList(nxMain.agentListFilters.id_category);
				$("#agentlistempty").show();
				
            }, nxMain.agentListTimerRefreshInterval)), nxMain.eventsAgentListInit(), 0 == nxMain.hideScene && ("" == $("#tuto").html() ? ($("#cat_description").show()) : ($("#cat_description").hide()));
			//console.log(i.count +' > ' + nxMain.limitagents);
			/*if(i.count > nxMain.limitagents){
				$("#agents_list").append('<div class="pagination-mobile"><span class="paginate-button"> <i class="fa fa-angle-down"></i> Afficher plus d\'experts</span></div>');
			}*/
			//void 0 != t && 1 == t  && ("" == $("#tuto").html() || nxMain.tuto_close == 1 ? $("#tuto_container").hide() : $("#tuto_container").show()) ,
        }, "json");
		
    },

    openModal: function(t, i) {
        var e = {};
        e = void 0 === i ? {
            isAjax: 1
        } : {
            param: i
        }, $("#myModal").remove(), this.ajaxRequest(t, e, function(t) {
            void 0 !== t.html ? ($("body").append(t.html), $("#myModal").modal({
                backdrop: !0,
                keyboard: !0,
                show: !0
            }), void 0 != t.chat && 1 == t.chat && nxMain.initChat()) : "usernovalid" === t["return"] && void 0 !== t.msg && alert(t.msg)
        }, "json")
    },
    openUrlInModal: function(t) {
        $("#myModal").remove(), this.ajaxRequest(t, [], function(t) {
            "usernovalid" === t["return"] && void 0 != t.msg || ($("body").append(nxMain.getTwitterModalHtmlTemplate(t.title, t.content, t.button)), $("#myModal").modal({
                backdrop: !0,
                keyboard: !0,
                show: !0
            }))
        }, "json")
    },
    modalLoading: function() {
        $("#myModal").remove();
        var t = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
        t += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">', t += '<h4 class="modal-title" id="myModalLabel">' + title + "</h4>", t += "</div>", t += '<div class="modal-body">' + content + "</div>", t += "</div></div></div>", $("body").append(), $("#myModal").modal({
            backdrop: !0,
            keyboard: !1,
            show: !0
        })
    },
    initAgentList: function(t, i) {
        nxMain.agentListFilters.id_category = t,nxMain.ajaxUpdateAgentList(t), nxMain.agentListTimerRefreshInterval = i, nxMain.autoRefresh && (nxMain.agentListTimer = setTimeout(function() {
           nxMain.ajaxUpdateAgentList(t);refreshtooltip();
        }, nxMain.agentListTimerRefreshInterval, i))
    },
    ajaxUpdateAgentList: function(t) {
        nxMain.callAjaxQuery();
    },
    getTwitterModalHtmlTemplate: function(t, i, e) {
        var n = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
        return n += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>', n += '<h4 class="modal-title" id="myModalLabel">' + t + "</h4>", n += "</div>", n += '<div class="modal-body">' + i + "</div>", n += '<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">' + e + "</button>", n += "</div></div></div></div>"
    },
    ajaxRequest: function(t, i, e, n) {
		
		/*t = t.replace('http:','https:');
		var count = (t.split('http').length - 1);
		if(count == 0){
			t = location.protocol+ "//" +location.host + t;
		}*/
		
		
        void 0 == n && (n = "json"), $.ajax({
            type: "POST",
            dataType: n,
            url: location.protocol+ "//" +location.host + t,
            data: i,
            success: function(t) {
                void 0 != e && e(t)
            },
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if (XMLHttpRequest.readyState == 4) {
					// HTTP error (can be checked by XMLHttpRequest.status and XMLHttpRequest.statusText)
				}
				else if (XMLHttpRequest.readyState == 0) {
					// Network error (i.e. connection refused, access denied due to CORS, etc.)
					e(false);
				}
				else {
					// something weird is happening
				}
			}
        })
    },
    cloneObject: function(t) {
        for (i in t) "source" == typeof t[i] ? this[i] = new this.cloneObject(t[i]) : this[i] = t[i]
    },
    nxSelectify: function(t) {
        t.hide();
        var i = "nxselect_" + t.attr("id");
        $('<div id="' + i + '" class="nxselect_div"></div>').insertBefore(t);
        var e = '<div class="nxselect_selected"><span class="nxs_icon"></span> ' + t.find(":selected").html() + '</div><div class="nxselect_list">';
        t.parents("form").find("label[for=" + t.attr("id") + "]").addClass("nxselect_label"), void 0 !== t.parents("form").find("label[for=" + t.attr("id") + "]").html() && (e += '<div class="nxselect_title">' + t.parents("form").find("label[for=" + t.attr("id") + "]").html() + "</div>"), e += "<ul>", t.find("option").each(function(i) {
            var n = $(this).attr("icon");
            e += "<li " + (n ? 'hascustomicon="1"' : "") + ' rel="' + $(this).val() + '"' + ($(this).val() == t.find(":selected").val() ? ' class="nxli_selected"' : "") + '><i class="nxli_icon glyphicon' + (n ? " glyphicon-" + n : 0 == i ? " glyphicon-ok" : "") + '"></i>' + $(this).html() + "</li>"
        }), e += "</ul></div>", $("#" + i).html(e), this.nxSelectifyEvents(t, $("#" + i))
    },
    nxSelectOpen: function(t) {
        t.addClass("opened")
    },
    nxSelectClose: function(t) {
        t.removeClass("opened")
    },
    nxSelectSwitch: function(t) {
        t.hasClass("opened") ? nxMain.nxSelectClose(t) : nxMain.nxSelectOpen(t)
    },
    nxSelectifyEvents: function(t, i) {
        i.hover(function() {
            nxMain.nxSelectOpen($(this))
        }, function() {
            nxMain.nxSelectClose($(this))
        }), i.find("li").hover(function() {
            $(this).hasClass("nxli_selected") || 1 != !$(this).attr("hascustomicon") || $(this).find(".nxli_icon").addClass("glyphicon-ok"), $(this).find(".nxli_icon").css({
                opacity: .5
            })
        }, function() {
            $(this).hasClass("nxli_selected") || 1 != !$(this).attr("hascustomicon") || $(this).find(".nxli_icon").removeClass("glyphicon-ok"), $(this).find(".nxli_icon").css({
                opacity: 1
            })
        }), $("label.nxselect_label").unbind("click").click(function() {
            nxMain.nxSelectSwitch($("div#nxselect_" + $(this).attr("for")))
        }), i.find("li").click(function() {
            i.find("li").removeClass("nxli_selected"), $(this).addClass("nxli_selected"), i.find(".nxli_icon").removeClass("glyphicon-ok"), 1 == !$(this).attr("hascustomicon") && $(this).find(".nxli_icon").addClass("glyphicon-ok"), t.val($(this).attr("rel")), t.change(), i.find(".nxselect_selected").html('<span class="nxs_icon"></span>' + t.find('option[value="' + $(this).attr("rel") + '"]').html())
        })
    }
};
$(document).ready(function() {
    nxMain.init(), nxMain.initAgentList(1, 8e3);
	
	test_cookie();

    $.cookieBar(), $(".show_hide").show(), $(".hide_btn").css("display", "none"), $(".show_hide").bind( "click",function(e) {//,SocialShareKit.init()
		e.preventDefault();
		/*$("html, body").animate({ scrollTop: 0 }, 600);*/
        $(".slidingDiv").slideToggle("fast", function() {
            "none" == $(".slidingDiv").css("display") ? ($(".hide_btn").css("display", "none"), $(".show_btn").css("display", "inline-block")) : ($(".hide_btn").css("display", "block"), $(".show_btn").css("display", "none"))
        })
		 
		return;
    }), $(".nav_categories").on("click", "a", function() {
        $(".nav_categories").find(".active").removeClass("active")
    }), $("body").on("DOMNodeInserted", function(t) {
        $(t.target).is(".chatbox_agent") && ($.playSound("https://www.talkappdev.com/media/sonnerie/0159"), document.getElementById("audiosonnerie").stop(),document.getElementById("audiosonnerie").play())
    })
	
	
	$(document).on("click", ".experts-online .pagination a", function(e) {
		e.preventDefault();
		nxMain.ajaxRequest("/pages/widgetlisting", {page:$(this).html()}, function(t) {
				//$('.experts-online').html(t.html);	
				$(".experts-online").fadeOut(function() {
				  $(this).html(t.html).fadeIn();
				});
			});
	});
	
	$(document).on("click", ".widget-experts-online .expert-arrow-right", function(e) {
		nxMain.widget_agent_offset = nxMain.widget_agent_offset +1;
		if(nxMain.widget_agent_offset > 0){
			$('.expert-arrow-left').css('display', 'block');		
		}
		var url = "/pages/widgetbottomlisting";
		if($(".content-horo").size() > 0){
			var url = "/horoscopes/widgetbottomlisting";		
		}
		nxMain.ajaxRequest(url, {page:nxMain.widget_agent_offset}, function(t) {
			if(t.html != ''){
				$(".widget-experts-online .online-list").fadeOut(function() {
				  $(this).html(t.html).fadeIn();
				});
				//$('.widget-experts-online .online-list').html(t.html);
			}else{
				$('.expert-arrow-right').css('display', 'none');	
			}
			if(t.stopright == 1){
				$('.expert-arrow-right').css('display', 'none');		
			}	
		});
	});
	
	$(document).on("click", ".widget-experts-online .expert-arrow-left", function(e) {
		nxMain.widget_agent_offset = nxMain.widget_agent_offset - 1;
		$('.expert-arrow-right').css('display', 'block');
		if(nxMain.widget_agent_offset == 0){
			$('.expert-arrow-left').css('display', 'none');	
					
		}
			nxMain.ajaxRequest("/pages/widgetbottomlisting", {page:nxMain.widget_agent_offset}, function(t) {
				//$('.widget-experts-online .online-list').html(t.html);	
				$(".widget-experts-online .online-list").fadeOut(function() {
				  $(this).html(t.html).fadeIn();
				});
			});
	});
	
	$(document).on("click", "#promo_live", function(e) {
		
		if($("#code_promo").val() == ''){
			alert('Merci de renseigner un code promo');
		}else{
			var produit_select = parseInt($(document).find("input#produit").val());
			
			nxMain.ajaxRequest("/products/promolive", {code:$("#code_promo").val()}, function(t) {
				if(t.error != ''){
					alert(t.error);
				}else{
					if(!t.is_promo_total){					
						$('.pricing-page-container').html(t.html);	
						//$("#code_promo").css('visibility','hidden');
						$("#code_promo").css('display','none');
						$("#promo_live").css('display','none');
						$(".voucher_box").find('p.small').html(t.promo+' : '+t.promo_title);
						
						if(produit_select)
						preselect_product(produit_select);
						
						$("html, body").animate({ scrollTop: 0 }, 600);
						//$("table_mobile_products.").offset().top -160}, 800);
						//$('<p class="promotitlevalid">'+t.promo_title+'</p>').appendTo('.voucher_box');
					}else{
						
						autovalidproduct(0);
							
					}
				}
			});	
		}
	});
	$(document).on("click", "#promo_reset", function(e) {
		
			var produit_select = parseInt($(document).find("input#produit").val());
			
			nxMain.ajaxRequest("/products/promoreset", {}, function(t) {
				if(t.error != ''){
					alert(t.error);
				}else{
						$('.pricing-page-container').html(t.html);	
						//$("#code_promo").css('visibility','hidden');
						$(".voucher_done_box").remove();
						//$(".voucher_box").find('p.small').html(t.promo+' : '+t.promo_title);
						
						if(produit_select)
						preselect_product(produit_select);
						
						$("html, body").animate({ scrollTop: 0 }, 600);
					
				}
			});	
	});
	if($("#UserSubscribeAgentForm").size() > 0 ){
		$("#UserSubscribeAgentForm").validate({
			focusInvalid: false,
			ignore: "#UserPhoto",
			invalidHandler: function(form, validator) {
		
				if (!validator.numberOfInvalids())
					return;
		
				$('html, body').animate({
					scrollTop: $(validator.errorList[0].element).offset().top -100
				}, 2000);
		
			}
		});
		//$.validator.messages.required = "";
	}
	
	$( document).on( "click", "label[for=sf_media_email]", function() {
			  if( $("#sf_media_email").is(':checked') ){
			//$("#sf_media_email").attr('checked', false);
				 $("label[for=sf_media_email]").removeClass("active");
			} else {
				//$("#sf_media_email").attr('checked', true);
				$("label[for=sf_media_email]").addClass("active");
				$('html, body').animate({
					scrollTop: $("#tuto_container").offset().top -160
				}, 800);
			}
		});
	$( document).on( "click", "label[for=sf_media_phone]", function() {
			  if( $("#sf_media_phone").is(':checked') ){
			//$("#sf_media_email").attr('checked', false);
				 $("label[for=sf_media_phone]").removeClass("active");
			} else {
				//$("#sf_media_email").attr('checked', true);
				$("label[for=sf_media_phone]").addClass("active");
				$('html, body').animate({
					scrollTop: $("#tuto_container").offset().top -160
				}, 800);
			}
		});
	$( document).on( "click", "label[for=sf_media_chat]", function() {
			  if( $("#sf_media_chat").is(':checked') ){
			//$("#sf_media_email").attr('checked', false);
				 $("label[for=sf_media_chat]").removeClass("active");
			} else {
				//$("#sf_media_email").attr('checked', true);
				$("label[for=sf_media_chat]").addClass("active");
				$('html, body').animate({
					scrollTop: $("#tuto_container").offset().top -160
				}, 800);
			}
		});
		
	
	if($("#myModalRedir").size() > 0){
		jQuery("#myModalRedir").detach().prependTo('.navbar-custom .container');
		$(".navbar-custom").css('z-index','2147483647');
		$(".navbar-header").css('z-index','-1');
		$(".navbar-header").css('position','relative');
		$(".navbar-header").css('opacity','0.4');
		$(".navbar-right").css('z-index','-1');
		$(".navbar-right").css('position','relative');
		$(".navbar-right").css('opacity','0.4');
		
		
		$("#myModalRedir").modal({
		  escapeClose: false,
		  clickClose: false,
		  showClose: false,
		  backdrop: 'static', keyboard: false
		});
	}
	
	$(document).on("click", ".reponde-avis .vote span", function(e) {
		var obj = $(this).parent().find('.vote-number');
			nxMain.ajaxRequest("/reviews/reviewutile", {id:obj.attr('rel')}, function(t) {
				if(t.error != ''){
					alert(t.error);
				}else{
					if(t.number){			
						obj.html(t.number);
					}
				}
			});	
	});
	
	$(document).on("click", ".phone_login_phra_slide", function(e) {
		e.preventDefault();
		$(this).parent().parent().find('.phone_login_phra').hide();
		$(this).parent().parent().find('.container_phone_login_block').slideDown('slow');
		
	});
	$(document).on("click", ".planningmobile_moreinfo", function(e) {
		e.preventDefault();
		$(this).parent().parent().find('tr').slideDown('slow');
		$(this).parent().hide('fast');
	});
	
	$(document).on("hover", "", function(e) {
		if($("#expertplanning_empty").size() > 0){
			$("#expertplanning_empty").hide();	
		}
	});
	
	$( "#a_planning .block-agenda" ).hover(
	  function() {
		if($("#expertplanning_empty").size() > 0){
			$("#expertplanning_empty").hide();	
		}
	  }, function() {
		if($("#expertplanning_empty").size() > 0){
			$("#expertplanning_empty").show();	
		}
	  }
	);
	$(document).on("click", "#avisClients .review_stop", function(e) {
		e.preventDefault();
		$( this ).nextAll().addClass( "msg_show" ).removeClass( "msg_hide" );
		$(this).remove();
		$('#avisClients li.review_stop').first().removeClass("msg_show").nextAll().removeClass( "msg_show" ).addClass( "msg_hide" );
	});
	
	if($('.clock').size()>0){
		$('.clock').countdown($('.clock').attr('rel'), function(event) {
		  $(this).html(event.strftime('%D jour(s) %Hh:%Mmin:%Ssec'));
		});
	}
	if($('.clock_mobile').size()>0){
		$('.clock_mobile').countdown($('.clock_mobile').attr('rel'), function(event) {
		  $(this).html(event.strftime('%D jour(s) %Hh:%Mmin:%Ssec'));
		});
	}
	if($('.clock_min').size()>0){
		$('.clock_min').countdown($('.clock_min').attr('rel'), function(event) {
		  $(this).html(event.strftime('%Mmin:%Ssec'));
			setInterval(function() { checkcountdown(); }, 3000);
		});
	}
	if($('#correspond').size()>0){
		stepnext(1);


		$('.step-2-next').click(function(){
			$('.stepwizard-step.stepwizard-step-2 a').addClass('step-active');
		});
	}
	
	if($('.saymore').size()>0){
		/*$('.saymore').click(function(e){
			
			$('html, body').animate({
					scrollTop: $("#cat_description").offset().top -90
				}, 800);
		});*/
	}
	
	$(document).on("click", ".accordeon", function(e) {
		e.preventDefault();
		if($(this).hasClass('acc-plus')){
			$(this).removeClass('acc-plus').addClass('acc-minus');
			$(this).next().removeClass('txt-acc-hide').addClass('txt-acc-show');
		}else{
			$(this).removeClass('acc-minus').addClass('acc-plus');
			$(this).next().removeClass('txt-acc-show').addClass('txt-acc-hide');
		}
	});
	
	 $(document).on("click", ".checkvu", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
            var url = $(this).attr('href');
            var idComm = $(this).attr('comm');
		 	var line = $(this);//.parent().parent();


                //On envoie la requête
                nxMain.ajaxRequest(url, {id_comm: idComm}, function(json){
                    if(json.return == false){
                        if(json.url !== undefined){
                            //Redirection
                            document.location.href = json.url;
                        }
                    }else if(json.return == true){
                        line.hide();
                    }
                },'json');

        });
	
	if($("#dis-call").size()>0){
		setTimeout(function() {
            refreshModeConsultAgent($("#dis-call").find('.dis-call-rel').html());
        }, 5000);
	}
	
	$('.fb-share').click(function(e) {
        e.preventDefault();
		$('meta[property="og:description"]').attr("content", $(this).attr('rel'));
        window.open($(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });
	
	$(document).on("click", ".share-fb", function(e) {
		$('.agent-sharing').slideDown();
	});
	
	//$(document).on("click", ".btn_share", function(e) {
	$('.btn_share').click(function(e) {
        e.preventDefault();	
            var params = {
                'og:url': $(this).attr('href'),
                'og:title': 'agents : Avis client conçernant '+$('.consult-name h2').html(),
            };
            var description = $(this).parent().parent().parent().parent().find('.reviews-content').html().trim().replace(/(<([^>]+)>)/ig,"");
            var image = $('meta[property="og:image"]').attr("content");
            if (description.length) {
                params['og:description'] = description;
            }
            if (image.length) {
                params['og:image'] = image.replace('.jpg','_min.jpg');
            }
			params['og:image:height'] = 200;
			params['og:image:width'] = 200;
            FB.ui({
                method: 'share_open_graph',
                action_type: 'og.shares',
                display: 'popup',
                action_properties: JSON.stringify({ object: params })
            });
			return false;
        });
	$('.btn_like').click(function(e) {
        e.preventDefault();	
			var params = {
                'og:url': $(this).attr('href'),
                'og:title': 'Avis client conçernant '+$('.consult-name h2').html(),
            };
		
            FB.ui({
                method: 'share_open_graph',
                action_type: 'og.likes',
                display: 'popup',
				action_properties: JSON.stringify({ object: params })
            });
			return false;
        });
	$(document).on("click", ".nx_chatbox", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
		 	return $("#myModal").modal("hide"), nxMain.ajaxRequest($(this).attr("href"), {}, function(t) {
                if (t["return"] === !1) switch (t.typeError) {
                    case "login":
                        $("#myModal").remove(), $("body").append(t.value), $("#myModal").modal({
                            backdrop: !0,
                            keyboard: !0,
                            show: !0
                        });
                        break;
                    case "noCustomer":
                    case "create":
                        alert(t.value);
                        break;
                    case "noCredit":
                    case "chat":
                        nxMain.openModal(t.value, t.param);
                        break;
                    case "missParam":
                    case "noAgent":
                        document.location.href = t.value
                } else t["return"] === !0 && void 0 !== t.session && nx_chat.init(t.url, t.session, t.otherUrl, t.lastIdMsg, t.lastIdEvent)
            }), !1
		});
	$(document).on("click", ".nx_chatboxinterne", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
		 	return $("#myModal").modal("hide"), nxMain.ajaxRequest(linklinkval($(this).find('.linklink').html()), {}, function(t) {
                if (t["return"] === !1) switch (t.typeError) {
					case "chatPopup":
						$(document).find("#myModalTchat").remove(), $(document).find("body").append(t.html);
						if($(document).find("#myModalTchat").size() > 0){
						
							$(document).find("#myModalTchat").modal({
									backdrop: !0,
									keyboard: !0,
									show: !0
								});
						}
                        break;
						
                    case "login":
                        $("#myModal").remove(), $("body").append(t.value), $("#myModal").modal({
                            backdrop: !0,
                            keyboard: !0,
                            show: !0
                        });
                        break;
                    case "noCustomer":
                    case "create":
                        alert(t.value);
                        break;
                    case "noCredit":
                    case "chat":
                        nxMain.openModal(t.value, t.param);
                        break;
                    case "missParam":
                    case "noAgent":
                        document.location.href = t.value
                } else t["return"] === !0 && void 0 !== t.session && nx_chat.init(t.url, t.session, t.otherUrl, t.lastIdMsg, t.lastIdEvent)
            }), !1
		});
	$(document).on("click", ".nx_chatboxpopup", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
		 	return $("#myModal").modal("hide"), nxMain.ajaxRequest(linklinkval($(this).parent().find('.linklink').html()), {}, function(t) {
                if (t["return"] === !1) switch (t.typeError) {
                    case "login":
                        $("#myModal").remove(), $("body").append(t.value), $("#myModal").modal({
                            backdrop: !0,
                            keyboard: !0,
                            show: !0
                        });
                        break;
                    case "noCustomer":
                    case "create":
                        alert(t.value);
                        break;
                    case "noCredit":
                    case "chat":
                        nxMain.openModal(t.value, t.param);
                        break;
                    case "missParam":
                    case "noAgent":
                        document.location.href = t.value
                } else t["return"] === !0 && void 0 !== t.session && $("#myModalTchat").modal("hide") && $('.modal-backdrop').remove() && $("#myModalTchat").remove() && nx_chat.init(t.url, t.session, t.otherUrl, t.lastIdMsg, t.lastIdEvent)
            }), !1
		});
	$(document).on("click", ".nx_phonebox", function(e) {
            if (undefined != $(this).attr("href")) var t = $(this).attr("href");
            else var t = $(this).find(".ae_phone_href").html();
            // return nxMain.ajaxRequest(t, {
            //     id: $(this).find(".ae_phone_param").html()
            // }, function(t) {
            //     t["return"] === !0 ? ($("#myModal").remove(), $("body").append(t.html), $("#myModal").modal({
            //         backdrop: !0,
            //         keyboard: !0,
            //         show: !0
            //     })) : t["return"] === !1 && (void 0 !== t.msg && alert(t.msg), document.location.reload())
            // }), !1
        });
	$(document).on("click", ".nx_phoneboxinterne", function(e) {
            if (undefined != linklinkval($(this).find('.linklink').html())) var t = linklinkval($(this).find('.linklink').html());
            else var t = $(this).find(".ae_phone_href").html();
		
			if(!$(this).parent().hasClass('t-busy')){

                return nxMain.ajaxRequest(t, {
                    id: $(this).find(".ae_phone_param").html()
                }, function(t) {
                    t["return"] === !0 ? ($("#myModal").remove(), $("body").append(t.html), $("#myModal").modal({
                        backdrop: !0,
                        keyboard: !0,
                        show: !0
                    })) : t["return"] === !1 && (void 0 !== t.msg && alert(t.msg), document.location.reload())
                }), !1
			}
	});

	
	
	
	if($(".horoscope-page").size() > 0 && $(".horoscope-details").size() == 0){
		$("aside.horoscope").hide();
	}
	$(document).on("click", ".horo-fb img", function(e) {
		var url = $(".horo_share_content_url").html();
		spiritPopup('https://www.facebook.com/share.php?u='+url);
	});
	
	if($(".account-sidebar .experts-online").size() > 0 && $(".expert-name-side").size() == 0){
		var data_center_height = $(".content_box").height()+40;
		if($(".content-horo").size() > 0){
			data_center_height = data_center_height + 140;
		}
		var data_titre_height = $(".account-sidebar .experts-online .widget-title").height()+30;
		
		var data_right_height = $(".account-sidebar .experts-online").height();
		var data_pub_height = 0;
		if($(".horo-pub-top").size() > 0){
			data_right_height = data_right_height + $(".horo-pub-top").height();
			data_pub_height = data_pub_height + 200;//$(".horo-pub-top").height()
		}
		if($(".horo-pub-bottom").size() > 0){
			data_right_height = data_right_height + $(".horo-pub-bottom").height();
			data_pub_height = data_pub_height + 200;//$(".horo-pub-bottom").height()
		}
		var data_vignette_height = $(".account-sidebar .experts-online .online-list li").height()+40;
		if(data_right_height > data_center_height){
			var h_defined = data_center_height - data_titre_height - data_pub_height;//enleve le titre widget
			
			var nb_vignette = Math.ceil(  h_defined / data_vignette_height );
			/*nb_vignette = nb_vignette - 1;
			if(nb_vignette < 1)nb_vignette=1;*/
			var new_height = data_titre_height  + (nb_vignette * data_vignette_height);
			if($(".horo-pub-top").size() > 0){
				new_height = new_height + 20;
			}
			$( ".account-sidebar .experts-online" ).after( '<a class="seemoreexpert" href="/" title="Voir plus d\'experts">Voir plus</a>' );
			$(".account-sidebar .experts-online").css('height',new_height+"px");
			$(".account-sidebar .experts-online").css('overflow-y',"hidden");
		}
		
		/*$(document).on("click", ".seemoreexpert", function(e) {
			$(".account-sidebar .experts-online").css('height',data_right_height+"px");
			$(".account-sidebar .experts-online").css('overflow-y',"visible");
			$(".seemoreexpert").hide();
		});*/
	 }
	
	
	$(document).on("click", ".linkinterne", function(e) {
		var link = linklinkval($(this).find('.linklink').html());
		window.location = link;
	});
	
	$(document).on("click", ".btnscroll", function() {
		var link = $(this).attr('href');
		$('html, body').animate({
					scrollTop: $(link).offset().top -90
				}, 800);
	});
	
	if($("#sf_filterby").size() > 0){
		var selectMenu = document.querySelector('select#sf_filterby');
		var selectMenu2 = document.querySelector('select#sf_orderby');
		var callback_select = function(e) {
		  var selectedOption = $("#sf_filterby").val();//e.target.options[e.target.selectedIndex];
		  window.dataLayer.push({
			event: 'selectionFiltre',
			selectedElement: selectedOption
		  });
		};
		var callback_select2 = function(e) {
		  var selectedOption = $("#sf_orderby").val();//e.target.options[e.target.selectedIndex];
		  window.dataLayer.push({
			event: 'selectionFiltre2',
			selectedElement: selectedOption
		  });
		};
		selectMenu.addEventListener('change', callback_select, true);
		selectMenu2.addEventListener('change', callback_select2, true);
	}
	
	var loop_pageNumber = 2;
	
	jQuery(document).on('click', '.paginate-button', function(e) {
			$(this).parent().remove();
			var loop_count = $(document).find('#loop_page').html();
			var loop_total = $(document).find('#loop_last').html();

			if (loop_pageNumber > loop_total){
				return false;
			}else{
				if($(this).hasClass('expert'))
				loadExperts(loop_pageNumber);
				if($(this).hasClass('review'))
				loadReviews(loop_pageNumber);
				
			}
			loop_pageNumber++;
			if (loop_pageNumber > loop_total)
				jQuery(this).hide();
		});
	$(".removetheprofil").bind( "click", function( e ) {
		
         if (confirm( ". Voulez vous supprimer votre compte ?") == true) {
						
		}else{
			//$(this).attr('href','');
			e.preventDefault();
			e.stopImmediatePropagation();
			e.stopPropagation();
		}
    });
	
	if($('.subscribe_intro_container').size() > 0){
		setInterval(function(){
		   switchTexteSubscribe();
		}, $('.subscribe_intro_timing').html());
	}
		
	
	if($('.buy_col').size()>0){
		jQuery(document).on('click', '.buy_col', function(e) {
			
			$(document).find('.buy_col_active').removeClass('buy_col_active');
			$(this).addClass('buy_col_active');
			/*if($(this).find("#UserSubscribeForm").size()>0){
				$(document).find('.btn-cart-buy').html('S\'inscrire et acheter !');
			}else{
				$(document).find('.btn-cart-buy').html('Se connecter et acheter !');
			}*/
			
			
		});
		/*jQuery(document).on('click', '.buy_col input', function(e) {
			$(document).find('.buy_col_active').removeClass('buy_col_active');
			$(this).parent().parent().parent().parent().addClass('buy_col_active');
			
		});*/
		jQuery(document).on('focus', '.buy_col input', function(e) {
			$(document).find('.buy_col_active').removeClass('buy_col_active');
			$(this).parent().parent().parent().parent().addClass('buy_col_active');
			
		});
	}
	if($('.mode_payment').size()>0){
		jQuery(document).on('click', '.mode_payment', function(e) {
			
			$(document).find('.mode_payment').removeClass('active');
			$(this).addClass('active');
			
			$('html, body').animate({
					scrollTop: $(".btn-cart-buy").offset().top - 540
				}, 800);
			
		});
		
	}
	if($('.card_payment').size()>0){
		jQuery(document).on('click', '.card_payment', function(e) {
			
			$(document).find('.card_payment').removeClass('active');
			$(this).addClass('active');
			
		});
		jQuery(document).on('click', '.stripe_payment_mod', function(e) {
			
			$(document).find('#stripe_cards_default').hide();
			$(document).find('#stripe_cards_other').show();
			$(document).find('#card-errors').html('');
		});
		
		if($('.stripe_payment_mod_activ').size()>0){
			//jQuery(document).find('.stripe_payment_mod').click();	
			//jQuery(document).find('.card_payment_add').click();	
			$(document).find('.linepayement').remove();
			$(document).find('.btn-cart-stripe').remove();
			$(document).find('#stripe_cards_other').remove();
			$(document).find('#stripe_cards_default').remove();
			$(document).find('#stripeCard').remove();
			//$(document).find('#stripeCustomer').val('');
			$(document).find('#form_fieldset_stripe_payment').show();
		}
		
		jQuery(document).on('click', '.source_payment_add', function(e) {
			
			$(document).find('.box-stripe').remove();
			$(document).find('#stripe_bancontact').show();
			bancontact_load();
		});
		
		jQuery(document).on('click', '.card_payment_add', function(e) {
			
			$(document).find('.linepayement').remove();
			$(document).find('.btn-cart-stripe').remove();
			$(document).find('#stripe_cards_other').remove();
			$(document).find('#stripe_cards_default').remove();
			$(document).find('#stripeCard').remove();
			//$(document).find('#stripeCustomer').val('');
			$(document).find('#form_fieldset_stripe_payment').show();
		});
		
		jQuery(document).on('click', '.btn-cart-stripe', function(e) {
			$(this).hide();//attr('disabled','disabled');
			doPaymentStripe();
		});
		
		jQuery(document).on('click', '.remove_content', function(e) {
			
			var card_id = $(this).parent().find('.action').attr('rel');
			if($('.box-stripe-bancontact').size()>0){
				removeStripeBancontact(card_id,$(this).parent());
			}else{
				removeStripe(card_id,$(this).parent());
			}
			
		});
		
	}
	
	
	
	if($('.buy_col').size()>0){
		jQuery(document).on('click', '.buy_col .buy_title_content', function(e) {
			if($(window).width() < 800){
				$(document).find('.buy_col_active').removeClass('buy_col_active');
				$(document).find('.buy_col').find('form').slideUp();
				$(this).parent().addClass('buy_col_active').find('form').slideDown();
			}
		});
		$(document).on("change", "#UserLoginForm #UserPasswd", function() {	
			$('html, body').animate({
					scrollTop: $(".linepayement").offset().top -120
				}, 800);
		});
		$(document).on("change", "#UserSubscribeForm #UserCgu", function() {	
			$('html, body').animate({
					scrollTop: $(".linepayement").offset().top -120
				}, 800);
		});
		 
		
	}
	if($('.btn-cart-buy').size()>0){
		jQuery(document).on('click', '.btn-cart-buy', function(e) {
			$(this).attr('disabled','disabled');
			doPaymentCart();
		});
	}
	
	$("#giftpreviewbtn").bind( "click", function( e ) {
		doShowPdfGift();
    });
	
	$("#quick-access").on( "change", function( e ) {
		$('html, body').animate({
					scrollTop: $($(this).val()).offset().top -90
				}, 800);
    });
	
	$(".link_white.disabled").bind( "click", function( e ) {
		e.preventDefault();
		e.stopImmediatePropagation();
		e.stopPropagation();
		var submenu = $(this).parent().parent().find('.dropdown-submenu');
		if(submenu.css('display') == 'none')
			submenu.slideDown();
		else
			submenu.slideUp();	
    });
	
	
	$(".navbar-myaccount-btn").on( "click", function( e ) {
		if($(this).hasClass('clic')){
			$(this).removeClass('clic')
		}else{
			$(this).addClass('clic')
		}
    });
	
	$(".btntopmenu").on( "click", function( e ) {
		/*if($(document).find('.navbar-myaccount-btn').hasClass('clic')){
			$(document).find('.navbar-myaccount-btn').removeClass('clic')
		}else{
			$(document).find('.navbar-myaccount-btn').addClass('clic')
		}*/
		if($(document).find('.navbar-myaccount-btn').hasClass('clic')){
			$(document).find('.navbar-myaccount-btn').click();
			$(this).click();
		}
    });
	
	
	$(document).find(".navbar-offcanvas-filter").find(".filtre-mode").on( "click", function( e ) {
		var type = $(this).find('.type').html();
		$("label[for="+type+"]").click()
    });
	
	$(document).find(".close_search_filters_mobile").on( "click", function( e ) {
		$(document).find('#offcanvasfilter').removeClass('in');
    });
	
	$(".filter-mobile-menu").on( "click", function( e ) {
		//if(!$(document).find('#offcanvasfilter').hasClass('in')){
			//$(document).find('#offcanvasfilter').addClass('in');
		//}
    });
	
	$('#filterCollapseContent').on( "click", function( e ) {
		if($(document).find('#filterCollapse').css('display') == 'block'){
			$(document).find('#filterCollapse').css('display','none');
		}else{
			$(document).find('#filterCollapse').css('display','block');
		}
    });
	
	$("#btn_box_payment").on( "click", function( e ) {
		if($(document).find('#UserPasswdSubscribe').val() != ''){
			$(document).find('#box_payment').slideDown();
			$(document).find('.box_payment_subscribe').slideUp();
			$(this).hide();
		}else{
			alert('Merci de renseigner tous les champs du formulaire');
		}
    });
	

});

function bindEmailFormCard(){
	$(document).find('#UserSubscribeEmailForm').find('.btn-2-gold').bind( "click",function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
		nxMain.ajaxRequest("/cards/process_subscribe_email", {email: $(document).find('#UserSubscribeEmailForm').find("#UserEmailSubscribe").val(), cardId: $(document).find(".tarot_emailform_id").html()}, function(t) {
			
			if(t.return){
			
				$(document).find('.tarot-result-emailform').hide();
				$(document).find('.tarot-result-content-card').removeClass('card-blur');
				$(document).find('.tarot-result-content-card').find('.txt-blur').addClass('txt').removeClass('txt-blur');
			}else{
				$(document).find('.card_emailform_title').html(t.error);
			}
		});

	});
}

function spiritPopup(url) {
        var width = 575, height = 400,
            left = (document.documentElement.clientWidth / 2 - width / 2),
            top = (document.documentElement.clientHeight - height) / 2,
            opts = 'status=1,resizable=yes' +
                ',width=' + width + ',height=' + height +
                ',top=' + top + ',left=' + left,
          win = window.open(url, '', opts);
        win.focus();
}



function switchFavoris(){
	var href = $(document).find('.star-favorite').attr('href');
	if($(document).find('.star-favorite').hasClass('nofavorite')){
			$(document).find('.fafavorite').css('color','#F8A420');
		var newhref = href.replace('remove_favorite', 'add_favorite');
		$(document).find('.star-favorite').delay(1000).attr('href',newhref);
		//$(document).find('span.name').find('.favorite').css("display","block");
		$(document).find('.star-favorite').removeClass('nofavorite');
		}else{
			$(document).find('.fafavorite').css('color','#000');
			$(document).find('span.name').find('.favorite').css("display","none");
			var newhref = href.replace('add_favorite', 'remove_favorite');
			$(document).find('.star-favorite').delay(1000).attr('href',newhref);
			$(document).find('.star-favorite').addClass('nofavorite');
		}
}

function hypay_cart(){
	nxMain.ajaxRequest("/accounts/cart_buy", {id_cart:$("#cart_id").val()}, function(t) {

			});	

}

function stepnext(n){

    if(n != 0){
		//$(".stepwizard-row a").switchClass('btn-primary','btn-default');
        $(".stepwizard-row a").removeClass('step-active');
        $(".stepwizard-row a").addClass('step-inactive');
		$('.stepwizard a[href="#step-'+n+'"]').tab('show');
		//$('.stepwizard-row a[href="#step-'+n+'"]').switchClass('btn-default','btn-primary');
        $('.stepwizard-row a[href="#step-'+n+'"]').removeClass('step-inactive');
        $('.stepwizard-row a[href="#step-'+n+'"]').addClass('step-active');
    }

}

function stepvalid(action){
	if(action =='voyant'){
		$('.fcompetence .filtre-category').find('.list-group-item').removeClass('active');
		nxMain.agentListFilters.categories = new Array();
		nxMain.agentListFilters.categories.push('Voyants &amp; Mediums');
		$('.fcompetence .filtre-category').find('.filtre-category-5').addClass('active');
		nxMain.refreshFilters();
		nxMain.ajaxUpdateAgentList(nxMain.agentListFilters.id_category)
		stepnext(3);
	}
	if(action =='tarologue'){
		$('.fcompetence .filtre-category').find('.list-group-item').removeClass('active');
		nxMain.agentListFilters.categories = new Array();
		nxMain.agentListFilters.categories.push('Tarologues');
		$('.fcompetence .filtre-category').find('.filtre-category-7').addClass('active');
		nxMain.refreshFilters();
		nxMain.ajaxUpdateAgentList(nxMain.agentListFilters.id_category)
		stepnext(3);
	}
	if(action =='astrologue'){
		$('.fcompetence .filtre-category').find('.list-group-item').removeClass('active');
		nxMain.agentListFilters.categories = new Array();
		nxMain.agentListFilters.categories.push('Astrologues');
		$('.fcompetence .filtre-category').find('.filtre-category-2').addClass('active');
		nxMain.refreshFilters();
		nxMain.ajaxUpdateAgentList(nxMain.agentListFilters.id_category)
		stepnext(3);
	}
	if(action =='numerologue'){
		$('.fcompetence .filtre-category').find('.list-group-item').removeClass('active');
		nxMain.agentListFilters.categories = new Array();
		nxMain.agentListFilters.categories.push('Numerologues');
		$('.fcompetence .filtre-category').find('.filtre-category-6').addClass('active');
		nxMain.refreshFilters();
		nxMain.ajaxUpdateAgentList(nxMain.agentListFilters.id_category)
		stepnext(3);
	}
	if(action =='tous_expert'){
		$('.fcompetence .filtre-category').find('.list-group-item').removeClass('active');
		nxMain.agentListFilters.categories = new Array();
		nxMain.agentListFilters.categories.push('Voyants &amp; Mediums');
		nxMain.agentListFilters.categories.push('Tarologues');
		nxMain.agentListFilters.categories.push('Astrologues');
		nxMain.agentListFilters.categories.push('Numerologues');
		$('.fcompetence .filtre-category').find('.list-group-item').addClass('active');
		nxMain.refreshFilters();
		nxMain.ajaxUpdateAgentList(nxMain.agentListFilters.id_category)
		stepnext(3);
	}
	if(action =='phone'){
		nxMain.agentListFilters.media = new Array('phone');
		$('.filter-icons').find('label').removeClass('active');
		nxMain.agentListFilters.filterby = 'availableagents';
		$("label[for=sf_media_phone]").addClass('active');
		nxMain.callAjaxQuery();
		//$("label[for=sf_media_phone]").click();
		//$("#tuto").html('<h1 style="text-align: left;">Notre sélection pour vous :</h1>');
		$("#cat_description").hide();
		//$("#tuto").show();
		$("#correspond .close").click();
		$('html, body').animate({
					scrollTop: $("#agents_list").offset().top -90
				}, 800);
	}
	if(action =='tchat'){
		nxMain.agentListFilters.media = new Array('chat');
		$('.filter-icons').find('label').removeClass('active');
		nxMain.agentListFilters.filterby = 'availableagents';
		//$("label[for=sf_media_chat]").click();
		$("label[for=sf_media_chat]").addClass('active');
		nxMain.callAjaxQuery();
		//$("#tuto").html('<h1 style="text-align: left;">Notre sélection pour vous :</h1>');
		$("#cat_description").hide();
		//$("#tuto_container").show();
		$("#correspond .close").click();
		$('html, body').animate({
					scrollTop: $("#agents_list").offset().top -90
				}, 800);
	}
	if(action =='email'){
		nxMain.agentListFilters.media = new Array('email');
		$('.filter-icons').find('label').removeClass('active');
		nxMain.agentListFilters.filterby = 'availableagents';
		nxMain.callAjaxQuery();
		//$("label[for=sf_media_email]").click();
		$("label[for=sf_media_email]").addClass('active');
		//$("#tuto").html('<h1 style="text-align: left;">Notre sélection pour vous :</h1>');
		$("#cat_description").hide();
		//$("#tuto_container").show();
		$("#correspond .close").click();
		$('html, body').animate({
					scrollTop: $("#agents_list").offset().top -90
				}, 800);
	}
	if(action =='media'){
		nxMain.agentListFilters.filterby = 'availableagents';
		nxMain.callAjaxQuery();
		//$("#tuto").html('<h1 style="text-align: left;">Notre sélection pour vous :</h1>');
		$("#cat_description").hide();
		//$("#tuto_container").show();
		
		$("#correspond .close").click();
		$('html, body').animate({
					scrollTop: $("#agents_list").offset().top -90
				}, 800);
	}

}

function refreshModeConsultAgent(user_id){
	nxMain.ajaxRequest("/accounts/updatemodeconsult", {id_agent:user_id}, function(t) {
			$("#dis-call").html(t.html);
			$("#mobileexpertbar_content").html(t.mobile_bar);
			
			$(".content_box .status").removeClass("unavailable").removeClass("available").removeClass("busy").addClass(t.set_class_status);
			$(".content_box .status").html(t.set_title_status);	
			nxMain.initEmail();
			nxMain.initPhone();
			nxMain.initChat();
		});	
	setTimeout(function() {
            refreshModeConsultAgent($("#dis-call").find('.dis-call-rel').html());
        }, 5000);
}
function preselect_product(idproduct){
	
	$(document).find("input#produit").val(idproduct);
	
	//desktop
	$(document).find('.price-table').find("button[param='"+idproduct+"']").parent().parent().addClass('selected_product');
	
	//mobile
	$(document).find('.table_mobile_products').find("a[param='"+idproduct+"']").addClass('selected_product');
	
}

function redirBuyCredit(id_product){
	nxMain.ajaxRequest("/accounts/redir_cart_buy", {id_product:id_product}, function(t) {
				
		if(t.redir_url){
			window.location.href = t.redir_url;
		}
	});	
}

function refreshtooltip(){
	$(document).find('[data-toggle="tooltip"]').tooltip();
}

function autovalidproduct(autovalid){
	var cgv   = $(document).find(".cgu_div input#AccountCgu").is(":checked");
				if (!cgv && !autovalid){
					
					$( "#dialog-confirm" ).dialog({
					  resizable: false,
					  height: "auto",
					  width: 400,
					  modal: true,
					  buttons: {
						"Refuser": function() {
						  $( this ).dialog( "close" );
						  return false;
						},
						"Valider": function() {
						  $( this ).dialog( "close" );
						  $(document).find(".cgu_div input#AccountCgu").attr('checked', true);
						  $('#produit').attr('value', '1');
							$( document ).find("form#AccountBuycreditsForm").submit();
						}
					  }
					});
					
					/*if (confirm(nx_select_product.nocgv + ". Voulez vous valider les conditions ?") == true) {
						$(document).find(".cgu_div input#AccountCgu").attr('checked', true);
						 $("form#AccountBuycreditsForm").delay( 800 ).submit();
						
					} else {
						return false;
					}*/
					//alert(nx_select_product.nocgv);
				}else{
					$('#produit').attr('value', '1');
					$(document).find(".cgu_div input#AccountCgu").attr('checked', true);
					$( document ).find("form#AccountBuycreditsForm").submit();
				}
}

function loadExperts(page){
	$("#agents_list").append('<div class=\"loading-pagination\"><i class=\"fa fa-spinner fa-spin  fa-fw\"></i></div>');
	nxMain.limitnb = page;
	nxMain.limitagents = nxMain.limitnb * nxMain.limitexpert;
	nxMain.ajaxUpdateAgentList();
}
function loadReviews(page){
	$(".avis-clients").append('<div class=\"loading-pagination\"><i class=\"fa fa-spinner fa-spin  fa-fw\"></i></div>');
	nxMain.ajaxRequest('/reviews/ajaxLoad', {page:page, ajax_for_reviews:1}, function(t) {
		if(t.html){
			$(".avis-clients .loading-pagination").remove();
			$(".avis-clients .list-unstyled").append(t.html);
			$(".avis-clients").append(t.paginate);
		}
	});
}
function switchTexteSubscribe(){
	
	var block = $('.subscribe_intro_container').find(".subscribe_intro_content_active");
	block.removeClass('subscribe_intro_content_active');
	var blocknext = block.next('.subscribe_intro_content');
	if(blocknext.hasClass('subscribe_intro_content')){
		blocknext.addClass('subscribe_intro_content_active');
	}else{
		$('.subscribe_intro_container').find(".subscribe_intro_content_1").addClass('subscribe_intro_content_active');
	}
}

function doPaymentCart(){
	
	//check si login ou subscribe ou deja co
	var etape_col = false;
	if($('.buy_col > #UserLoginForm').size()>0){
		if($('.buy_col > #UserLoginForm').parent().hasClass('buy_col_active')){
			//ws connexion
			var email = $(document).find('.buy_col.buy_col_active #UserLoginForm').find("#UserEmail").val();
			var pass = $(document).find('.buy_col.buy_col_active #UserLoginForm').find("#UserPasswd").val();
			nxMain.ajaxRequest("/users/login_cart", {email_con:email,passwd_con:pass}, function(t) {
				//console.log(t);
				if(!t.return && t.msg){
					//$(document).find('.cart_box_buy_error').html(t.msg);
					cartFlashMessage(t.msg);
          
				}else{
          //check restricted account
          if(t.is_restricted){
             if($(document).find('.mode_payment_paypal ').hasClass('active')){
               $(document).find('.mode_payment_stripe').addClass('active');
               $(document).find('.mode_payment_paypal').removeClass('active').remove();
             }
          }
          
					PaymentCart();
				}
			});	
			
			
		}else{
			var cgu = false;
			if( $(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserCgu").is(':checked') ){
				var cgu = true;
			}
			//check si juste cgu coché
			if(cgu && $(document).find('.buy_col #UserLoginForm').find("#UserEmail").val() && !$(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserEmailSubscribe").val()){
				//ws connexion
				//console.log('login');
				var email = $(document).find('.buy_col #UserLoginForm').find("#UserEmail").val();
				var pass = $(document).find('.buy_col #UserLoginForm').find("#UserPasswd").val();
				nxMain.ajaxRequest("/users/login_cart", {email_con:email,passwd_con:pass}, function(t) {
					//console.log(t);
					if(!t.return && t.msg){
						//$(document).find('.cart_box_buy_error').html(t.msg);
						cartFlashMessage(t.msg);
					}else{
						PaymentCart();
					}
				});	
			}else{
			
				//ws inscription
				var firstname = $(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserFirstname").val();
				var email = $(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserEmailSubscribe").val();
				var pass = $(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserPasswdSubscribe").val();
				var country = $(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserCountryId").val();

				var subscribe = false;

				if( $(document).find('.buy_col.buy_col_active #UserSubscribeForm').find("#UserOptin").is(':checked') ){
					var subscribe = true;
				}


				if(!cgu || !firstname || !email || !pass ){
					//$(document).find('.cart_box_buy_error').html('Merci de valider les conditions générales d\'utilisations.');
					var messageerror = '';
					if(!firstname) messageerror = messageerror+ 'Merci de renseigner un prénom ou un pseudo.<br />';
					if(!email) messageerror = messageerror+ 'Merci de renseigner un email.<br />';
					if(!pass) messageerror = messageerror+ 'Merci de renseigner un mot de passe.<br />';
					if(!cgu)messageerror = messageerror+ 'Merci de valider les conditions générales d\'utilisations.';
					cartFlashMessage(messageerror);
				}else{
					//console.log('subscribe');
					nxMain.ajaxRequest("/users/subscribe_cart", {firstname:firstname,email:email,pass:pass,country:country,subscribe:subscribe}, function(t) {
						//console.log(t);
						if(!t.return && t.msg){
							//$(document).find('.cart_box_buy_error').html(t.msg);
							cartFlashMessage(t.msg);
						}else{
							PaymentCart();
						}
					});
				}
			}
		}
	}else{
		PaymentCart();
	}
	
}

function PaymentCart(){
	//console.log('payment');
	//check si payment selected
		if(!$(document).find('.mode_payment').hasClass('active')){
			//$(document).find('.cart_box_buy_error').html('Merci de choisir un mode de paiement.');
			cartFlashMessage('Merci de choisir un mode de paiement.');
		}else{
			//executer le paiement / ws generation infos payment
			var dompayment = $(document).find('.mode_payment.active');
			
			if(dompayment.hasClass('mode_payment_coupon')){
				document.location.href = $(document).find('.mode_payment_coupon').find('.action').attr('rel');
			}
			if(dompayment.hasClass('mode_payment_hipay')){
				var urlhipay = "/paymenthipay/getHookPayment";
				if($('#giftorder_id').size()>0){
					urlhipay = "/paymenthipay/getHookPaymentGift";
				}
				nxMain.ajaxRequest(urlhipay, {}, function(t) {
					//console.log(t);
					if(t.html){
						$(document).find('#hipay_form').remove();
						$(document).find('.mode_payment_hipay').find('.action').append(t.html);
						hypay_cart();$(document).find('#hipay_form').submit(); 
						
					}else{
						//$(document).find('.cart_box_buy_error').html('Problème technique avec ce mode de réglement.');
						cartFlashMessage('Problème technique avec ce mode de réglement.');
					}
				});
			}
			if(dompayment.hasClass('mode_payment_paypal')){
				var urlpaypal = "/paymentpaypal/getHookPayment";
				if($('#giftorder_id').size()>0){
					urlpaypal = "/paymentpaypal/getHookPaymentGift";
				}
				nxMain.ajaxRequest(urlpaypal, {}, function(t) {
					//console.log(t);
					if(t.url){
						document.location.href = t.url;
					}else{
						//$(document).find('.cart_box_buy_error').html('Problème technique avec Paypal.');
						cartFlashMessage('Problème technique avec Paypal.');
					}
				});
			}
			if(dompayment.hasClass('mode_payment_bankwire')){
				document.location.href = $(document).find('.mode_payment_bankwire').find('.action').attr('rel');
			}
			if(dompayment.hasClass('mode_payment_stripe')){
				document.location.href = $(document).find('.mode_payment_stripe').find('.action').attr('rel');
			}
			if(dompayment.hasClass('mode_payment_stripe_bancontact')){
				document.location.href = $(document).find('.mode_payment_stripe_bancontact').find('.action').attr('rel');
			}
			if(dompayment.hasClass('mode_payment_stripe_payment')){
				document.location.href = $(document).find('.mode_payment_stripe_payment').find('.action').attr('rel');
			}
			if(dompayment.hasClass('mode_payment_stripe_sepa')){
				document.location.href = $(document).find('.mode_payment_stripe_sepa').find('.action').attr('rel');
			}
		}
}

function cartFlashMessage(msg){
	
	$(document).find('#myModal').remove();
	$(document).find('.btn-cart-buy').removeAttr('disabled');
	$("body").append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title" id="myModalLabel"><img src="/media/logo/default.jpg" alt="Spiriteo" title="Spiriteo" style="height: 35px !important; margin-right: 10px;">  </h4></div><div class="modal-body"><div class="alert alert-warning alert-dismissable" style="font-size:16px; margin:0">' + msg + "</div><div></div>"), $("#myModal").modal({
                backdrop: !0,
                keyboard: !0,
                show: !0
            });
	
	if($('.box_payment_subscribe').size()>0){
		$('.box_payment_subscribe').slideDown();
		$('#box_payment').slideUp();
		$('#btn_box_payment').show();
	}
	
}

function doShowPdfGift(){
	nxMain.ajaxRequest('/gifts/postpdf', {firstname:$("#GiftBeneficiaryFirstname").val(),text:$("#GiftText").val(),value:$("#GiftId").val()}, function(t) {
	});
}

function doPaymentStripe(){
	//$(document).find('.btn-cart-stripe').attr('disabled','disabled');
		if(!$(document).find('.card_payment').hasClass('active')){
			//$(document).find('.cart_box_buy_error').html('Merci de choisir un mode de paiement.');
			cartFlashMessage('Merci de choisir une carte.');
		}else{
			//executer le paiement / ws generation infos payment
			var card_id = $(document).find('.card_payment.active').find('.action').attr('rel');
			$(document).find('#stripeCard').val(card_id);
			if($('.box-stripe-bancontact').size()>0){
				nxMain.ajaxRequest("/paymentbancontact/go_payment", {source_id:card_id}, function(t) {
					//console.log(t);
					//handleServerResponse(t);
				});	
			}else{
				nxMain.ajaxRequest("/paymentstripe/confirm_payment", {payment_method_id:card_id}, function(t) {
					//console.log(t);
					handleServerResponse(t);
				});	
			}
			
			
			//$('#payment-form-stripe').submit();
			
		}
}

function removeStripe(card_id,line){
	
	var cust_id = $(document).find('#stripeCustomer').val();
	nxMain.ajaxRequest("/paymentstripe/remove_card", {customer:cust_id,card:card_id}, function(t) {
					//console.log(t);
					if(!t.return && t.msg){
						//$(document).find('.cart_box_buy_error').html(t.msg);
						cartFlashMessage(t.msg);
					}else{
						line.remove();
					}
				});	
}

function removeStripeBancontact(card_id,line){
	
	var cust_id = $(document).find('#stripeCustomer').val();
	nxMain.ajaxRequest("/paymentbancontact/remove_card", {customer:cust_id,card:card_id}, function(t) {
					//console.log(t);
					if(!t.return && t.msg){
						//$(document).find('.cart_box_buy_error').html(t.msg);
						cartFlashMessage(t.msg);
					}else{
						line.remove();
					}
				});	
}

function test_cookie(){
	document.cookie="cookietest";
	cookiesEnabled=(document.cookie.indexOf("cookietest")!=-1)? true : false;
	document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";
	//if(!cookiesEnabled)alert('Merci d\'activer les cookies de votre navigateur pour accéder à votre compte.');
}
function checkcountdown(){
		if($('.clock_min').size()>0){
			if($('.clock_min').html() == '00min:00sec'){
				window.location.href = "/";
			}
		}
	}

function linklinkval(val){
	
	var url = val.replace("agents par téléphone", "/home/media_phone");
	url = url.replace("agents par tchat - ", "/chats/create_session-");
	url = url.replace("agents par mail - ", "/accounts/new_mail/");
	
	return url;
}