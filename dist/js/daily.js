
(function ($) {

    $.fn.Price= function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init: function () {
                    var that = this;
                    $selObj = $(selObj);
                    $($selObj).keyup(function() {
                       if($.isNumeric($(this).val()) == true){
                           if($(this).attr("id") == "min_price")
                               var formData = {min_price: $(this).val()};
                           else if($(this).attr("id") == "max_price")
                               var formData = {max_price: $(this).val()};
                           $.ajax({
                               url : "ajax.php",
                               type: "POST",
                               data : formData,
                               success: function(data, textStatus, jqXHR) {
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

    $.fn.Switch = function (options) {
        var settings = $.extend({
        }, options);

        var ret = this.each(function () {
            var selObj = this;
            this.sumo = {
                init:function(){
                    var that = this;
                    $selObj = $(selObj);
                    $selObj.change(function() {
                        selObj.sumo.onChange(this);
                    });
                },
                onChange:function(obj){
                    var that = this;
                    var val = $(obj).find("option:selected").val();
                    var formData = {sub_type_id: val}; //Array
                    $.ajax({
                        url : "ajax.php",
                        type: "POST",
                        data : formData,
                        success: function(data, textStatus, jqXHR) {
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
              init:function(){
                var that = this;
                $selObj = $(selObj);
                var finObj = settings.name;
                $finObj = $(settings.name);
                $sub_types = $("#user_sub_types");
                that.action(1);
                $selObj.change(function() {
                  selObj.sumo.onChange(this);
                });
              },

              onChange:function(obj){
                var that = this;
                var val = $(obj).find("option:selected").val();
                that.action(val);
              },

              action: function(index){
                  var sub_type_values = JSON.parse($sub_types.val());
                  var sub_type_values =  sub_type_values.filter(function (person) { return person.supertypeid == index });
                  $finObj.empty();
                  for (var prop in sub_type_values) {
                      $finObj.append($("<option></option>").attr("value",sub_type_values[prop].id).text(sub_type_values[prop].name));
                  }
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
                init: function(){
                    var that = this;
                    $selObj = $(selObj);
                    var slide = $("#slide");
                    $selObj.click(function() {
                        var user_sub_types = JSON.parse($("#user_sub_types").val());
                        $("#sub_type_id option").remove();
                        $prev = JSON.parse($(this).prev().val());
                        for (var prop in user_sub_types) {
                            if($prev.super_type_id == user_sub_types[prop].supertypeid)
                                $("#sub_type_id").append($("<option></option>").attr("value",user_sub_types[prop].id).text(user_sub_types[prop].name));
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