<template>
    <div class="d-flex justify-content-center p-5">
        <div class="w-100" v-if="isRoom">
            <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
                <h3 class="mb-4">Rooms & Suites</h3>
                <div class="border py-3 rounded-sm">
                    <div class="d-flex mb-3">
                        <div class="justify-content-center col-auto">
                            <b-button variant="success" class="rounded-circle" title="New Room" @click="open_room_modal(true)" v-b-tooltip.hover.noninteractive>
                                <i class="fa fa-plus"></i>
                            </b-button>
                            <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllRoom">
                                <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                            </b-button>
                            <b-button variant="secondary" class="rounded-circle" title="Manage Room Types" v-b-tooltip.hover.noninteractive @click="isRoom = false">
                                <i class="fa fa-cog"></i>
                            </b-button>
                        </div>
                        <div class="col">
                            <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Room"></b-input>
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
                            <template #cell(position)="row">
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
            <b-modal id="room_modal" :title="(modal_new ? 'New' : 'View / Update') + ' Room Record'" centered scrollable no-close-on-backdrop>
                <b-overlay :show="modalIsBusy" opacity="0.3">
                    <b-container fluid>
                        <div class="d-flex justify-content-center mb-4">
                            <i class="fa fa-building-o my-3" style="font-size: 120px;"></i>
                        </div>
                        <b-form class="my-3">
                            <h6 class="text-success font-weight-bold text-black-70 text-black-70">Room Information</h6>
                            <div class="font-weight-lighter ml-2 mb-3 text-danger w-100" style="font-size: 9px;">* Required Field</div>
                            <b-form-group label="Room Name *">
                                <b-form-input class="form-control rounded-pill" v-model="room_info.room_name" @keyup="is_valid('room_name')"></b-form-input>
                                <span class="text-danger i-label" v-if="valid.room_name">{{ valid.room_name }}</span>
                            </b-form-group>
                            <b-form-row>
                                <b-form-group class="col" label="Room Type *">
                                    <b-form-select class="form-control rounded-pill" v-model="room_info.room_type_name" :options="room_type_name_options" @change="is_valid('room_type_name')"></b-form-select>
                                    <span class="text-danger i-label" v-if="valid.room_type_name">{{ valid.room_type_name }}</span>
                                </b-form-group>
                                <b-form-group class="col" label="Room Rate (PHP)*">
                                    <b-form-input type="number" class="form-control rounded-pill" v-model="room_info.room_rate" min="0" step="100" @keyup="is_valid('room_rate')"></b-form-input>
                                    <span class="text-danger i-label" v-if="valid.room_rate">{{ valid.room_rate }}</span>
                                </b-form-group>
                            </b-form-row>
                            <b-form-row>
                                <b-form-group class="col" label="Max Adult Capacity (pax) *">
                                    <b-form-input type="number" class="form-control rounded-pill" v-model="room_info.max_adult" min="1" @keyup="is_valid('max_adult')"></b-form-input>
                                    <span class="text-danger i-label" v-if="valid.max_adult">{{ valid.max_adult }}</span>
                                </b-form-group>
                                <b-form-group class="col" label="Max Child Capacity (pax) *">
                                    <b-form-input type="number" class="form-control rounded-pill" v-model="room_info.max_child" min="0" @keyup="is_valid('max_child')"></b-form-input>
                                    <span class="text-danger i-label" v-if="valid.max_child">{{ valid.max_child }}</span>
                                </b-form-group>
                            </b-form-row>
                            <b-form-row>
                                <b-form-group class="col" label="Extra Adult Rate (per pax; 0 for not allowed; PHP)*">
                                    <b-form-input type="number" class="form-control rounded-pill" v-model="room_info.adult_extra_rate" min="0" step="100" @keyup="is_valid('adult_extra_rate')"></b-form-input>
                                    <span class="text-danger i-label" v-if="valid.adult_extra_rate">{{ valid.adult_extra_rate }}</span>
                                </b-form-group>
                                <b-form-group class="col" label="Extra Adult Rate (per pax; 0 for not allowed; PHP) *">
                                    <b-form-input type="number" class="form-control rounded-pill" v-model="room_info.child_extra_rate" min="0" step="100" @keyup="is_valid('child_extra_rate')"></b-form-input>
                                    <span class="text-danger i-label" v-if="valid.child_extra_rate">{{ valid.child_extra_rate }}</span>
                                </b-form-group>
                            </b-form-row>
                            <b-form-group label="Room Amenities *">
                                <!-- {{tag_input}} -->
                                <b-form-tags v-model="room_info.amenities" tag-variant="primary" no-outer-focus class="mb-2">
                                    <template v-slot="{ tags, inputAttrs, inputHandlers, tagVariant, addTag, removeTag }">
                                        <b-input-group class="mb-2">
                                            <b-form-input class="form-control r-left" v-bind="inputAttrs" v-model="tag_input" v-on="inputHandlers" placeholder="Enter to Add Amenities"></b-form-input>
                                            <b-input-group-append>
                                                <b-button class="pr-3 r-right" @click="addTag()" variant="primary">Add</b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                        <div class="d-block text-center" style="font-size: 1rem;" v-if="room_info.amenities.length == 0">
                                            <span style="font-size: 0.8rem;">No Amenities</span>
                                        </div>
                                        <div class="d-inline-block" style="font-size: 1rem;" v-else>
                                            <b-form-tag v-for="tag in tags" @remove="removeTag(tag)" :key="tag" :title="tag" :variant="tagVariant" class="mr-1 px-2" pill>{{ tag }}</b-form-tag>
                                        </div>
                                    </template>
                                </b-form-tags>
                            </b-form-group>
                            <b-form-group label="Room Description *">
                                <div class="border p-2 text-area-wrapper">
                                    <b-form-textarea class="border-0" v-model="room_info.description" rows="3" @keyup="is_valid('description')"></b-form-textarea>
                                </div>
                                <span class="text-danger i-label" v-if="valid.description">{{ valid.description }}</span>
                            </b-form-group>
                        </b-form>
                    </b-container>
                </b-overlay>
                <template #modal-footer>
                    <div class="d-flex justify-content-center w-100">
                        <b-button class="m-2 w-50" size="sm" variant="danger" @click="$bvModal.hide('room_modal')" :disabled="modalIsBusy" block pill>Cancel</b-button>
                        <b-button class="m-2 w-50" size="sm" variant="primary" @click="save_room()" :disabled="modalIsBusy" block pill>Done</b-button>
                    </div>
                </template>
            </b-modal>
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
            isRoom: true,
            all_rooms: [],
            all_room_types: [],
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
            tag_input: '',
            modal_new: true,
            room_info: {
                room_id: -1,
                room_type_name: 'Select Room Type',
                room_name: '',
                description: '',
                room_rate: 0,
                max_adult: 1,
                max_child: 0,
                adult_extra_rate: 0,
                child_extra_rate: 0,
                amenities: []
            },
            room_type_name_options: ['Select Room Type'],
            valid: {
                room_type_id: '',
                room_name: '',
                description: '',
                room_rate: '',
                max_adult: '',
                max_child: '',
                adult_extra_rate: '',
                child_extra_rate: ''
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

            this.room_type_name_options = ['Select Room Type']

            this.all_room_types.forEach((item) => {
                this.room_type_name_options.push(item.room_type_name)
            })
        },

        is_valid(field) {
            var regex_name = /^.{3,}$/
            var regex_description = /^.{10,}$/

            switch (field) {
                case 'room_name':
                    var valid_aname = regex_name.test(this.room_info.room_name)
                    this.valid.room_name = !valid_aname ? 'Name too short (At least 3 characters)' : ''
                    break

                case 'room_type_name':
                    this.valid.room_type_name = this.room_info.room_type_name == 'Select Room Type' ? 'Field is required' : ''
                    break

                case 'description':
                    var valid_cname = regex_description.test(this.room_info.description)
                    this.valid.description = !valid_cname ? 'Description too short (At least 10 characters)' : ''
                    break

                case 'room_rate':
                    var valid_rrate = this.room_info.room_rate
                    this.valid.room_rate = !valid_rrate ? 'Invalid Room Rate' : ''
                    break

                case 'adult_extra_rate':
                    var valid_arate = this.room_info.adult_extra_rate
                    this.valid.adult_extra_rate = !valid_arate ? 'Invalid Extra Adult Rate' : ''
                    break

                case 'child_extra_rate':
                    var valid_rrate = this.room_info.child_extra_rate
                    this.valid.child_extra_rate = !valid_rrate ? 'Invalid Extra Child Rate' : ''
                    break

                case 'max_adult':
                    var valid_amax = this.room_info.max_adult
                    this.valid.max_adult = !valid_amax ? 'Invalid Max Adult Capacity' : ''
                    break

                case 'max_child':
                    var valid_cmax = this.room_info.max_child
                    this.valid.max_child = !valid_cmax ? 'Invalid Max Child Capacity' : ''
                    break

                default:
                    break
            }
        },

        open_room_modal(is_new, room = null) {
            if(is_new) {
                this.modal_new = true

                this.room_info.room_id = -1
                this.room_info.room_type_name = 'Select Room Type'
                this.room_info.room_name = ''
                this.room_info.description = ''
                this.room_info.room_rate = 0
                this.room_info.max_adult = 1
                this.room_info.max_child = 0
                this.room_info.adult_extra_rate = 0
                this.room_info.child_extra_rate = 0

                this.valid.room_type_name = ''
                this.valid.room_name = ''
                this.valid.description = ''
                this.valid.room_rate = ''
                this.valid.max_adult = ''
                this.valid.max_child = ''
                this.valid.adult_extra_rate = ''
                this.valid.child_extra_rate = ''

                this.$bvModal.show('room_modal')
            }
            else {
                this.modal_new = false

                this.room_info.room_id = room.room_id
                this.room_info.room_type_name = room.room_type_name
                this.room_info.room_name = room.room_name
                this.room_info.description = room.description
                this.room_info.room_rate = room.room_rate
                this.room_info.max_adult = room.max_adult
                this.room_info.max_child = room.max_child
                this.room_info.adult_extra_rate = room.adult_extra_rate
                this.room_info.child_extra_rate = room.child_extra_rate

                this.$bvModal.show('room_modal')
            }
        },

        async save_room() {
            var all_valid = !(this.valid.room_name != '' || this.room_info.room_name == '') &&
                            !(this.room_info.room_type_name == 'Select Room Type') &&
                            !(this.valid.description != '' || this.room_info.description == '') &&
                            !(this.valid.room_rate !=  '') &&
                            !(this.valid.max_adult !=  '') &&
                            !(this.valid.max_child !=  '') &&
                            !(this.valid.adult_extra_rate !=  '') &&
                            !(this.valid.child_extra_rate !=  '')

            if(!all_valid) {
                this.alert.confirm = false
                this.alert.message = 'Please fill all the required fields!'

                this.alert.okClicked = () => {
                    this.$bvModal.hide('room_alert')
                }

                this.$bvModal.show('room_alert')
                return
            }

            this.modalIsBusy = true

            var _room_response = await axios.post('/api/rooms/' + (this.modal_new ? 'create' : 'update'),
                    this.room_info
                )
                .then((resp) => {
                    this.modalIsBusy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('room_modal')
                        this.$bvModal.hide('room_alert')

                        this.room_info.room_id = -1
                        this.room_info.room_type_name = 'Select Room Type'
                        this.room_info.room_name = ''
                        this.room_info.description = ''
                        this.room_info.room_rate = 0
                        this.room_info.max_adult = 1
                        this.room_info.max_child = 0
                        this.room_info.adult_extra_rate = 0
                        this.room_info.child_extra_rate = 0

                        this.valid.room_type_name = ''
                        this.valid.room_name = ''
                        this.valid.description = ''
                        this.valid.room_rate = ''
                        this.valid.max_adult = ''
                        this.valid.max_child = ''
                        this.valid.adult_extra_rate = ''
                        this.valid.child_extra_rate = ''

                        this.getAllRoom()
                    }

                    this.$bvModal.show('room_alert')

                    return resp.data.message
                })
                .then((err) => {
                    console.log(err)
                })
        },

        delete_room(room_id) {
            this.alert.confirm = true
            this.alert.message = 'Delete Room record?'

            this.alert.okClicked = async () => {
                var delete_room_response = await axios.post('/api/rooms/delete', {
                        room_id: room_id
                    })
                    .then((resp) => {
                        this.modalIsBusy = false

                        this.alert.confirm = false
                        this.alert.message = resp.data.message

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('room_alert')
                            this.getAllRoom()
                        }

                        this.$bvModal.show('room_alert')

                        return resp.data.message
                    })
                    .then((err) => {
                        console.log(err)
                    })
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('room_alert')
            }

            this.$bvModal.show('room_alert')
        },

        show_rooms() {
            this.isRoom = true
            this.getAllRoom()
            this.getAllRoomType()
        }
    },

    computed: {
        tags_amenities() {
            var tag_input = document.querySelector('input#tags_amenities')
            console.log(tag_input)
            // return tag_input ? tag_input.value : null
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
</style>
