
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
                $finObj.find("option[data!='1']").hide();
                selObj.sumo.previous = $selObj.find("option:selected").val();
                $selObj.change(function() {
                  selObj.sumo.onChange(this);
                });
              },

              onChange:function(obj){
                var that = this;
                var val = $(obj).find("option:selected").val();
                $finObj.find("option[data='"+val+"']").show();
                $finObj.find("option[data='"+val+"']:eq(0)").prop('selected', true);
                $finObj.find("option[data='"+ selObj.sumo.previous+"']").hide();
                selObj.sumo.previous = val;
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
                    $selObj.click(function() {
                        $prev = $(this).prev().val();
                        console.log($prev);
                    });
                }
            }
            selObj.sumo.init();
        });

    }
}(jQuery));