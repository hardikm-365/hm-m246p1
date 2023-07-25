define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    function main(config) {
        var AjaxUrl = config.AjaxUrl;
        var proid = config.Proid;
        var imgWrap = "";
        var imgArray = [];

        $('.upload__inputfile').each(function () {
        $(document).on('change','.upload__inputfile',function() {
            var filesAmount = this.files.length;
            console.log(filesAmount);
            const file = this.files[0];
            //console.log(file);
            var data = {
                'proid' : proid
            };

            imgWrap = $(this).closest('.upload__box').find('.upload__img-wrap');
            var maxLength = $(this).attr('data-max_length');

            var files = this.files;
            var filesArr = Array.prototype.slice.call(files);
            var iterator = 0;

            $.ajax({
                showLoader: true,
                url: AjaxUrl,
                data: data,
                type: "POST",
                dataType: 'json'
            }).done(function (response) {
                //readURL(this);
                /*if (file){
                    let reader = new FileReader();
                    reader.onload = function(event){
                        $('#imgPreview').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }*/
                filesArr.forEach(function (f, index) {

                    if (!f.type.match('image.*')) {
                        return;
                    }

                    if (imgArray.length > maxLength) {
                        return false
                    } else {
                        var len = 0;
                        for (var i = 0; i < imgArray.length; i++) {
                            if (imgArray[i] !== undefined) {
                                len++;
                            }
                        }
                        if (len > maxLength) {
                            return false;
                        } else {
                            imgArray.push(f);

                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var html = "<div class='upload__img-box'><div style='background-image: url(" + e.target.result + ")' data-number='" + $(".upload__img-close").length + "' data-file='" + f.name + "' class='img-bg'><div class='upload__img-close'></div></div></div>";
                                console.log(html);
                                $('.upload__img-wrap').append(html);
                                iterator++;
                            }
                            reader.readAsDataURL(f);
                        }
                    }
                });
            }).fail(function (response){
                console.log("error");
            });
        });
        });
    };
    return main;
});