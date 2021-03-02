// modify product schema

export default {
	class: "py-4",
	groups: [
		{
			legend: {
				text: "Main details",
				class: "text-success small-uppercase text-12"
			},
			wrap_divs: [
				{
					id: "productParent1",
					class: "d-flex justify-content-between text-left",
					fields: [
						{
							name: "id",
							type: "hidden",
							model: "id"
						},
						{
							name: "name",
							type: "text",
							model: "name",
							class: "form-control reactive",
							label: "Name",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["alphanumeric", "min:2", "max:191"]
						},
						{
							name: "category",
							type: "select",
							placeholder: "Select category",
							// multiple: "multiple",
							model: "category",
							class: "form-control reactive",
							label: "Category",
							wrap_div_class: "form-group col-3",
							rules: ["required"]
						},
						{
							name: "gender",
							type: "select",
							placeholder: "Select gender",
							model: "gender",
							class: "form-control reactive",
							label: "Gender",
							wrap_div_class: "form-group w-25 mr-3",
							rules: ["required"]
						}
					]
				},
				{
					id: "productParent2",
					class: "d-flex justify-content-between text-left mr-3",
					fields: [
						{
							name: "description",
							type: "textarea",
							rows: "4",
							model: "description",
							class: "form-control reactive",
							label: "Description",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["alphanumeric", "max:191"]
						}
					]
				}
			]
		},
		{
			legend: {
				text: "Product variations",
				class: "mt-4 text-success small-uppercase text-12",
				id: "productVariations"
			},
			wrap_divs: [
				{
					id: "productdetails1",
					class: "d-flex justify-content-between text-left",
					fields: [
						{
							name: "color",
							type: "text",
							model: "color",
							class: "form-control reactive",
							label: "Color",
							wrap_div_class: "form-group flex-fill",
						},
						{
							name: "size",
							type: "text",
							model: "size",
							class: "form-control reactive",
							label: "Size",
							wrap_div_class: "form-group col-3",
						},
						{
							name: "price",
							type: "text",
							model: "price",
							class: "form-control reactive",
							label: "Price",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["required", "numeric", "min:9", "max:14"]
						},
						{
							name: "quantity",
							type: "text",
							model: "units",
							class: "form-control reactive",
							label: "Quantity",
							wrap_div_class: "form-group col-3",
		                    rules: ["required", "numeric", "min:2", "max:191"]
						}
					]
				}
			]
		}
	],
	submit_button: {
        wrap_div_class: "d-flex justify-content-end pr-3",
        value: "Update",
        type: "button",
        id: "submitProductButton",
        class: "btn btn-success"
    }
};