<template>
    <div class="d-flex justify-content-center p-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <h3 class="mb-5">Bookings & Reservations</h3>
            <div class="border mb-2 py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="justify-content-center col-auto">
                        <b-button variant="success" class="rounded-circle" title="Walk-in Booking" v-b-tooltip.hover.noninteractive @click="$bvModal.show('new_booking')">
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getBookings">
                            <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Booking Code"></b-input>
                    </div>
                </div>
                <div class="d-flex flex-column">
                    <b-table class="border-bottom vw-75" :busy="table.isBusy" :fields="table.fields" :filter="table.filter" :filter-included-fields="table.filter_on" :current-page="table.current_page" :items="table.items" striped responsive show-empty>
                        <template #table-busy>
                            <div class="text-center text-danger my-2">
                                <i class="fa fa-spinner fa-10x align-middle"></i>
                                <strong>Loading...</strong>
                            </div>
                        </template>
                        <template #cell(booking_code)="row">
                            <h5 class="text-center font-weight-bold" style="width: 220px;">{{ row.item.booking_code }}</h5>
                        </template>
                        <template #cell(reserve_info)="row">
                            <div style="width: 200px;">
                                <div class="mb-2"><b>Room</b>: {{ row.item.room.room_name }}</div>
                                <b>Guests</b>:<br>
                                <div class="ml-2 mb-2">{{ ((row.item.no_of_adult > 0) ? row.item.no_of_adult + ' Adult(s)' : '') + ((row.item.no_of_child > 0) ? ' & ' + row.item.no_of_child + ' Child(ren)' : '') }}</div>
                                <b>{{ row.item.is_reservation ? 'Reserved' : 'Booked' }} by</b>:<br>
                                <div class="ml-2 mb-2">{{ row.item.user.first_name + ' ' + row.item.user.last_name }}</div>
                                <b>Booking Dates</b>:<br>
                                <div class="ml-2 mb-2">{{ $moment(row.item.book_start).format('MMM D, YYYY')  + ' - ' + $moment(row.item.book_end).format('MMM D, YYYY') }}</div>
                                <b>Reserved At</b>: {{ $moment(row.item.created_at).format('MMM D, YYYY')}}
                            </div>
                        </template>
                        <template #cell(is_paid)="row">
                            <div style="width: 150px;">
                                <div v-if="row.item.is_paid">
                                    <div class="mb-2"><b>Paid at</b>: {{ $moment(row.item.paid_at).format('MMM D, YYYY') }}</div>
                                    <b>Amount Paid</b>: <br>
                                    <div class="ml-2 mb-2">PHP {{ row.item.total_fee.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</div>
                                    <b>Transaction ID</b>:<br>
                                    <div class="ml-2 mb-2">ABCDE12345</div>
                                </div>
                                <div v-else>
                                    <div class="mb-2"><b>Pending</b></div>
                                    <b>Amount to Pay</b>: <br>
                                    <div class="ml-2">PHP {{ row.item.total_fee.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</div>
                                </div>
                            </div>
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="danger" class="rounded-lg mx-1" size="sm" title="Cancel" @click="cancelBooking(row.item)" v-if="!row.item.is_paid" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-times-circle"></i>
                                </b-button>
                                <h6 class="text-secondary" v-if="row.item.is_paid">No Actions Available</h6>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table.current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
        <b-modal id="new_booking" title="Walk-in Booking" centered no-close-on-backdrop scrollable hide-footer>
            <b-container fluid>
                <div class="d-flex align-self-center w-100">
                    <b-form-group class="px-1 w-100" label="Booked By:">
                        <b-input v-model="book_info.booked_by"></b-input>
                    </b-form-group>
                </div>
                <div class="d-flex align-self-center w-100">
                    <b-form-group class="px-1 w-50" label="Start Date">
                        <b-input type="date" v-model="book_info.start_date" :min="date_today" @change="get_total_fee"></b-input>
                    </b-form-group>
                    <b-form-group class="px-1 w-50" label="End Date">
                        <b-input type="date" v-model="book_info.end_date" :min="date_tomorrow" @change="get_total_fee"></b-input>
                    </b-form-group>
                </div>
                <div class="d-flex align-self-center w-100">
                    <b-form-group class="px-1 w-100" label="Room Type">
                        <b-select v-model="book_info.room_type" :options="room_types" @change="getAllRooms"></b-select>
                    </b-form-group>
                    <b-form-group class="px-1 w-100" label="Selected Room">
                        <b-select v-model="book_info.room" :options="rooms" @change="get_total_fee"></b-select>
                    </b-form-group>
                </div>
                <div class="d-flex align-self-center w-100">
                    <b-form-group class="px-1" label="No. of Adult(s)">
                        <b-input type="number" min="1" value="1" v-model="book_info.adult_no" @change="get_total_fee"></b-input>
                    </b-form-group>
                    <b-form-group class="px-1" label="No of Child(ren)">
                        <b-input type="number" min="0" value="0" v-model="book_info.child_no" @change="get_total_fee"></b-input>
                    </b-form-group>
                </div>
                <div class="d-flex align-self-center mb-3 w-100">
                    <b-form-group class="px-1 w-50" label="Mode of Payment">
                        <b-input class="bg-white" value="Over the Counter" readonly></b-input>
                    </b-form-group>
                    <b-form-group class="px-1 w-50" label="Total">
                        <b-input class="bg-white" :value="total_fee.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')" readonly></b-input>
                    </b-form-group>
                </div>
                <div class="d-flex align-self-center w-100">
                    <b-button class="mx-auto w-50" variant="success" pill>Book Now</b-button>
                </div>
            </b-container>
        </b-modal>
        <alert id="bookings_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :ok-text="alert.okText" :cancel-text="alert.cancelText" :ok-clicked="alert.okClicked" :cancel-clicked="alert.cancelClicked" ></alert>
    </div>
</template>

<script>
import Alert from './alert.vue'

export default {
    components: {
        Alert
    },

    data() {
        return {
            table: {
                current_page: 1,
                filter: '',
                filter_on: ['booking_code'],
                fields: [
                    {
                        key: 'booking_code',
                        label: 'Booking / Reservation Code',
                        sortable: true
                    },
                    {
                        key: 'reserve_info',
                        label: 'Booking Information',
                        sortable: false
                    },
                    {
                        key: 'is_paid',
                        label: 'Payment Status',
                        sortable: false
                    },
                    {
                        key: 'actions',
                        label: 'Actions',
                        sortable: false,
                    }
                ],
                items: [],
                per_page: 6
            },
            selected_booking: null,
            alert: {
                show: false,
                title: 'Notice',
                message: '',
                confirm: false,
                okText: 'Ok',
                cancelText: 'Cancel',
                okClicked: () => {this.alert.show = false},
                cancelClicked: () => {this.alert.show = false},
            },
            room_types: ['Select Room Types'],
            room_types_o: ['Select Room Types'],
            rooms: ['No Rooms Found'],
            selected_room: null,
            total_fee: 0,
            book_info: {
                booked_by: '',
                start_date: this.$moment().format('YYYY-MM-DD'),
                end_date: this.$moment().add(1, 'days').format('YYYY-MM-DD'),
                adult_no: 1,
                child_no: 0,
                room_type: 'Select Room Types',
                room: 'No Rooms Found'
            }
        }
    },

    computed: {
        date_today() {
            this.$moment().format('YYYY-MM-DD')
        },

        date_tomorrow() {
            this.$moment().add(1, 'days').format('YYYY-MM-DD')
        }
    },

    methods: {
        async getBookings() {
            this.table.items = await axios.get('/api/booking/allBookings')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })
        },

        async getAllRooms() {
            this.selected_room = null

            var rooms = await axios.get('/api/rooms/allRooms')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            var room_type_id = -1

            this.room_types_o.forEach((item) => {
                if(item.room_type_name == this.book_info.room_type){
                    room_type_id = item.room_type_id
                    return;
                }
            })

            if(room_type_id != -1) {
                this.rooms = ['Select Room']

                rooms.forEach((item) => {
                    if(item.room_type_id == room_type_id){
                        this.selected_room = item
                        this.rooms.push(item.room_name)
                    }
                })

                this.book_info.room = 'Select Room'
            }
            else {
                this.rooms = ['No Rooms Found']
                this.book_info.room = 'Select Room'
            }
        },

        async getAllRoomTypes() {
            var roomtypes = await axios.get('/api/roomtypes/allRoomTypes')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.room_types_o = roomtypes
            this.room_types = ['Select Room Types']

            roomtypes.forEach((item) => {
                this.room_types.push(item.room_type_name)
            })
        },

        cancelBooking(booking) {
            this.alert.confirm = true
            this.alert.message = 'Cancel Booking?'

            this.alert.okClicked = async () => {
                this.table.items = await axios.post('/api/booking/cancelBooking', {
                        booking_id: booking.booking_id
                    })
                    .then((resp) => {
                        this.$bvModal.hide('bookings_alert')
                        this.alert.confirm = false
                        this.alert.message = 'Booking canceled!'

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('bookings_alert')
                            this.getBookings()
                        }
                        return resp.data
                    })
                    .catch((err) => {
                        console.log(err)
                    })

                this.$bvModal.show('bookings_alert')
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('bookings_alert')
            }

            this.$bvModal.show('bookings_alert')
        },

        pay_modal(item) {
            this.selected_booking = item

            this.$bvModal.show('pay_modal')

            this.$nextTick(() => {
                paypal.Buttons({
                    style: {
                        layout: 'vertical',
                        color:  'blue',
                        shape:  'rect',
                        label:  'paypal'
                    }
                }).render('#paypal-button-container');
            })
        },

        get_total_fee() {
            this.total_fee = 0

            if(this.selected_room){
                var stay_fee = this.selected_room.room_rate * this.$moment(this.book_info.end_date).diff(this.book_info.start_date, 'days')
                var xtadult_fee = (this.book_info.adult_no - this.selected_room.max_adult) * this.selected_room.adult_extra_rate
                var xtchild_fee = (this.book_info.child_no - this.selected_room.max_child) * this.selected_room.child_extra_rate

                this.total_fee = stay_fee + xtadult_fee + xtchild_fee
            }
        }
    },

    mounted() {
        this.getBookings()
        this.getAllRoomTypes();
    }
}
</script>

<style scoped>

</style>
