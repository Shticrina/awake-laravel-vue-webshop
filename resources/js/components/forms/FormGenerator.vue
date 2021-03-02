<template>
    <form :id="form_id" method="post" :class="form_schema.class">
        <input type="hidden" name="_token" :value="csrf">

                <!-- <p v-if="details">{{ details.length }}</p> -->
        <template v-if="form_schema.groups" v-for="group in form_schema.groups">
            <h5 v-if="group.legend" :class="group.legend.class" :id="group.legend.id">{{ group.legend.text }}</h5>

            <div v-for="(wrap_div, index) in group.wrap_divs" @key="index" :class="wrap_div.class" :id="wrap_div.id" v-if="wrap_div.id != 'productdetails1'">
                <div v-for="field in wrap_div.fields" :class="field.wrap_div_class">
                    <label :for="field.name">{{ field.label }}</label>

                    <textarea v-if="field.type == 'textarea'" :name="field.name" v-model="form_model[field.model]" :class="field.class" :rows="field.rows"></textarea>

                    <select v-if="field.type == 'select'" v-model="form_model[field.model]" :class="field.class" :placeholder="field.placeholder" :multiple="field.multiple">
                        <option v-if="field.name == 'category' && select_categories" v-for="item in select_categories" :label="item.label" :value="item.value"></option>
                        <option v-if="field.name == 'gender' && select_genders" v-for="item in select_genders" :label="item.label" :value="item.value"></option>
                    </select>

                    <input v-if="field.type == 'text'" :type="field.type" :name="field.name" v-model="form_model[field.model]" :class="field.class">
                </div>
            </div>

            <div v-else>
                <button class="text-info" @click="addNewVariation">Add new variation</button>

                <div v-for="detail in details">
                    <div :class="group.wrap_divs[0].class" :id="group.wrap_divs[0].id">
                        <div v-for="field in group.wrap_divs[0].fields" :class="field.wrap_div_class">
                            <label :for="field.name">{{ field.label }}</label>
                            <input type="text" :name="field.name" v-model="detail[field.model]" :class="field.class">
                        </div>

                        <a href="#" class="btn btn-danger" @click="deleteVariation(detail.id)">delete</a>
                    </div>
                </div>
            </div>

        </template>

        <template v-if="form_schema.submit_button.wrap_div_class">
            <div :class="form_schema.submit_button.wrap_div_class">
                <button :type="form_schema.submit_button.type" :class="form_schema.submit_button.class" :id="form_schema.submit_button.id" @click="$emit(form_schema.submit_button.bindings.event)">{{ form_schema.submit_button.value }}</button>
            </div>
        </template>

        <button v-else :type="form_schema.submit_button.type" :class="form_schema.submit_button.class" :id="form_schema.submit_button.id" @click="$emit(form_schema.submit_button.bindings.event)">bindEvent update</button>
    </form>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';

	export default {
        name: "FormGenerator",
        props: ['form_id', 'form_model', 'form_schema', 'select_categories', 'select_genders', 'details'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        },
        computed: {
            ...mapGetters([
                'current_product_details'
            ])
        },
        mounted() {
            // console.log('in FormGenerator current_product_details: ', this.current_product_details); // null
        },
        methods: {
            deleteVariation(id) {
                console.log('delete ', id);
            },
            addNewVariation() {
                console.log('addNewVariation ');
            }
        } 
    }
</script>

<style scoped>
    
</style>