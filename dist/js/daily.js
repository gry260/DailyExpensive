Array.prototype.remove = function () {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

function util(data) {
    var xmlDoc = $.parseXML(data);
    var $xml = $(xmlDoc);

    $xml.find("root:first").children().each(function () {
        var that = this;
        if ($(".timeline").find(".time-label[value='" + that.nodeName + "']").length) {
            var body = $(".timeline").find(".time-label[value='" + this.nodeName + "']").next().find(".row");
            var first_child = $(".timeline").find(".time-label[value='" + this.nodeName + "']").next().find(".row").children(":first");
            var lg = $('<div class="col-lg-1" style="margin-bottom: 15px;background-color: #f5f5f5;border: 1px solid #e3e3e3;border-radius: 4px; margin-left:15px;padding:15px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);box-shadow: inset 0 1px 1px rgba(0,0,0,.05);  overflow: hidden;"> </div>');
            body.children().remove();
            var str = '';
            $(that).children().each(function (index) {
                var user_id = $(this).find("user_id").text();
                var note = $(this).find("note").text();
                var amount = $(this).find("amount").text();
                var superid = $(this).find("superid").text();
                var id = $(this).find("id").text();
                var subtypeid = $(this).find("subtypeid").text();
                var url = $(this).find("url").text();
                var date = $(this).find("date").text();
                var name = $(this).find("name").text();
                var paymentid = $(this).find("paymentid").text();

                ((amount) ? first_child.find(".timeline-body .record_amount").text("$" + amount) : null);
                ((note) ? first_child.find(".timeline-body .record_note").text(note) : null);
                ((name) ? first_child.find(".timeline-body .record_url").text(name) : null);
                ((url) ? first_child.find(".timeline-body .record_url").attr("href", url) : null);
                date = date.substring(1);
                date = new Date(1000 * date);
                var date = date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate();

                var obj = {
                    user_id: user_id,
                    note: note,
                    amount: amount,
                    super_type_id: superid,
                    id: id,
                    date: date,
                    name: name,
                    paymentid: paymentid,
                    url: url,
                    sub_type_id: subtypeid
                };
                obj = JSON.stringify(obj);
                first_child.find(".timeline-body #each_record").val(obj);
                str += first_child[0].outerHTML;
            });
            body.append($(str));
        }
    });
    $(".edit_record").editRecord();
    return;
}


function parseXML(xml)
{

}

(function ($) {
    $.fn.lastType = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    $selObj.change(function () {
                        selObj.sumo.onChange(this);
                    });
                },
                onChange: function (obj) {
                    var that = this;
                    var val = $(obj).find("option:selected").val();
                    var formData = {last_type: val};
                    $.ajax({
                        url: "ajax.php",
                        type: "POST",
                        data: formData,
                        success: function (data, textStatus, jqXHR) {

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            //   console.log(errorThrown);
                        }
                    });
                }
            }
            selObj.sumo.init();
        });
    };

    $.fn.reservation = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    $($selObj).click(function () {
                        var start_day = $(".daterangepicker").find(".calendar.left .table-condensed .available.active.start-date").text();
                        var end_day = $(".daterangepicker").find(".calendar.right .table-condensed .available.active.end-date").text();
                        var start_date = $(".daterangepicker").find(".calendar.left .table-condensed thead tr th:eq(1)").text();
                        var end_date = $(".daterangepicker").find(".calendar.right .table-condensed thead tr th:eq(1)").text();
                        var pieces_1 = start_date.split(" ");
                        var pieces_2 = end_date.split(" ");
                        start_date = pieces_1[0] + " " + start_day + ", " + pieces_1[1];
                        end_date = pieces_2[0] + " " + end_day + ", " + pieces_2[1];
                        var formData = {start_date: start_date, end_date: end_date};
                        $.ajax({
                            url: "ajax.php",
                            type: "POST",
                            data: formData,
                            success: function (data, textStatus, jqXHR) {
                                console.log(data);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {

                            }
                        });
                    });
                }
            }
            selObj.sumo.init();
        });
    };

    $.fn.Price = function (options) {

        var $div = $("<div>", { class: "col-lg-1"});
        var $a = $("<a>", { class: "remove_record"});
        var $i = $("<i>", { class: "fa fa-remove"});

        var $timeline = $("<div>", { class: "timeline-body"});

        var $product_info = $("<div>", { class: "product-info"});
        var $product_img = $("<div>", { class: "product-img"});
        var $product_info_2 = $("<div>", { class: "product-info"});
        $product_info.append('<span class="info-box-number record_amount"></span>');
        $product_img.append('<img src="http://placehold.it/50x50/d2d6de" height="100" width="150" alt="Product Image">');
        $product_info_2.append('<h4 class="box-title" style="margin-top:2px; margin-bottom:5px;"><a class="record_url" href="" target="_blank"></a></h4>');
        $product_info_2.append('<span class="product-description record_note"></span><br />');
        var $eachrecord = $("<input>", { class: "each_record", id: "each_record", type:"hidden"});
        $product_info_2.append($eachrecord);
        $product_info_2.append('<a class="btn btn-primary btn-xs slide_open btn btn-danger btn-sm edit_record" id="edit_record" data-popup-ordinal="9">Edit</a>');
        $timeline.append($product_info).append($product_img).append($product_info_2);
        $a.append($i);
        $div.append($a).append($timeline);

        var $tag = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    $($selObj).keyup(function () {
                        if ($.isNumeric($(this).val()) == true && $(this).val().length > 1) {
                            if ($(this).attr("id") == "min_price")
                                var formData = {min_price: $(this).val()};
                            else if ($(this).attr("id") == "max_price")
                                var formData = {max_price: $(this).val()};
                            $.ajax({
                                url: "ajax.php",
                                type: "POST",
                                data: formData,
                                success: function (data, textStatus, jqXHR) {
                                    var xmlDoc = $.parseXML(data);
                                    var $xml = $(xmlDoc);
                                    var last_id = 0;
                                    var last_node = 0;
                                    $xml.find("root:first").children().each(function () {
                                        var that = this;
                                        var currentNode =  $(".timeline").find(".time-label[value='" + this.nodeName + "']");

                                        if($(that).is(":first-child")){
                                            $(".timeline").find(".time-label[value='" + this.nodeName + "']").prevAll().remove();
                                        }

                                        if($(that).is(":last-child")){
                                            $(".timeline").find(".time-label[value='" + this.nodeName + "']").next().nextAll().remove();
                                        }

                                        if(!$(that).is(":first-child")){
                                            $(".timeline").find(".time-label[value='" + last_node + "']").next().nextUntil(currentNode).remove();
                                        }

                                        if (!$(".timeline").find(".time-label[value='" + this.nodeName + "']").length){
                                            var $li = $("<li>", { class: "time-label", value: that.nodeName});
                                            var temp_date = that.nodeName.substring(1);
                                            var date = new Date(temp_date*1000);
                                            var month = parseInt(date.getMonth())+1;
                                            var newString = month + '-' + date.getDate() + '-' + date.getFullYear();
                                            var $span = $('<span class="bg-green">'+newString+'</span>');
                                            $li.append($span);


                                            var $li2 = $("<li>");
                                            var $i2 = $("<i>", { class: "fa fa-user bg-aqua"});
                                            var $div23 = $("<div>", { class: "timeline-item"});
                                            var $timeLineHeader = $('<h3 class="timeline-header"><a href="#.">Support Team</a> ..</h3>');
                                            var $row = $("<div>", { class: "row", id: that.nodeName});
                                            $div23.append($timeLineHeader).append($row);
                                            $li2.append($i2).append($div23);
                                            var app = $li2.html();

                                            if(last_node == 0){
                                                $(".timeline").prepend($li2).prepend($li);
                                            }
                                            else{
                                                $(".timeline").find(".time-label[value='" + last_node + "']").next().after($li2).after($li);
                                            }
                                        }



                                        if ($(".timeline").find(".time-label[value='" + this.nodeName + "']").length) {

                                            $(that).children().each(function (index) {

                                                var user_id = $(this).find("user_id").text();
                                                var note = $(this).find("note").text();
                                                var amount = $(this).find("amount").text();
                                                var superid = $(this).find("superid").text();
                                                var id = $(this).find("id").text();
                                                var subtypeid = $(this).find("subtypeid").text();
                                                var url = $(this).find("url").text();
                                                var date = $(this).find("date").text();
                                                var name = $(this).find("name").text();
                                                var paymentid = $(this).find("paymentid").text();
                                                date = date.substring(1);
                                                date = new Date(1000 * date);
                                                var date = date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate();

                                                var obj = {
                                                    user_id: user_id,
                                                    note: note,
                                                    amount: amount,
                                                    super_type_id: superid,
                                                    id: id,
                                                    date: date,
                                                    name: name,
                                                    paymentid: paymentid,
                                                    url: url,
                                                    sub_type_id: subtypeid
                                                };

                                                var $div = $("<div>", { class: "col-lg-1", id: id});
                                                var $a = $("<a>", { class: "remove_record", id:"remove_record_"+id});
                                                var $i = $("<i>", { class: "fa fa-remove"});

                                                var $timeline = $("<div>", { class: "timeline-body"});

                                                var $product_info = $("<div>", { class: "product-info"});
                                                var $product_img = $("<div>", { class: "product-img"});
                                                var $product_info_2 = $("<div>", { class: "product-info"});
                                                $product_info.append('<span class="info-box-number record_amount">'+amount+'</span>');
                                                $product_img.append('<img src="http://placehold.it/50x50/d2d6de" height="100" width="150" alt="Product Image">');
                                                $product_info_2.append('<h4 class="box-title" style="margin-top:2px; margin-bottom:5px;"><a class="record_url" href="'+url+'" target="_blank">'+name+'</a></h4>');
                                                $product_info_2.append('<span class="product-description record_note">'+note+'</span><br />');
                                                var $eachrecord = $("<input>", { class: "each_record", id: "each_record", type:"hidden"});
                                                var obj = JSON.stringify(obj);


                                                $eachrecord.val(obj);
                                                $product_info_2.append($eachrecord);
                                                $product_info_2.append('<a class="btn btn-primary btn-xs slide_open btn btn-danger btn-sm edit_record" id="edit_record" data-popup-ordinal="9">Edit</a>');
                                                $timeline.append($product_info).append($product_img).append($product_info_2);
                                                $a.append($i);
                                                $div.append($a).append($timeline);


                                                if($(".timeline").find(".time-label[value='" + that.nodeName + "']").next().find(".row").children("#" + $(this).find("id").text()).length > 0){
                                                    var this_record = $(".timeline").find(".time-label[value='" + that.nodeName + "']").next().find(".row").children("#" + $(this).find("id").text());

                                                    if($(this).is(':first-child')){
                                                        this_record.prevAll().remove();
                                                    }

                                                    if($(this).is(':last-child')){
                                                        this_record.nextAll().remove();
                                                    }

                                                    if(!$(this).is(':last-child') || !$(this).is(':first-child')) {
                                                        $(".timeline").find(".time-label[value='" + that.nodeName + "']").next().find(".row").children("#" + last_id).nextUntil(this_record).remove();
                                                    }

                                                }
                                                else{
                                                    if($(this).is(':first-child')){
                                                       $(".timeline").find(".time-label[value='" + that.nodeName + "']").next().find(".row").prepend($div);
                                                    }
                                                    else{
                                                       $div.insertAfter($(".timeline").find(".time-label[value='" + that.nodeName + "']").next().find(".row").children("#" + last_id));
                                                    }
                                                }
                                                last_id = $(this).find("id").text();
                                            });
                                        }
                                        last_node = this.nodeName;
                                    });

                                    $(".edit_record").editRecord();


                                },
                                error: function (jqXHR, textStatus, errorThrown) {

                                }
                            });
                        }
                    });

                }
            }
            selObj.sumo.init();
        });
    };

    $.fn.Switch = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    localStorage.removeItem("datas");
                    $selObj = $(selObj);
                    $selObj.click(function () {
                        selObj.sumo.onChange(this);
                    });
                },
                onChange: function (obj) {
                    var that = this;
                    if ($(obj).attr("aria-selected") === undefined || $(obj).attr("aria-selected") == "false") {

                        if (localStorage.getItem("datas") !== null)
                            var datas = JSON.parse(localStorage["datas"]);
                        else
                            var datas = [];

                        datas.push(parseInt($(obj).attr("value")));
                        localStorage["datas"] = JSON.stringify(datas);
                    }
                    else if ($(obj).attr("aria-selected") == "true") {
                        if (localStorage.getItem("datas") !== null) {
                            var datas = JSON.parse(localStorage["datas"]);
                            datas.remove(parseInt($(obj).attr('value')));
                            localStorage["datas"] = JSON.stringify(datas);
                        }
                    }

                    var rv = {};
                    for (var i = 0; i < datas.length; ++i)
                        rv["sub_type_id_" + i] = datas[i];

                    $.ajax({
                        url: "ajax.php",
                        type: "POST",
                        data: rv,
                        success: function (data, textStatus, jqXHR) {
                            console.log(data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                        }
                    });
                }
            }
            selObj.sumo.init();
        });
    };

    $.fn.Select = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    var finObj = settings.name;
                    $finObj = $(settings.name);
                    $sub_types = $("#user_sub_types");
                    that.action(1);
                    $selObj.change(function () {
                        selObj.sumo.onChange(this);
                    });
                },

                onChange: function (obj) {
                    var that = this;
                    var val = $(obj).find("option:selected").val();
                    that.action(val);
                },

                action: function (index) {
                    var sub_type_values = JSON.parse($sub_types.val());
                    var sub_type_values = sub_type_values.filter(function (person) {
                        return person.supertypeid == index
                    });
                    $finObj.empty();
                    for (var prop in sub_type_values) {
                        if (prop != "remove")
                            $finObj.append($("<option></option>").attr("value", sub_type_values[prop].id).text(sub_type_values[prop].sub_name));
                    }
                }
            }
            selObj.sumo.init();
        });
    };


    $.fn.removeRecord = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    $selObj.click(function () {
                        if ($(this).parent().siblings().length == 0) {
                            $(this).parent().parent().parent().parent().prev().remove();
                            $(this).parent().parent().parent().parent().remove();
                        }
                        else {
                            $(this).parent().remove();
                        }
                        var n = selObj.id.lastIndexOf("_");
                        var res = selObj.id.substring(n + 1, selObj.length);
                        var rv = {"id": res};
                        console.log(rv);
                        $.ajax({
                            url: "removeRecord.php",
                            type: "POST",
                            data: rv,
                            success: function (data, textStatus, jqXHR) {
                                console.log(data);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {

                            }
                        });
                    });
                }
            }
            selObj.sumo.init();
        });
    };

    $.fn.searchRecords = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    $selObj.keyup(function () {
                        if ($(this).val().length > 2) {
                            var text = $(this).val();
                            var rv = {"text": text};
                            $.ajax({
                                url: "ajax.php",
                                type: "POST",
                                data: rv,
                                success: function (data, textStatus, jqXHR) {
                                    console.log(data);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                }
                            });
                        }
                    });
                }
            }
            selObj.sumo.init();
        });
    };

    $.fn.editRecord = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    var slide = $("#slide");
                    $selObj.click(function () {
                        var user_sub_types = JSON.parse($("#user_sub_types").val() || "null");
                        $("#sub_type_id option").remove();
                        $prev = JSON.parse($(this).prev().val());
                        for (var prop in user_sub_types) {
                            if ($prev.super_type_id == user_sub_types[prop].supertypeid)
                                $("#sub_type_id").append($("<option></option>").attr("value", user_sub_types[prop].id).text(user_sub_types[prop].sub_name));
                        }
                        (($prev.id) ? slide.find("input[name='id']").val($prev.id) : null);
                        (($prev.name) ? slide.find("input[name='name']").val($prev.name) : null);
                        (($prev.amount) ? slide.find("input[name='amount']").val($prev.amount) : null);
                        (($prev.url) ? slide.find("input[name='url']").val($prev.url) : null);
                        (($prev.note) ? slide.find("input[name='notes']").val($prev.note) : null);
                        (($prev.date) ? slide.find("input[name='date']").val($prev.date) : null);
                        (($prev.sub_type_id) ? slide.find("select[name='sub_type_id']").val($prev.sub_type_id) : null);
                        (($prev.super_type_id) ? slide.find("select[id='general']").val($prev.super_type_id) : null);
                    });
                }
            }
            selObj.sumo.init();
        });

    }
}(jQuery));