<template>
    <div>
        <div class="d-flex justify-content-start px-4">
            <h3 class="small-uppercase text-14 text-myblue">&gt;&nbsp; Orders</h3>
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
            </div>

            <div class="p-0 d-flex align-items-center">
                <el-button @click="exportAll" size="small" class="mr-3">Export all</el-button>
                <el-button @click="exportFiltered" size="small">Export filtered</el-button>
            </div>
        </div>

        <!-- Filters -->
        <div class="d-none flex-wrap justify-content-between align-items-center bg-filter my-4 px-4">
            <el-col :span="4">
                <el-select v-model="filters[4].value" placeholder="Payment status" multiple="multiple">
                    <el-option label="Not paid" value="Not paid"></el-option>
                    <el-option label="Paid" value="Paid"></el-option>
                    <el-option label="Pending" value="Pending"></el-option>
                    <!-- <el-option v-for="item in paymentStatuses" :key="item.value" :label="item.label" :value="item.value"></el-option> -->
                </el-select>
            </el-col>

            <el-col :span="4">
                <el-select v-model="filters[1].value" placeholder="Is delivered">
                    <el-option label="Yes" value="Yes"></el-option>
                    <el-option label="No" value="No"></el-option>
                </el-select>
            </el-col>

            <!-- <el-col :span="3"> -->
                <!-- <el-date-picker type="date" placeholder="Date1" v-model="sizeForm.date1" style="width: 100%;"></el-date-picker> -->
                <!-- <el-date-picker type="date" placeholder="Date2" v-model="sizeForm.date2" style="width: 100%;"></el-date-picker> -->

                <!-- <el-select v-model="filters[1].value" placeholder="Date" multiple="multiple">
                    <el-option label="Repair" value="repair"></el-option>
                    <el-option label="Help" value="help"></el-option>
                </el-select> -->
            <!-- </el-col> -->

            <el-col :span="4">
                <div class="dropdown w-100">
                    <button @click="myFunction" class="dropbtn">
                        <div class="d-flex justify-content-between">
                            <span class="text-light-grey">Amount</span>
                            <span class="text-light-grey"><i class="el-icon-arrow-down el-icon--right"></i></span>
                        </div>
                    </button>

                    <div id="myDropdown" class="dropdown-content">
                        <div class="d-flex justify-content-between my-2 px-2">
                            <el-input v-model="filters[2].value" size="small" placeholder="min" class="mr-3"></el-input>
                            <el-input v-model="filters[3].value" size="small" placeholder="max"></el-input>
                        </div>
                    </div>
                </div>
            </el-col>

            <el-button size="medium" @click="clearFilter">Reset filters</el-button>
        </div>
        <!-- End Filters -->

        <div class="d-flex justify-content-start my-4 px-4">
            <!-- <tool-bar v-model='filters'></tool-bar> -->

            <data-tables :data="mydata" :table-props="tableProps" :pagination-props="{ pageSizes: [5, 10, 15] }" :action-col="actionCol" :filters="filters" ref="filterTable" @filtered-data="handleFilteredData" :sort-method="sortDate">   <!-- ref="filterTable": doesn't work -->
                <div slot="empty">No data in table.</div>

                <el-table-column v-for="title in titles" :visible.sync="centerVisible" :width="title.width" :prop="title.prop" :label="title.label" :key="title.label" sortable="custom" center>  <!-- :filter-method="filterHandler" -->
                </el-table-column>
            </data-tables>
        </div>

        <!-- .modal-dialog for modify product -->
        <div class="modal fade slide-up p-5" tabindex="-1" role="dialog" aria-labelledby="modalSlideUpLabel" aria-hidden="false" id="viewOrderModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalTitle">Modify product</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                                
                    <!-- Modal body -->
                    <div class="modal-body px-5 mb-5">
                        <order-details :id="orderId"></order-details>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import OrderDetails from '../../components/userboard/OrderDetails';
    import { mapGetters, mapActions } from 'vuex';

    var payment_statuses = ["Not paid", "Pending", "Paid"];

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
        data() {
            return {
                orders: [],
                mydata: [],
                orderId: null,
                paymentStatuses: [
                    { label: "Not paid", value: "Not paid"},
                    { label: "Pending", value: "Pending"},
                    { label: "Paid", value: "Paid"}
                ],
                total: 0,
                centerVisible: false,
                titles: [
                    {
                        prop: "id",
                        label: "Id",
                        width: '70%'
                    }, 
                    {
                        prop: "user_id",
                        label: "User id",
                        width: '100%'
                    },
                    {
                        prop: "total_price",
                        label: "Cost",
                        width: '100%'
                    },
                    {
                        prop: "payment_status",
                        label: "Payment status"
                    },
                    {
                        prop: "shipping_address",
                        label: "Shipping address"
                    },
                    {
                        prop: "date",
                        label: "Date"
                    },
                    {
                        prop: "is_delivered",
                        label: "Delivered",
                        width: "120"
                    }
                ],
                tableProps: {
                    border: true,
                    center: true,
                    defaultSort: {
                        prop: 'user_id',
                        order: 'descending'
                    }
                },
                filters: [
                    {prop: ['id', 'user_id', 'total_price', 'payment_status', 'shipping_address', 'date', 'is_delivered'], value: ''},
                    {prop: 'is_delivered', value: ''},
                    {
                        value: '',
                        filterFn: (row, filter) => {
                            return Object.keys(row).some(prop => {
                                if (prop === 'total_price') {
                                    return row.total_price >= filter.value;
                                }
                            });
                        }
                    },
                    {
                        value: '',
                        filterFn: (row, filter) => {
                            return Object.keys(row).some(prop => {
                                if (prop === 'total_price') {
                                    return row.total_price <= filter.value;
                                }
                            });
                        }
                    },
                    {prop: 'payment_status', value: []},
                ],
                filteredData: [],
                columns: ['id', 'user_id', 'total_price', 'date', 'payment_status', 'shipping_address'],
                columnNames: ['Order ID', 'User ID', 'Amount', 'Date', 'Payment status', 'Shipping addresss'],
                sortDate: {
                    date(a, b) {
                        let date_a = Date.parse(new Date(a));
                        let date_b = Date.parse(new Date(b));

                        if (date_a < date_b) {
                            return false;
                        }

                        return true;

                        // filter between 2 dates from the calendar
                        /*var dataStartWithoutHour = data[4].split(' ');
                        var dataStartTable = dataStartWithoutHour[0].trim();
                        var dateStartArray = dataStartTable.split('/');
                        var dateStart = Date.parse(new Date(dateStartArray[2], dateStartArray[1]-1, dateStartArray[0])); // timestamp

                        if (dateStart < start || dateStart > end) {
                            return false;
                        }*/
                    }
                },
                /*sizeForm: {
                    date1: '',
                    date2: ''
                },*/
                /*columnfilters: [
                    {text: '2016-05-01', value: '2016-05-01'},
                    {text: '2016-05-02', value: '2016-05-02'},
                    {text: '2016-05-03', value: '2016-05-03'},
                    {text: '2016-05-04', value: '2016-05-04'}
                ],*/
                actionCol: {
                    label: 'Actions',
                    props: {
                        align: 'center',
                        width: '120'
                    },
                    buttons: [
                        {
                            props: {
                                icon: 'el-icon-view text-skyblue viewOrderButton'
                            },
                            handler: row => {
                                // bind modal window
                                $('.viewOrderButton').each(function(event) {
                                    $(this).attr("data-toggle", "modal");
                                    $(this).attr("data-target", "#viewOrderModal");
                                });

                                this.getOrderDetails(row.id);
                            }
                        }
                    ]
                }
            }
        },
        components: {
            OrderDetails
        },
        computed: {
            ...mapGetters([
                'orderDetailsId'
            ])
        },
        beforeMount() {
            axios.get('/api/orders/')
            .then(response => {
                this.orders = response.data;

                for (let item in this.orders) {
                    let order = this.orders[item];

                    this.mydata.push({
                        "id": order.id,
                        "user_id": order.user_id,
                        "total_price": order.total_price, // +" €"
                        "payment_status": payment_statuses[order.payment_status],
                        "shipping_address": this.$options.filters.uppercaseFirst(order.shipping_address),
                        "date": this.$options.filters.formatDate(order.created_at),
                        "is_delivered": order.is_delivered ? "Yes" : "No"
                    });
                }
            })
            .catch(error => {
                console.error(error);
            }); 
        },
        mounted() {
            // console.log(this.paymentStatuses); // 0, 1, 2
            $('.pagination-bar').addClass('mt-3');
        },
        methods: {
            myFunction() {
                document.getElementById("myDropdown").classList.toggle("show");
            },
            toggleFilters() {
                $(".bg-filter").fadeToggle(500).toggleClass("d-flex");
            },
            clearFilter() {
                // this.$refs.filterTable.clearFilter();
                this.handleFilteredData(this.mydata);
                this.filters[0].value = '';
                this.filters[1].value = '';
                this.filters[2].value = '';
                this.filters[3].value = '';
                this.filters[4].value = '';
            },
            filterDate(value, row, column) {
                const property = column['property'];
                return row[property] === value;
            },
            filterHandler(value, row, column) {
                const property = column['property'];
                return row[property] === value;
            },
            exportAll() {
                CsvExport(this.mydata, this.columns, this.columnNames, 'all_orders');
            },
            exportFiltered() {
                CsvExport(this.filteredData, this.columns, this.columnNames, 'filtered_orders');
            },
            handleFilteredData(filteredData) {
                this.filteredData = filteredData;
            },
            async getOrderDetails(orderId) {
                this.orderId = orderId;
                $('#viewOrderModal #modalTitle').text('Order #'+orderId);
                await this.$store.dispatch('updateOrderDetails', orderId);               
            },
            /*deliver(index) {
                let order = this.orders[index];

                axios.patch(`/api/orders/${order.id}/deliver`)
                .then(response => {
                    this.orders[index].is_delivered = 1;
                    this.$forceUpdate();
                })
                .catch(error => {
                    console.error(error);
                }); 
            }*/
        }
    }
</script>

<style scoped>
 </style>