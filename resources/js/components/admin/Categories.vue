<template>
    <div>
        <div class="d-flex justify-content-start px-4">
            <h3 class="small-uppercase text-14 text-myblue">&gt;&nbsp; Categories</h3>
        </div>

        <div class="d-flex justify-content-between my-4 px-4">
            <div class="p-0 d-flex align-items-center">
                <div class="mr-3">
                    <el-input v-model="filters[0].value" size="small" placeholder="Search" prefix-icon="el-icon-search" clearable></el-input>
                </div>
            </div>

            <el-button size="small" type="success" @click="modifyCategoryModal" data-toggle="modal" data-target="#editCategoryModal">Add new category</el-button>
        </div>

        <div class="d-flex justify-content-start my-4 px-4">
            <data-tables :data="mydata" :table-props="tableProps" :pagination-props="{ pageSizes: [5, 10, 15] }" :action-col="actionCol" :filters="filters" @filtered-data="handleFilteredData">
                <div slot="empty">No data in table.</div>

                <el-table-column v-for="title in titles" :width="title.width" :prop="title.prop" :label="title.label" :key="title.label" sortable="custom" center> 
                </el-table-column>
            </data-tables>
        </div>

        <!-- .modal-dialog for modify category -->
        <div class="modal fade slide-up p-5" tabindex="-1" role="dialog" aria-labelledby="modalSlideUpLabel" aria-hidden="false" id="editCategoryModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title text-success" id="modalTitle">Add new category</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                                
                    <!-- Modal body -->
                    <div class="modal-body px-5">
                        <form-generator :form_id="editCategory_form_id" :form_model="mycategory" :form_schema="category_schema" @update="modifyCategory($event)" v-if="validateCategoryForm"></form-generator>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import * as functions from '../../functions.js';
    import categorySchema from '../../schemas/updateCategorySchema.js';
    import FormGenerator from '../../components/forms/FormGenerator';
    import { mapGetters, mapActions } from 'vuex';

	export default {
        data() {
            return {
                all_categories: [],
                mydata: [],
                mycategory: {
                    id: "",
                    name: "",
                    description: ""
                },
                category_schema: categorySchema,
                editCategory_form_id: "updateCategoryForm",
                attemptSubmitCategory: false,
                titles: [
                    {
                        prop: "id",
                        label: "Id",
                        width: "80%"
                    }, 
                    {
                        prop: "name",
                        label: "Name"
                    },
                    {
                        prop: "description",
                        label: "Description"
                    }
                ],
                tableProps: {
                    border: true,
                    defaultSort: {
                        prop: 'name',
                        order: 'descending'
                    }
                },
                filters: [
                    {prop: ['id', 'name', 'description'], value: ''},
                    {prop: 'name', value: []}
                ],
                filteredData: [],
                columns: ['id', 'name', 'description'],
                columnNames: ['ID', 'Name', 'Description'],
                actionCol: {
                    label: 'Actions',
                    props: {
                        align: 'center',
                        width: '160%'
                    },
                    buttons: [
                        {
                            props: {
                                icon: 'el-icon-edit text-skyblue editCategoryButton'
                            },
                            handler: row => {
                                // bind modal window
                                $('.editCategoryButton').each(function(event) {
                                    $(this).attr("data-toggle", "modal");
                                    $(this).attr("data-target", "#editCategoryModal");
                                });

                                this.getCategory(row.id);
                            }
                        },
                        {
                            props: {
                                icon: 'el-icon-delete text-danger',
                            },
                            handler: row => {
                                this.$confirm('This will permanently delete this category. Continue?', '', {
                                    confirmButtonText: 'OK',
                                    cancelButtonText: 'Cancel',
                                    type: 'warning'
                                }).then(() => {
                                    this.mydata.splice(this.mydata.indexOf(row), 1);

                                    // delete from the database
                                    this.deleteCategory(row.id);

                                    this.$message({
                                        type: 'success',
                                        message: 'Delete completed'
                                    });
                                }).catch(() => {
                                    this.$message({
                                        type: 'info',
                                        message: 'Delete canceled'
                                    });          
                                });    
                            }
                        }
                    ]
                }
            }
        },
        components: {
            FormGenerator
        },
        computed: {
            ...mapGetters([
                'categories'
            ]),
            validateCategoryForm() {
                if (this.attemptSubmitCategory && this.attemptSubmitCategory == true) {
                    functions.validateForm(this.mycategory, categorySchema, this.editCategory_form_id);
                }

                return true;
            },
            updateMyData() {
                this.all_categories = this.categories;
                this.mydata = [];

                for (let item in this.all_categories) {
                    let category = this.all_categories[item];

                    this.mydata.push({
                        "id": category.id,
                        "name": category.name,
                        "description": category.description
                    });
                }
            }
        },
        beforeMount() {
            this.updateMyData;
        },
        mounted() {
            $('.pagination-bar').addClass('mt-3');
        },
        methods: {
            toggleFilters() {
                $(".bg-filter").fadeToggle(500).toggleClass("d-flex");
            },
            clearFilter() {
                this.handleFilteredData(this.mydata);
                this.filters[0].value = '';
                this.filters[1].value = '';
            },
            handleFilteredData(filteredData) {
                this.filteredData = filteredData;
            },
            modifyCategoryModal() {
                this.mycategory.id = this.mycategory.name = this.mycategory.description ="";
                $('#editCategoryModal .modal-title').attr('id', 'modalTitle');
                $('#editCategoryModal #modalTitle').text('Add new category');
                $('#submitCategoryButton').text('Create');
            },
            getCategory(catId) {
                axios.get(`/api/categories/${catId}`)
                .then(response => {
                    let category = response.data;
                    this.mycategory.id = category.id;
                    this.mycategory.name = category.name;
                    this.mycategory.description = category.description;
                })
                .catch(error => {
                    console.error(error);
                });

                $('#editCategoryModal #modalTitle').text('Modify category #'+catId);
                $('#editCategoryModal #modalTitle').attr('id', 'modalTitle-'+catId);
                $('#submitCategoryButton').text('Update');
            },
            async modifyCategory() { // param id if edit, else create
                this.attemptSubmitCategory = true;

                if (this.mycategory.id != "") { // if we want to edit
                    this.mycategory.id = this.mycategory.id;
                }

                this.mycategory.name = $('#'+this.editCategory_form_id+' input[name="name"]').val();
                this.mycategory.description = $('#'+this.editCategory_form_id+' input[name="description"]').val();

                let resultValidation = functions.validateForm(this.mycategory, categorySchema, this.editCategory_form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.editCategory_form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = {
                        id: this.mycategory.id,
                        name: this.mycategory.name,
                        description: this.mycategory.description
                    };

                    await this.$store.dispatch('editOrAddCategory', {vm: this, formData: data})
                    .then(response => {
                        this.updateMyData;
                    });

                    $('#editCategoryModal').modal('hide');
                } else {
                    console.log('Update category validation error!');
                }
            },
            deleteCategory(cat_id) {
                this.$store.dispatch('deleteCategory', cat_id);
            }
        }
    }
</script>

<style scoped>
    .sc-table {
        width: 100% !important;
    }
 </style>