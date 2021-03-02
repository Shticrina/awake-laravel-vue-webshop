<template>
	<div>
        <div class="d-flex justify-content-start px-4">
            <h3 class="small-uppercase text-14 text-myblue">&gt;&nbsp; Products</h3>
        </div>

        <div class="d-flex justify-content-between my-4 px-4">
            <div class="p-0 d-flex align-items-center">
                <div class="mr-3">
                    <el-input v-model="filters[0].value" size="small" placeholder="Search" prefix-icon="el-icon-search" clearable></el-input>
                </div>

                <el-link size="small" type="info" @click="toggleFilters" class="mr-5">
                    <u>Filters</u>
                    <i class="el-icon-arrow-down el-icon--right"></i>
                </el-link>

                <div class="p-0 d-flex align-items-center">
                    <el-button @click="exportAll" size="small" class="mr-3">Export all</el-button>
                    <el-button @click="exportFiltered" size="small">Export filtered</el-button>
                </div>
            </div>

            <el-button size="small" type="success" @click="modifyProductModal" data-toggle="modal" data-target="#editProductModal">Add new product</el-button>
        </div>

        <!-- Filters -->
        <div class="d-none flex-wrap justify-content-between align-items-center bg-filter my-4 px-4">
            <el-col :span="4">
                <el-select v-model="filters[1].value" size="small" placeholder="Category" multiple="multiple">
                    <el-option v-for="item in select_categories" :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
            </el-col>

            <el-col :span="4">
                <el-select v-model="filters[2].value" size="small" placeholder="Gender">
                    <el-option v-for="item in select_genders" :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
            </el-col>

            <el-col :span="4">
                <el-select v-model="filters[3].value" size="small" placeholder="Color" multiple="multiple">
                    <el-option v-for="item in select_colors" :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
            </el-col>

            <el-col :span="4">
                <el-select v-model="filters[4].value" size="small" placeholder="Size" multiple="multiple">
                    <el-option v-for="item in select_sizes" :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
            </el-col>

            <el-button size="medium" @click="clearFilter">Reset filters</el-button>
        </div>

        <div class="d-flex justify-content-start my-4 px-4">
            <data-tables :data="mydata" :table-props="tableProps" :pagination-props="{ pageSizes: [5, 10, 15] }" :action-col="actionCol" :filters="filters" ref="filterTable" @filtered-data="handleFilteredData">
                <div slot="empty">No data in table.</div>

                <el-table-column v-for="title in titles" :width="title.width" :prop="title.prop" :label="title.label" :key="title.label" sortable="custom" center>
                </el-table-column>
            </data-tables>
        </div>

        <!-- .modal-dialog for modify / add product -->
        <div class="modal fade slide-up p-5" tabindex="-1" role="dialog" aria-labelledby="modalSlideUpLabel" aria-hidden="false" id="editProductModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title text-success" id="modalTitle">Add new product</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                                
                    <!-- Modal body -->
                    <div class="modal-body px-5">
                        <form-generator :form_id="editProduct_form_id" :form_model="myproduct" :form_schema="product_schema" :select_categories="select_categories" :select_genders="select_genders" :details="myproduct.details" @update="modifyProduct($event)" v-if="validateProductForm"></form-generator>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Modal from './ProductModal';
    import * as functions from '../../functions.js';
    import productSchema from '../../schemas/updateProductSchema.js';
    import FormGenerator from '../../components/forms/FormGenerator';
    import { mapGetters, mapActions } from 'vuex';

    let CsvExport = function(data, fields, fieldNames, fileName) {
        try {
            var result = json2csv({
                data: data,
                fields: fields,
                fieldNames: fieldNames
            });
            var csvContent = 'data:text/csvcharset=GBK,\uFEFF' + result;
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement('a');

            link.setAttribute('href', encodedUri);
            link.setAttribute('download', `${(fileName || 'file')}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (err) {
            console.error(err);
        }
    }

	export default {
        data(){
            return {
                editingItem: null,
                addingProduct: null,
                select_categories: [],
                select_genders: [],
                select_colors: [],
                select_sizes: [],
                mydata: [],
                myproduct: {
                    id: "",
                    name: "",
                    description: "",
                    gender: "",
                    category: "",
                    details: null,
                    colors: "",
                    sizes: "",
                    price: "",
                    quantity: ""
                },
                currentProductDetails: null,
                product_schema: productSchema,
                editProduct_form_id: "updateProductForm",
                attemptSubmitProduct: false,
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
                        prop: "category",
                        label: "Category",
                        width: '160'
                    },
                    {
                        prop: "gender",
                        label: "Gender",
                        width: '100%'
                    },
                    {
                        prop: "colors",
                        label: "Colors"
                    },
                    {
                        prop: "sizes",
                        label: "Sizes",
                        width: '160'
                    }
                ],
                tableProps: {
                    border: true,
                    defaultSort: {
                        prop: 'name',
                        order: 'ascending'
                    }
                },
                filters: [
                    {prop: ['id', 'name', 'category', 'gender', 'colors', 'sizes'], value: ''},
                    {prop: 'category', value: []},
                    {prop: 'gender', value: []},
                    {prop: 'colors', value: []},
                    {prop: 'sizes', value: []}
                ],
                filteredData: [],
                columns: ['id', 'name', 'category', 'gender', 'colors', 'sizes'],
                columnNames: ['ID', 'Name', 'Category', 'Gender', 'Colors', 'Sizes'],
                actionCol: {
                    label: 'Actions',
                    props: {
                        align: 'center',
                        width: '120'
                    },
                    buttons: [
                        {
                            props: {
                                icon: 'el-icon-edit text-skyblue editProductButton'
                            },
                            handler: row => {
                                // bind modal window
                                $('.editProductButton').each(function(event) {
                                    $(this).attr("data-toggle", "modal");
                                    $(this).attr("data-target", "#editProductModal");
                                });

                                this.getProduct(row.id);
                            }
                        },
                        {
                            props: {
                                icon: 'el-icon-delete text-danger',
                            },
                            handler: row => {
                                this.$confirm('This will permanently delete this product. Continue?', '', {
                                    confirmButtonText: 'OK',
                                    cancelButtonText: 'Cancel',
                                    type: 'warning'
                                }).then(() => {
                                    this.mydata.splice(this.mydata.indexOf(row), 1);

                                    // delete from the database
                                    this.deleteProduct(row.id);

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
            FormGenerator,
            Modal
        },
        computed: {
            ...mapGetters([
                'products',
                'categories',
                'product_details',
                'current_product_details',
                'currentProduct'
            ]),
            validateProductForm() {
                if (this.attemptSubmitProduct && this.attemptSubmitProduct == true) {
                    // functions.validateForm(this.myproduct, productSchema, this.editProduct_form_id);
                }

                return true;
            },
            updateMyData() {
                this.mydata = [];

                for (let item in this.products) {
                    let product = this.products[item];

                    this.mydata.push({
                        "id": product.id,
                        "name": product.name,
                        "description": product.description,
                        "category": product.category.name,
                        "gender": product.gender,
                        "colors": this.objToString(product.colors),
                        "sizes": (Object.entries(product.sizes).length > 1 ? this.objToString(product.sizes) : "one size")
                    });
                }
            }
        },
        async beforeMount() {
            await this.$store.dispatch('getAllProducts');
            this.updateMyData;
        },
        mounted() {
            $('.pagination-bar').addClass('mt-3');

            var genderValues = this.selectUniqueValues(this.products, 'gender');
            var colorValues = this.selectUniqueValues(this.product_details, 'color');
            var sizeValues = this.selectUniqueValues(this.product_details, 'size');

            for (let category in this.categories) {
                let item = this.categories[category];
                this.select_categories.push({label: item.name, value: item.name});
            }

            this.transformToSelect(genderValues, this.select_genders);
            this.transformToSelect(sizeValues, this.select_sizes);
            this.transformToSelect(colorValues, this.select_colors);
        },
        methods: {
            objToString(obj) {
                let arr = Object.values(obj);
                return arr.join(", ");
            },
            selectUniqueValues(obj, property) {
                let result = obj.map(function (product) {
                    return product[property];
                }).filter((v, i, a) => a.indexOf(v) === i && v != null);

                return result;
            },
            transformToSelect(from_array, to_array) {
                for (let selector in from_array) {
                    let item = from_array[selector];
                    to_array.push({label: item, value: item});
                }

                return to_array;
            },
            myFunction() {
                document.getElementById("myDropdown").classList.toggle("show");
            },
            toggleFilters() {
                $(".bg-filter").fadeToggle(500).toggleClass("d-flex");
            },
            clearFilter() {
                this.handleFilteredData(this.mydata);
                this.filters[0].value = '';
                this.filters[1].value = '';
                this.filters[2].value = '';
                this.filters[3].value = '';
                this.filters[4].value = '';
            },
            filterHandler(value, row, column) {
                const property = column['property'];
                return row[property] === value;
            },
            exportAll() {
                CsvExport(this.mydata, this.columns, this.columnNames, 'all_products');
            },
            exportFiltered() {
                CsvExport(this.filteredData, this.columns, this.columnNames, 'filtered_products');
            },
            handleFilteredData(filteredData) {
                this.filteredData = filteredData;
            },
            modifyProductModal() {
                this.myproduct.id = this.myproduct.name = this.myproduct.description ="";
                $('#editProductModal .modal-title').attr('id', 'modalTitle');
                $('#editProductModal #modalTitle').text('Add new product');
                $('#submitProductButton').text('Create');
            },
            async getProduct(productId) {
                console.log('productId: ', productId);
                $('#editProductModal #modalTitle').text('Modify product #'+productId);
                $('#editProductModal #modalTitle').attr('id', 'modalTitle-'+productId);
                $('#submitProductButton').text('Update');

                await this.$store.dispatch('getCurrentProduct', productId)
                .then(response => {
                    // console.log('response: ', response);
                    this.myproduct.id = response.id;
                    this.myproduct.name = response.name;
                    this.myproduct.description = response.description;
                    this.myproduct.gender = response.gender;
                    this.myproduct.category = response.category_name;
                    this.myproduct.details = response.details;
                    // console.log('response.details: ', response.details.length); // ok
                    // console.log('current_product_details: ', this.current_product_details.length); // nu se actualizeaza
                })
                .catch(error => {
                    console.error(error);
                });


                /*axios.get(`/api/products/${productId}`)
                .then(response => {
                    let product = response.data;

                    this.myproduct.id = product.id;
                    this.myproduct.name = product.name;
                    this.myproduct.description = product.description;
                    this.myproduct.gender = product.gender;
                    this.myproduct.category = product.category_name;
                    this.myproduct.details = product.details;
                    console.log('myproduct.details: ', this.myproduct.details.length); // ramane la 3

                    // this.currentProductDetails = product.details;
                })
                .catch(error => {
                    console.error(error);
                });*/
            },
            async modifyProduct() { // param id if edit, else create
                this.attemptSubmitProduct = true;

                if (this.myproduct.id != "") { // if we want to edit
                    this.myproduct.id = this.myproduct.id;
                }

                this.myproduct.name = $('#'+this.editProduct_form_id+' input[name="name"]').val();
                this.myproduct.description = $('#'+this.editProduct_form_id+' input[name="description"]').val();

                /*let resultValidation = functions.validateForm(this.myproduct, productSchema, this.editProduct_form_id);
                let hasValidationErrors = Object.keys(resultValidation).length > 0;
                $('#'+this.editProduct_form_id+' .form-control.reactive.is-invalid').first().focus();

                if (!hasValidationErrors) {
                    let data = {
                        id: this.myproduct.id,
                        name: this.myproduct.name,
                        description: this.myproduct.description
                    };

                    await this.$store.dispatch('editOrAddProduct', {vm: this, formData: data})
                    .then(response => {
                        this.updateMyData;
                    });

                    $('#editProductModal').modal('hide');
                } else {
                    console.log('Update category validation error!');
                }*/
            },
            deleteProduct(product_id) {
                this.$store.dispatch('deleteProduct', product_id);
            },
            newProduct(){
                this.addingProduct = {
                    name : null, 
                    units : null, 
                    price : null,
                    description : null,
                    image : null
                }
            },
            endEditing(product){
                this.editingItem = null
                let index = this.products.indexOf(product)
                axios.put(`/api/products/${product.id}`,{
                    name  : product.name,
                    units : product.units,
                    price : product.price,
                    description : product.description,
                })
                .then(response =>{
                    this.products[index] = product
                })
                .catch(response => {

                })
            },
            addProduct(product){
                this.addingProduct = null
                axios.post("/api/products/",{
                    name  : product.name,
                    units : product.units,
                    price : product.price,
                    description : product.description,
                    image : product.image
                })
                .then(response =>{
                    this.products.push(product)
                })
                .catch(response => {

                })
            }
        }
    }
</script>