// user schema for generate user form

export default {
	// id: "personalDetails",
	class: "px-5 py-4",
	groups: [
		{
			legend: {
				text: "Personal details",
				class: "text-success"
			},
			wrap_divs: [
				{
					id: "parent1",
					class: "d-flex justify-content-between mt-2 text-left",
					fields: [
						{
							name: "name",
							type: "text",
							model: "name",
							class: "form-control reactive",
							label: "Name",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["required", "alphanumeric", "min:2", "max:191"]
						},
						{
							name: "email",
							type: "text",
							model: "email",
							class: "form-control reactive",
							label: "Email",
							wrap_div_class: "form-group flex-fill col-6",
		                    rules: ["required", "email"]
						}
					]
				},
				{
					id: "parent2",
					class: "d-flex justify-content-between text-left",
					fields: [
						{
							name: "phone",
							type: "text",
							model: "phone",
							class: "form-control reactive",
							label: "Phone  (ex: 0475224456 - between 9 and 14 digits)",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["numeric", "min:9", "max:14"]
						},
						{
							name: "address",
							type: "text",
							model: "address",
							class: "form-control reactive",
							label: "Address",
							wrap_div_class: "form-group flex-fill col-7",
		                    rules: ["required", "alphanumeric", "min:2", "max:191"]
						}
					]
				},
				{
					id: "parent3",
					class: "d-flex justify-content-between text-left",
					fields: [
						{
							name: "country",
							type: "text",
							model: "country",
							class: "form-control reactive",
							label: "Country",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["required", "alphanumeric", "max:191"]
						},
						{
							name: "city",
							type: "text",
							model: "city",
							class: "form-control reactive",
							label: "City",
							wrap_div_class: "form-group flex-fill col-4",
		                    rules: ["required", "alphanumeric", "max:191"]
						},
						{
							name: "postal_code",
							type: "text",
							model: "postal_code",
							class: "form-control reactive",
							label: "Postal code (between 4 and 8 digits)",
							wrap_div_class: "form-group flex-fill pr-3",
		                    rules: ["required", "numeric", "min:4", "max:8"]
						}
					]
				}
			]
		},
		{
			legend: {
				text: "Changing password",
				class: "text-success mt-4"
			},
			wrap_divs: [
				{
					id: "parent4",
					class: "d-flex justify-content-between text-left",
					fields: [
						{
							name: "password",
							type: "password",
							model: "password",
							class: "form-control reactive",
							label: "New Password",
							wrap_div_class: "form-group flex-fill",
		                    rules: ["string", "same_as:password_confirmation", "min:6", "max:191"]
						},
						{
							name: "password_confirmation",
							type: "password",
							model: "password_confirmation",
							class: "form-control reactive",
							label: "Confirm Password",
							wrap_div_class: "form-group flex-fill col-6"
						}
					]
				}
			]
		}
	],
	submit_button: {
        wrap_div_class: "d-flex justify-content-end pr-3",
        value: "Modify",
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