// shipping schema for generate shipping address form

export default {
	class: "col-12",
	groups: [
		{
			wrap_divs: [
				{
					id: "shippingParent1",
					class: "d-flex flex-column mt-2 text-left",
					fields: [
						{
							name: "shipping_address",
							type: "text",
							model: "address",
							class: "form-control reactive",
							label: "Address",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["alphanumeric", "min:2", "max:191"]
						},
						{
							name: "shipping_country",
							type: "text",
							model: "country",
							class: "form-control reactive",
							label: "Country",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["alphanumeric", "max:191"]
						},
                        {
                            name: "shipping_city",
                            type: "text",
                            model: "city",
                            class: "form-control reactive",
                            label: "City",
                            wrap_div_class: "form-group flex-fill",
                            rules: ["alphanumeric", "max:191"]
                        },
                        {
                            name: "shipping_postal_code",
                            type: "text",
                            model: "postal_code",
                            class: "form-control reactive",
                            label: "Postal code (between 4 and 8 digits)",
                            wrap_div_class: "form-group flex-fill",
                            rules: ["numeric", "min:4", "max:8"]
                        }
					]
				}
			]
		}
	],
	submit_button: {
        wrap_div_class: "d-flex justify-content-end",
        value: "Update",
        type: "button",
        class: "btn btn-success",
        bindings: {
            model: "",
            if: "",
            click: "",
            event: "update"
        }
    }
};