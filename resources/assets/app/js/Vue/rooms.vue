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
            <b-modal id="room_modal" :title="(modal_new ? 'New' : 'View / Update') + ' Room Record'" centered scrollable no-close-on-backdrop hide-header-close>
                <b-overlay :show="modalIsBusy" opacity="0.3">
                    <b-container fluid>
                        <div class="d-flex justify-content-center mb-4">
                            <i class="fa fa-building-o my-3" style="font-size: 120px;"></i>
                        </div>
                        <b-form class="my-3">
                            <input class="d-none" type="file" accept="image/*" @change="onFileChange" ref="add_img" multiple />
                            <div class="d-flex">
                                <h6 class="mt-1 text-success font-weight-bold text-black-70 text-black-70">Room Preview Images</h6>
                                <b-button class="ml-auto rounded-circle" variant="primary" size="sm" @click="$refs.add_img.click()" title="Upload Image(s)" v-b-tooltip.hover.non-interactive>
                                    <i class="fa fa-plus"></i>
                                </b-button>
                            </div>
                            <div class="mt-3 mb-5" v-if="images.length == 0">
                                <h6 class="text-secondary font-weight-bold text-center">No Images Uploaded</h6>
                                <span class="text-danger i-label" v-if="valid.images">{{ valid.images }}</span>
                            </div>
                            <div class="mt-3 mb-5" v-else>
                                <b-overlay class="preview-carousel" opacity="0.9" :show="reloading">
                                    <b-carousel v-model="slide" background="#ababab" style="text-shadow: 1px 1px 2px #333;" :fade="images.length > 1" :controls="images.length > 1" :indicators="images.length > 1">
                                        <b-carousel-slide class="d-flex justify-content-center py-2" v-for="(image, key) in images" :key="key">
                                            <template #img>
                                                <img class="d-block rounded" height="240" :src="'/storage/uploads/' + image.file_name" :alt="image.file_name" :id="'slide_' + image.attachment_id" @mouseenter="show_delete_btn = true" @mouseleave="show_delete_btn = false">
                                                <b-button :class="'rounded-circle delete-btn' + (!show_delete_btn ? ' transparent' : '')" variant="danger" size="sm" title="Remove Image" @click="delete_img(image.attachment_id)" @mouseenter="show_delete_btn = true" @mouseleave="show_delete_btn = false" v-b-tooltip.hover.non-interactive>
                                                    <i class="fa fa-trash"></i>
                                                </b-button>
                                            </template>
                                        </b-carousel-slide>
                                    </b-carousel>
                                </b-overlay>
                                <span class="text-danger i-label" v-if="valid.images">{{ valid.images }}</span>
                            </div>
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
                                <b-form-tags v-model="room_info.amenities" tag-variant="primary" no-outer-focus class="mb-2">
                                    <template v-slot="{ tags, inputAttrs, inputHandlers, tagVariant, addTag, removeTag }">
                                        <b-input-group class="mb-2">
                                            <b-form-input class="d-none" v-bind="inputAttrs" v-on="inputHandlers" ref="text_tag"></b-form-input>
                                            <b-form-input class="form-control r-left" v-model="result.filter" @keyup="getAllAmenities" placeholder="Enter to Add Amenities"></b-form-input>
                                            <b-input-group-append>
                                                <b-button class="pr-3 r-right" @click="reset_tag_input(addTag)" variant="primary" :disabled="!result_selected">Add</b-button>
                                            </b-input-group-append>
                                            <div class="mt-2 result_table" v-if="result.filter.length > 2 & !result_selected">
                                                <b-table class="border-bottom vw-75" :busy="result.isBusy" :fields="result.fields" :filter="result.filter" :filter-included-fields="result.filter_on" :current-page="result.current_page" :items="result.items" @row-clicked="result_clicked" striped responsive show-empty>
                                                    <template #table-busy>
                                                        <div class="text-center text-danger my-2">
                                                            <i class="fa fa-spinner fa-10x align-middle"></i>
                                                            <strong>Loading...</strong>
                                                        </div>
                                                    </template>
                                                </b-table>
                                            </div>
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
                        <b-button class="m-2 w-50" size="sm" variant="danger" @click="discard_changes('room_modal')" :disabled="modalIsBusy" block pill>Cancel</b-button>
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
            isModified: false,
            isRoom: true,
            all_rooms: [],
            all_room_types: [],
            all_amenities: [],
            images: [],
            images_to_delete: [],
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
                amenities: [],
                image_ids: []
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
                child_extra_rate: '',
                images: ''
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

        async getAllAmenities() {
            this.all_amenities = await axios.get('/api/amenities/allAmenities')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.result.items = this.all_amenities

            this.result_selected = false
        },

        result_clicked(item){
            this.result.filter = item.amenities_name
            this.result_selected = true
            this.$refs.text_tag.$el.value = item.amenities_name
        },

        reset_tag_input(addTag) {
            addTag(this.result.filter)
            this.result.filter = ''
        },

        is_valid(field) {
            var regex_name = /^.{3,}$/
            var regex_description = /^.{10,}$/

            this.isModified = true

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
            this.isModified = false

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
                this.room_info.amenities = []
                this.room_info.image_ids = []
                this.images = []

                this.valid.room_type_name = ''
                this.valid.room_name = ''
                this.valid.description = ''
                this.valid.room_rate = ''
                this.valid.max_adult = ''
                this.valid.max_child = ''
                this.valid.adult_extra_rate = ''
                this.valid.child_extra_rate = ''
                this.valid.images = ''

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
                this.room_info.amenities = JSON.parse(room.amenities)

                this.images = []

                this.room_info.image_ids = JSON.parse(room.image_ids)

                this.room_info.image_ids.forEach(async (attachment_id) => {
                    var attach_details_response = await axios.get('/api/attachments/viewDetails?attachment_id=' + attachment_id)
                        .then((resp) => {
                            this.images.push(resp.data)

                            return resp.data
                        })
                        .catch((err) => {
                            console.log(err)
                        })
                })

                this.images_to_delete = []

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

            var images_valid = this.images.length >= 2

            if(!(all_valid && images_valid)) {
                this.valid.images = !images_valid ? 'Please upload at least 2 images' : ''

                this.alert.confirm = false
                this.alert.message = 'Please fill all the required fields!'

                this.alert.okClicked = () => {
                    this.$bvModal.hide('room_alert')
                }

                this.$bvModal.show('room_alert')
                return
            }

            this.modalIsBusy = true

            this.room_info.image_ids = []

            this.images.forEach((image) => {
                this.room_info.image_ids.push(image.attachment_id)
            })

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
                        this.room_info.image_ids = []

                        this.valid.room_type_name = ''
                        this.valid.room_name = ''
                        this.valid.description = ''
                        this.valid.room_rate = ''
                        this.valid.max_adult = ''
                        this.valid.max_child = ''
                        this.valid.adult_extra_rate = ''
                        this.valid.child_extra_rate = ''
                        this.valid.images = ''

                        this.getAllRoom()
                    }

                    this.$bvModal.show('room_alert')

                    return resp.data.message
                })
                .catch((err) => {
                    console.log(err)
                })

            this.images_to_delete.forEach(async (attachment_id) => {
                    var attach_delete_response = await axios.get('/api/attachments/delete?attachment_id=' + attachment_id)
                        .then((resp) => {
                            return resp.data
                        })
                        .catch((err) => {
                            console.log(err)
                        })

                    this.images.forEach((image) => {
                        if(image.attachment_id = attachment_id) this.images.pop(image)
                    })
                })
        },

        delete_room(room_id) {
            this.alert.confirm = true
            this.alert.message = 'Delete Room record?'

            this.alert.okClicked = async () => {
                this.room_info.image_ids.forEach(async (attachment_id) => {
                        var attach_delete_response = await axios.get('/api/attachments/delete?attachment_id=' + attachment_id)
                            .then((resp) => {
                                return resp.data
                            })
                            .catch((err) => {
                                console.log(err)
                            })

                        this.images.forEach((image) => {
                            if(image.attachment_id = attachment_id) this.images.pop(image)
                        })
                    })

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
                    .catch((err) => {
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
        },

        onFileChange(e) {
            var selectedFiles = e.target.files

            this.isModified = true
            this.reloading = true

            setTimeout(async () => {
                for (let i = 0; i < selectedFiles.length; i++) {
                    var f_data = new FormData()

                    f_data.append('image', selectedFiles[i])

                    var upload_img_response = await axios.post('/api/attachments/create', f_data)
                            .then((resp) => {
                                return resp.data
                            })
                            .catch((err) => {
                                console.log(err)
                            })

                    var attach_details_response = await axios.get('/api/attachments/viewDetails?attachment_id=' + upload_img_response)
                        .then((resp) => {
                            this.images.push(resp.data)

                            return resp.data
                        })
                        .catch((err) => {
                            console.log(err)
                        })
                }

                this.$refs.add_img.files = null

                this.reloading = false
            }, 800);
        },

        delete_img(attachment_id) {
            this.isModified = true
            this.reloading = true

            setTimeout(async () => {
                if(this.modal_new) {
                    var attach_delete_response = await axios.get('/api/attachments/delete?attachment_id=' + attachment_id)
                        .then((resp) => {
                            return resp.data
                        })
                        .catch((err) => {
                            console.log(err)
                        })
                }
                else {
                    this.images_to_delete.push(attachment_id)
                    this.room_info.image_ids.pop(attachment_id)
                }

                this.images.forEach((image, index) => {
                    if(image.attachment_id = attachment_id) this.images.pop(this.images[index])
                })

                this.images = []

                this.room_info.image_ids.forEach(async (attachment_id) => {
                    var attach_details_response = await axios.get('/api/attachments/viewDetails?attachment_id=' + attachment_id)
                        .then((resp) => {
                            this.images.push(resp.data)

                            return resp.data
                        })
                        .catch((err) => {
                            console.log(err)
                        })
                })

                this.reloading = false
            }, 800);
        },

        discard_changes(modal_id){
            if(this.isModified){
                this.alert.confirm = true
                this.alert.message = 'Discard Changes?'

                this.alert.okClicked = () => {
                    this.$bvModal.hide(modal_id)
                    this.$bvModal.hide('room_alert')
                }

                this.alert.cancelClicked = () => {
                    this.$bvModal.hide('room_alert')
                }

                this.$bvModal.show('room_alert')
            }
            else {
                this.$bvModal.hide(modal_id)
            }
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
