<template>
    <div>
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <div class="d-flex mb-4">
                <div class="mr-3 no-arrow" style="margin-top: 6px;" title="Go back to Amenities" @click="$emit('backtorooms')" v-b-tooltip.hover.noninteractive>
                    <i class="fa fa-arrow-left"></i>
                </div>
                <h3>Room Type</h3>
            </div>
            <div class="border py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="justify-content-center col-auto">
                        <b-button variant="success" class="rounded-circle" title="New Room Type" @click="open_roomtypes_modal(true)" v-b-tooltip.hover.noninteractive>
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllAmenitiesCategory">
                            <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Room Type"></b-input>
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
                            {{ row.item.room_type_name }}
                        </template>
                        <template #cell(position)="row">
                            {{ row.item.description }}
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="success" class="rounded-lg mx-1" size="sm" title="View / Update Record" @click="open_roomtypes_modal(false, row.item)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-edit"></i>
                                </b-button>
                                <b-button variant="primary" class="rounded-lg mx-1" size="sm" title="Delete Record" @click="delete_roomtypes(row.item.room_type_id)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-trash"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
        <b-modal id="roomtypes_modal" :title="(modal_new ? 'New' : 'View / Update') + ' Room Type Record'" centered scrollable no-close-on-backdrop>
            <b-overlay :show="modalIsBusy" opacity="0.3">
                <b-container fluid>
                    <div class="d-flex justify-content-center mb-4">
                        <i class="fa fa-building-o my-3" style="font-size: 120px;"></i>
                    </div>
                    <b-form class="my-3" @keydown.enter="save_roomtypes()">
                        <h6 class="text-success font-weight-bold text-black-70 text-black-70">Room Type Information</h6>
                        <div class="font-weight-lighter ml-2 mb-3 text-danger w-100" style="font-size: 9px;">* Required Field</div>
                        <b-form-group label="Room Type Name *">
                            <b-form-input class="form-control rounded-pill" v-model="roomtype_info.room_type_name" @keyup="is_valid('room_type_name')"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.room_type_name">{{ valid.room_type_name }}</span>
                        </b-form-group>
                        <b-form-group label="Room Type Description *">
                            <div class="border p-2 text-area-wrapper">
                                <b-form-textarea class="border-0" v-model="roomtype_info.description" rows="3" @keyup="is_valid('description')"></b-form-textarea>
                            </div>
                            <span class="text-danger i-label" v-if="valid.description">{{ valid.description }}</span>
                        </b-form-group>
                    </b-form>
                </b-container>
            </b-overlay>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button class="m-2 w-50" size="sm" variant="danger" @click="$bvModal.hide('roomtypes_modal')" :disabled="modalIsBusy" block pill>Cancel</b-button>
                    <b-button class="m-2 w-50" size="sm" variant="primary" @click="save_roomtypes()" :disabled="modalIsBusy" block pill>Done</b-button>
                </div>
            </template>
        </b-modal>
        <alert id="roomtypes_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            all_rooms_categories: '',
            table: {
                current_page: 1,
                filter: '',
                filter_on: ['room_type_name', 'description'],
                fields: [
                    {
                        key: 'room_type_name',
                        label: 'Room Type Name',
                        sortable: true
                    },
                    {
                        key: 'description',
                        label: 'Description',
                        sortable: true
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
            modal_new: true,
            roomtype_info: {
                room_type_id: -1,
                room_type_name: '',
                description: ''
            },
            valid: {
                room_type_name: '',
                description: ''
            },
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
        async getAllAmenitiesCategory() {
            this.table.isBusy = true

            this.all_rooms_categories = await axios.get('/api/roomtypes/allRoomTypes')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = this.all_rooms_categories

            this.table.isBusy = false
        },

        is_valid(field) {
            var regex_name = /^.{3,}$/
            var regex_description = /^.{10,}$/

            switch (field) {
                case 'room_type_name':
                    var valid_aname = regex_name.test(this.roomtype_info.room_type_name)
                    this.valid.room_type_name = !valid_aname ? 'Name too short (At least 3 characters)' : ''
                    break

                case 'description':
                    var valid_cname = regex_description.test(this.roomtype_info.description)
                    this.valid.description = !valid_cname ? 'Description too short (At least 10 characters)' : ''
                    break

                default:
                    break
            }
        },

        open_roomtypes_modal(is_new, roomtypes = null) {
            if(is_new) {
                this.modal_new = true

                this.roomtype_info.room_type_id = -1
                this.roomtype_info.room_type_name = ''
                this.roomtype_info.description = ''

                this.valid.room_type_name = ''
                this.valid.description = ''

                this.$bvModal.show('roomtypes_modal')
            }
            else {
                this.modal_new = false

                this.roomtype_info.room_type_id = roomtypes.room_type_id
                this.roomtype_info.room_type_name = roomtypes.room_type_name
                this.roomtype_info.description = roomtypes.description

                this.$bvModal.show('roomtypes_modal')
            }
        },

        async save_roomtypes() {
            var all_valid = !(this.valid.room_type_name != '' || this.roomtype_info.room_type_name == '') &&
                            !(this.valid.description != '' || this.roomtype_info.description == '')

            if(!all_valid) {
                this.alert.confirm = false
                this.alert.message = 'Please fill all the required fields!'

                this.alert.okClicked = () => {
                    this.$bvModal.hide('roomtypes_alert')
                }

                this.$bvModal.show('roomtypes_alert')
                return
            }

            this.modalIsBusy = true

            var _roomtypes_response = await axios.post('/api/roomtypes/' + (this.modal_new ? 'create' : 'update'),
                    this.roomtype_info
                )
                .then((resp) => {
                    this.modalIsBusy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('roomtypes_modal')
                        this.$bvModal.hide('roomtypes_alert')

                        this.roomtype_info.room_type_name == ''
                        this.roomtype_info.description == ''

                        this.getAllAmenitiesCategory()
                    }

                    this.$bvModal.show('roomtypes_alert')

                    return resp.data.message
                })
                .then((err) => {
                    console.log(err)
                })
        },

        delete_roomtypes(room_type_id) {
            this.alert.confirm = true
            this.alert.message = 'Delete Room Type and its related Amenities record?'

            this.alert.okClicked = async () => {
                var delete_roomtypes_response = await axios.post('/api/roomtypes/delete', {
                        room_type_id: room_type_id
                    })
                    .then((resp) => {
                        this.modalIsBusy = false

                        this.alert.confirm = false
                        this.alert.message = resp.data.message

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('roomtypes_alert')
                            this.getAllAmenitiesCategory()
                        }

                        this.$bvModal.show('roomtypes_alert')

                        return resp.data.message
                    })
                    .then((err) => {
                        console.log(err)
                    })
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('roomtypes_alert')
            }

            this.$bvModal.show('roomtypes_alert')
        }
    },

    mounted() {
        this.getAllAmenitiesCategory()
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
</style>
