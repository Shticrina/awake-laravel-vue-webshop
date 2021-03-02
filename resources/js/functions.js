
// Refreshing shopping cart
function refreshShoppingCart(items) {
	
	// console.log('items in refreshShoppingCart: ', items);
    let $content = $("#cartContent");
    $content.empty();

	if (!items || items.length == 0) {
		let $p = $('<p></p>');
		$p.text("Your shopping cart is empty");
		$p.appendTo($content);
		$('#cartButtons').addClass('d-none');
		$('#js-nav-basket-nbitems').html('(0)');
	} else {
		$('#js-nav-basket-nbitems').html('('+items.length+')');
		$('#cartButtons').removeClass('d-none');

		let $ul = $('<ul class="shopping-cart-items p-0"></ul>');
		let $h5 = $('<h5 class="text-dark text-right my-3"></h5>');
		let total_price = 0;

		for (let i = 0; i < items.length; i++) {	
			let color = items[i].color ? items[i].color : '';
			let size = items[i].size ? items[i].size : '';
			let image = items[i].image ? items[i].image : '';
			let virgule = color == '' || size == '' ? '' : ', ';
			let $li = $('<li class="px-2 d-flex justify-content-between mb-2 bor-bot"></li>');
			let $div1 = $('<div class=""></div>');
			let $img = $('<img class="w-100"/>');

			$img.attr('src', image);
			$img.attr('alt', items[i].name);
			$img.appendTo($div1);

			let $div2 = $('<div class="col-9"></div>');
			let $h6 = $('<h6 class="text-success mt-2">'+items[i].name+'<span class="text-secondary"> ('+color+virgule+size+')</span></h6>');
			let $divflex = $('<div class="d-flex justify-content-between text-dark"></div>');
			let $p1 = $('<p></p>');
			let $p2 = $('<p class="font-weight-bold"></p>');
			$p1.text('Qty: '+items[i].quantity);
			$p2.text(items[i].price.toFixed(2)+' €');
			$p1.appendTo($divflex);
			$p2.appendTo($divflex);
			$h6.appendTo($div2);
			$divflex.appendTo($div2);

			$div1.appendTo($li);
			$div2.appendTo($li);
			$li.appendTo($ul);

			total_price += items[i].quantity * items[i].price;
		}

		$ul.appendTo($content);
		$h5.text('Total price: '+total_price.toFixed(2)+' €');
		$h5.appendTo($content);
	}
}

function validateForm(model, schema, formId) {
	let validation_errors = {};
    let groups = schema.groups;

    let field_names = Object.keys(model);
	let rules2 = ["email", "string", "alphanumeric", "numeric"];

    $('#'+formId+' .reactive').removeClass('is-invalid');
	$('#'+formId+' .invalid-feedback').remove();

	for (let group in groups) {
		let wrap_divs = groups[group].wrap_divs;

		for (let item in wrap_divs) {
			let wrap_div = wrap_divs[item];
			let fields = wrap_div.fields;
			// console.log('fields: ', fields); // ok

			for (let item in fields) {
				let field = fields[item];
				let field_name = field.name;
				let field_value = model[field.model]; // fara shipping
		        let field_rules = field.rules ? field.rules.reverse() : null;

		        if (field_rules != null && field_rules.length > 0) {
			        for (let field_rule in field_rules) {
			        	let rule = field_rules[field_rule];

						if (rule == "required") {
							let functionCallResult = functionObject['required'](field_value);

							if (functionCallResult == false) {
								validation_errors["required-"+field_name] = "This field is required!";
							}
						}

						if (rule.match(/^(same_as:){1}[\w]+$/) != null) { // regex for password (same as password_confirmation)
							let comparing_field = getSecondPart(rule, ":"); // password_confirmation

							if (field_names.indexOf(comparing_field) !== -1) {
								let comparing_value = model[comparing_field];
								let functionCallResult = functionObject['same_as'](field_value, comparing_value);

								if (functionCallResult == false && functionObject['required'](field_value) == true || comparing_value != "" && field_value == "") {
									validation_errors["wrong-"+field_name] = "Password do not match Confirm Password!";
									// validation_errors["wrong-"+comparing_field] = "Password do not match Confirm Password!";
								}
							}
						} else if (rules2.indexOf(rule) !== -1) { // ["string", "alphanumeric", "numeric", "email"]
							let functionCallResult = functionObject[rule](field_value);

							if (functionCallResult == false && functionObject['required'](field_value) == true) {
								validation_errors["wrong-"+field_name] = "This field is invalid!";
							}
						} else if (rule.match(/^(min:|max:){1}[0-9]+$/) != null) { // regex for min, max
							let rule_type = getFirstPart(rule, ":"); // min or max
			    			let number = getSecondPart(rule, ":"); // number
							let functionCallResult = functionObject[rule_type](field_value, number);

							if (functionCallResult == false && functionObject['required'](field_value) == true) {
								validation_errors["wrong-"+field_name] = (rule_type == "min" ? "Minimum " : "Maximum ")+number+" characters!";
							}
						}

			        }
			    }
		    }
		}
    }

	if (Object.keys(validation_errors).length > 0) {
    	bindValidationErrors(validation_errors, formId);
    }

    return validation_errors;
}

function bindValidationErrors(errors, formId) {
	for (let error in errors) {
    	let error_type = getFirstPart(error, "-");
    	let field_name = getSecondPart(error, "-");
		let $input = $('#'+formId+' input[name="'+field_name+'"]');
		let $error_div = $('<div class="invalid-feedback"></div>');

		$input.addClass('is-invalid');
		$error_div.text(errors[error]);
		$input.parent().append($error_div);
	}
}

function getFirstPart(str, separator) {
    return str.split(separator)[0];
}

function getSecondPart(str, separator) {
    return str.split(separator)[1];
}

var functionObject = {
	required: function(value) { return value !== ''; },
	same_as: function(value1, value2) { return value1 == value2; },
	string: function(value) { return value.match(/^[a-zA-Z0-9\_\-\'!@#$%^&*;:¨]+$/i) != null; },
	min: function(field, value) { return field.length >= value; },
	max: function(field, value) { return field.length <= value; },
	alphanumeric: function(value) { return value.match(/^(\w)+[a-zA-Z0-9\s,\_\-\']*(\w)+$/i) != null; },
	numeric: function(value) { return !isNaN(parseFloat(value)) && value.match(/^[0-9]+$/) != null; },
	email: function(value) { return value.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) != null; }
};

export { refreshShoppingCart, validateForm };