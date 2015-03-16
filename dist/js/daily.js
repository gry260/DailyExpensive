
(function ($) {
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
                $sub_types = $("#sub_types");
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
                        $prev = JSON.parse($(this).prev().val());
                        (($prev.id) ? slide.find("input[name='id']").val($prev.id) : null);
                        (($prev.name) ? slide.find("input[name='name']").val($prev.name) : null);
                        (($prev.amount) ? slide.find("input[name='amount']").val($prev.amount) : null);
                        (($prev.url) ? slide.find("input[name='url']").val($prev.url) : null);
                        (($prev.note) ? slide.find("input[name='note']").val($prev.note) : null);
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