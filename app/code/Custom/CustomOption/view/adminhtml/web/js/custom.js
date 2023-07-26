define([
        'jquery',
        'underscore',
        ], function (
            $,
            _
        ) {
            function main(config) {
				/*console.log("test");
				console.log(config.Count);
				console.log(config.OptionCount);*/
				var optionvalue = config.OptionCount.replace("[","").replace("]","").split(',');
				//console.log(optionvalue);
				//var input = "input[name='product[options][";
				var value = "][values][";
				var image = "][image]']";
				var hiddenimage = "][image_hidden]']";
				var customurl = "http://202.131.107.107:8016/hm/garagedoor/pub/admin/product/upload/image";
				// for (i = 0; i < config.Count; i++) {
					$.each(optionvalue, function (key, val) {
						//console.log(key);
						for (j = 0; j < val; j++){
							var input = "input[name='product[options][" + key + "][values][" + j + "][option_image]']";
							//console.log(key);
							console.log(input);
							var h_image = "input[name='product[options][" + key + "][values][" + j + "][image_hidden]']";
							console.log(h_image);
							// console.log(("input[name="'product[options][' + key + '][values][' + j + '][qty]'"]").val());
							$(document).on('change',input,function() {
								var input_name = this.name;
								var hidden_image = input_name.replace("image", "image_hidden");
								/*console.log(input_name);
								console.log(hidden_image);
								console.log("input[name='" + hidden_image + "']");*/
								var hidden_image_name = "input[name='" + hidden_image + "']";
								$(hidden_image_name).val($(this).val());
								 var file = $(this).val();
								 var name = this.name;
								 var file1 = this.files[0]['name'];
								 console.log(this.name);
								 $.ajax({
						            url: customurl,
						             type: 'POST',
						             dataType: 'json',
						             data: {
						                 tmp_name: name						                 
						             },
						             complete: function(response) {             

						                 console.log( response.responseText );   
						             },
						             error: function (xhr, status, errorThrown) {
						                 console.log('Error happens. Try again.');
						             }
						         });
								/*console.log(key);
								console.log(val);
								console.log(j);
								 console.log(h_image);*/
						   		//alert($(this).val());
						   	});
						}
					});
				// }
				

			};
			return main;
		});