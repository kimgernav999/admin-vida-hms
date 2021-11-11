
<template>
    <div class="d-flex justify-content-center p-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100" v-if="isRoom">
            <div>
                <h3 class="mb-4">Room Inventory</h3>
                <div class="border py-3 rounded-sm">
                    <div class="d-flex mb-3">
                        <div class="justify-content-center col-auto">
                            <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllRoom">
                                <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                            </b-button>
                            <b-button variant="secondary" class="rounded-circle" title="Manage Rooms" v-b-tooltip.hover.noninteractive @click="$router.push('/rooms')">
                                <i class="fa fa-cog"></i>
                            </b-button>
                        </div>
                        <div class="col px-0">
                            <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Room / Guest Name"></b-input>
                        </div>
                        <div class="col-auto">
                            <b-form-select class="form-control rounded-pill" v-model="vroom_type" :options="room_type_name_options"></b-form-select>
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
                            <template #cell(full_name)="row">
                                {{ row.item.room_name }}
                            </template>
                            <template #cell(email_address)="row">
                                {{ row.item.room_type_name }}
                            </template>
                            <template #cell(description)="row">
                                {{ row.item.description }}
                            </template>
                            <template #cell(actions)="row">
                                <div class="d-flex justify-content-center">
                                    <b-button variant="success" class="rounded-lg mx-1" size="sm" title="View / Update Record" @click="open_room_modal(false, row.item)" v-b-tooltip.hover.noninteractive>
                                        <i class="fa fa-edit"></i>
                                    </b-button>
                                    <b-button variant="primary" class="rounded-lg mx-1" size="sm" title="Delete Record" @click="delete_room(row.item.room_id)" v-b-tooltip.hover.noninteractive>
                                        <i class="fa fa-trash"></i>
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                        <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                    </div>
                </div>
            </div>
            <alert id="room_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
        </div>
        <room-types class="w-100" @backtorooms="show_rooms" v-else></room-types>
    </div>
</template>

<script>
import Alert from './alert.vue'
import RoomTypes from './roomtypes.vue'

export default {
    components: {
        Alert,
        RoomTypes
    },

    data() {
        return {
            isModified: false,
            isRoom: true,
            all_rooms: [],
            all_room_types: [],
            all_amenities: [],
            images: [],
            images_to_delete: [],
            images_to_delete_upon_discard: [],
            slide: 0,
            table: {
                current_page: 1,
                filter: '',
                filter_on: ['room_name', 'room_type_name', 'description'],
                fields: [
                    {
                        key: 'room_name',
                        label: 'Room Name',
                        sortable: true
                    },
                    {
                        key: 'room_type_name',
                        label: 'Room Type',
                        sortable: true
                    },
                    {
                        key: 'status',
                        label: 'Room Status',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: 'Actions',
                        sortable: false
                    }
                ],
                items: [],
                per_page: 6
            },
            result: {
                current_page: 1,
                filter: '',
                filter_on: ['amenities_name', 'description'],
                fields: [
                    {
                        key: 'amenities_name',
                        label: 'Room Type',
                        sortable: true
                    }
                ],
                items: [],
                per_page: 6
            },
            result_selected: false,
            tag_input: '',
            modal_new: true,
            show_delete_btn: false,
            reloading: false,
            room_type_name_options: ['All Room Type'],
            vroom_type: 'All Room Type',
            isBusy: false,
            modalIsBusy: false,
            alert: {
                show: false,
                title: 'Notice',
                message: '',
                confirm: false,
                okText: 'Ok',
                cancelText: 'Cancel',
                okClicked: () => {this.$bvModal.hide('signin_alert')},
                cancelClicked: () => {this.$bvModal.hide('signin_alert')},
            }
        }
    },

    methods: {
        async getAllRoom() {
            this.table.isBusy = true

            this.all_rooms = await axios.get('/api/rooms/allRooms')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = []

            this.all_rooms.forEach((item) => {
                var room = item

                room['room_type_name'] = item.type.room_type_name

                this.table.items.push(room)
            })

            this.table.isBusy = false
        },

        async getAllRoomType() {
            this.all_room_types = await axios.get('/api/roomtypes/allRoomTypes')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.room_type_name_options = ['All Room Type']

            this.all_room_types.forEach((item) => {
                this.room_type_name_options.push(item.room_type_name)
            })
        },

        result_clicked(item){
            this.result.filter = item.amenities_name
            this.result_selected = true
            this.$refs.text_tag.$el.value = item.amenities_name
        }
    },

    mounted() {
        this.getAllRoom()
        this.getAllRoomType()
    }
}
</script>

<style scoped>
    .cbox-label {
        font-size: 14px;
    }

    .separator {
        border-bottom: solid 1px gray;
        height: 1px;
    }

    .separator-lightgray {
        border-bottom: solid 1px lightgray;
    }

    .preview-carousel {
        height: 256px;
    }

    .delete-btn {
        position: absolute;
        bottom: 40px;
    }

    .transparent {
        opacity: 30%;
    }
</style>
