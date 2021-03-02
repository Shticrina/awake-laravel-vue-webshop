// modify category schema

export default {
	class: "col-12",
	groups: [
		{
			wrap_divs: [
				{
					id: "categoryParent1",
					class: "d-flex flex-column mt-2 text-left",
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
							name: "description",
							type: "text",
							model: "description",
							class: "form-control reactive",
							label: "Description",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["alphanumeric", "max:191"]
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
        id: "submitCategoryButton",
        class: "btn btn-success",
        bindings: {
            model: "",
            if: "",
            click: "",
            event: "update"
        }
    }
};