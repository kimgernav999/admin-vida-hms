<template>
    <div class="d-flex justify-content-center p-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <h3 class="mb-5">Promo Offers</h3>
            <div class="border py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="justify-content-center col-auto">
                        <b-button variant="success" class="rounded-circle" title="New Offer" @click="open_offer_modal(true)" v-b-tooltip.hover.noninteractive>
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllOffer">
                            <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Offer"></b-input>
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
                            {{ row.item.offer_name }}
                        </template>
                        <template #cell(description)="row">
                            {{ row.item.description }}
                        </template>
                        <template #cell(rates)="row">
                            {{ row.item.rates.toFixed(2) }} %
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="success" class="rounded-lg mx-1" size="sm" title="View / Update Record" @click="open_offer_modal(false, row.item)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-edit"></i>
                                </b-button>
                                <b-button variant="primary" class="rounded-lg mx-1" size="sm" title="Delete Record" @click="delete_offer(row.item.offer_id)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-trash"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
        <b-modal id="offer_modal" :title="(modal_new ? 'New' : 'View / Update') + ' Promo Offer Record'" centered scrollable no-close-on-backdrop hide-header-close>
            <b-overlay :show="modalIsBusy" opacity="0.3">
                <b-container fluid>
                    <div class="d-flex justify-content-center mb-4">
                        <i class="fa fa-handshake-o my-3" style="font-size: 120px;"></i>
                    </div>
                    <b-form class="my-3">
                            <input class="d-none" type="file" accept="image/*" @change="onFileChange" ref="add_img" multiple />
                            <div class="d-flex">
                                <h6 class="mt-1 text-success font-weight-bold text-black-70 text-black-70">Promo Illustration Images</h6>
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
                        <h6 class="text-success font-weight-bold text-black-70 text-black-70">Offer Information</h6>
                        <div class="font-weight-lighter ml-2 mb-3 text-danger w-100" style="font-size: 9px;">* Required Field</div>
                        <b-form-group label="Offer Name *">
                            <b-form-input class="form-control rounded-pill" v-model="offer_info.offer_name" @keyup="is_valid('offer_name')"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.offer_name">{{ valid.offer_name }}</span>
                        </b-form-group>
                        <b-form-row>
                            <b-form-group class="col" label="Promo Offer Rate (%)*">
                                <b-form-input type="number" class="form-control rounded-pill" v-model="offer_info.rates" min="0" step="100" @keyup="is_valid('rates')"></b-form-input>
                                <span class="text-danger i-label" v-if="valid.offer_rate">{{ valid.offer_rate }}</span>
                            </b-form-group>
                        </b-form-row>
                        <b-form-group label="Offer Description *">
                            <div class="border p-2 text-area-wrapper">
                                <b-form-textarea class="border-0" v-model="offer_info.description" rows="3" @keyup="is_valid('description')"></b-form-textarea>
                            </div>
                            <span class="text-danger i-label" v-if="valid.description">{{ valid.description }}</span>
                        </b-form-group>
                    </b-form>
                </b-container>
            </b-overlay>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button class="m-2 w-50" size="sm" variant="danger" @click="discard_changes('offer_modal')" :disabled="modalIsBusy" block pill>Cancel</b-button>
                    <b-button class="m-2 w-50" size="sm" variant="primary" @click="save_offer()" :disabled="modalIsBusy" block pill>Done</b-button>
                </div>
            </template>
        </b-modal>
        <alert id="offer_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            isModified: false,
            isOffer: true,
            all_offers: [],
            images: [],
            images_to_delete: [],
            images_to_delete_upon_discard: [],
            slide: 0,
            table: {
                current_page: 1,
                filter: '',
                filter_on: ['offer_name', 'rates', 'description'],
                fields: [
                    {
                        key: 'offer_name',
                        label: 'Promo Offer Name',
                        sortable: true
                    },
                    {
                        key: 'rates',
                        label: 'Rate',
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
            show_delete_btn: false,
            reloading: false,
            offer_info: {
                offer_id: -1,
                offer_name: 'Select Offer Type',
                rates: '',
                description: '',
                image_ids: []
            },
            valid: {
                offer_id: '',
                offer_name: '',
                rates: 0,
                description: '',
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
        async getAllOffer() {
            this.table.isBusy = true

            this.all_offers = await axios.get('/api/offers/allOffers')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = this.all_offers

            this.table.isBusy = false
        },

        is_valid(field) {
            var regex_name = /^.{3,}$/
            var regex_description = /^.{10,}$/

            this.isModified = true

            switch (field) {
                case 'offer_name':
                    var valid_aname = regex_name.test(this.offer_info.offer_name)
                    this.valid.offer_name = !valid_aname ? 'Name too short (At least 3 characters)' : ''
                    break

                case 'description':
                    var valid_description = regex_description.test(this.offer_info.description)
                    this.valid.description = !valid_description ? 'Description too short (At least 10 characters)' : ''
                    break

                case 'rates':
                    var valid_rate = this.offer_info.rates != '' || this.offer_info.rates > 0
                    this.valid.rates = !valid_rate ? 'Invalid Offer Rate' : ''
                    break

                default:
                    break
            }
        },

        open_offer_modal(is_new, offer = null) {
            this.isModified = false

            if(is_new) {
                this.modal_new = true

                this.offer_info.offer_id = -1
                this.offer_info.offer_name = ''
                this.offer_info.description = ''
                this.offer_info.rates = 0
                this.offer_info.image_ids = []
                this.images = []

                this.$bvModal.show('offer_modal')
            }
            else {
                this.modal_new = false

                this.offer_info.offer_id = offer.offer_id
                this.offer_info.offer_name = offer.offer_name
                this.offer_info.description = offer.description
                this.offer_info.rates = offer.rates

                this.images = []

                this.offer_info.image_ids = JSON.parse(offer.image_ids)

                this.offer_info.image_ids.forEach(async (attachment_id) => {
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

                this.$bvModal.show('offer_modal')
            }
        },

        async save_offer() {
            var all_valid = !(this.valid.offer_name != '' || this.offer_info.offer_name == '') &&
                            !(this.valid.description != '' || this.offer_info.description == '') &&
                            !(this.valid.rates !=  '' || this.offer_info.rates == '')

            var images_valid = this.images.length >= 1

            if(!(all_valid && images_valid)) {
                this.valid.images = !images_valid ? 'Please upload at least 1 image' : ''

                this.alert.confirm = false
                this.alert.message = 'Please fill all the required fields!'

                this.alert.okClicked = () => {
                    this.$bvModal.hide('offer_alert')
                }

                this.$bvModal.show('offer_alert')
                return
            }

            this.modalIsBusy = true

            this.offer_info.image_ids = []

            this.images.forEach((image) => {
                this.offer_info.image_ids.push(image.attachment_id)
            })

            var _offer_response = await axios.post('/api/offers/' + (this.modal_new ? 'create' : 'update'),
                    this.offer_info
                )
                .then((resp) => {
                    this.modalIsBusy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('offer_modal')
                        this.$bvModal.hide('offer_alert')

                        this.offer_info.offer_id = -1
                        this.offer_info.offer_name = ''
                        this.offer_info.description = ''
                        this.offer_info.rates = 0

                        this.valid.offer_type_name = ''
                        this.valid.offer_name = ''
                        this.valid.description = ''
                        this.valid.rates = ''

                        this.offer_info.image_ids = []

                        this.valid.images = ''

                        this.getAllOffer()
                    }

                    this.$bvModal.show('offer_alert')

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

            this.images_to_delete_upon_discard = []
        },

        delete_offer(offer_id) {
            this.alert.confirm = true
            this.alert.message = 'Delete Offer record?'

            this.alert.okClicked = async () => {
                this.offer_info.image_ids.forEach(async (attachment_id) => {
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

                var delete_offer_response = await axios.post('/api/offers/delete', {
                        offer_id: offer_id
                    })
                    .then((resp) => {
                        this.modalIsBusy = false

                        this.alert.confirm = false
                        this.alert.message = resp.data.message

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('offer_alert')
                            this.getAllOffer()
                        }

                        this.$bvModal.show('offer_alert')

                        return resp.data.message
                    })
                    .catch((err) => {
                        console.log(err)
                    })

                this.$bvModal.hide('offer_modal')
                this.$bvModal.hide('offer_alert')
                this.getAllOffer()
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('offer_alert')
            }

            this.$bvModal.show('offer_alert')
        },

        show_offers() {
            this.isOffer = true
            this.getAllOffer()
        },

        onFileChange(e) {
            this.valid.images = ''
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
                            this.images_to_delete_upon_discard.push(resp.data.attachment_id)

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
                    this.offer_info.image_ids.pop(attachment_id)
                }

                this.images.forEach((image, index) => {
                    if(image.attachment_id = attachment_id) this.images.pop(this.images[index])
                })

                this.images = []

                this.offer_info.image_ids.forEach(async (attachment_id) => {
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
                    this.images_to_delete_upon_discard.forEach(async (attachment_id) => {
                        var attach_delete_response = await axios.get('/api/attachments/delete?attachment_id=' + attachment_id)
                            .then((resp) => {
                                return resp.data
                            })
                            .catch((err) => {
                                console.log(err)
                            })
                    })

                    this.images_to_delete_upon_discard = []

                    this.$bvModal.hide(modal_id)
                    this.$bvModal.hide('offer_alert')
                }

                this.alert.cancelClicked = () => {
                    this.$bvModal.hide('offer_alert')
                }

                this.$bvModal.show('offer_alert')
            }
            else {
                this.$bvModal.hide(modal_id)
            }
        }
    },

    mounted() {
        this.getAllOffer()
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
