<template>
    <div>
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <div class="d-flex mb-4">
                <div class="mr-3 no-arrow" style="margin-top: 6px;" title="Go back to Amenities" @click="$emit('backtoamenities')" v-b-tooltip.hover.noninteractive>
                    <i class="fa fa-arrow-left"></i>
                </div>
                <h3>Amenities Category</h3>
            </div>
            <div class="border py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="justify-content-center col-auto">
                        <b-button variant="success" class="rounded-circle" title="New Amenities Category" @click="open_amenities_category_modal(true)" v-b-tooltip.hover.noninteractive>
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllAmenitiesCategory">
                            <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Amenities Category"></b-input>
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
                            {{ row.item.category_name }}
                        </template>
                        <template #cell(position)="row">
                            {{ row.item.description }}
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="success" class="rounded-lg mx-1" size="sm" title="View / Update Record" @click="open_amenities_category_modal(false, row.item)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-edit"></i>
                                </b-button>
                                <b-button variant="primary" class="rounded-lg mx-1" size="sm" title="Delete Record" @click="delete_amenities_category(row.item.category_id)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-trash"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
        <b-modal id="amenities_category_modal" :title="(modal_new ? 'New' : 'View / Update') + ' Amenities Category Record'" centered scrollable no-close-on-backdrop hide-header-close>
            <b-overlay :show="modalIsBusy" opacity="0.3">
                <b-container fluid>
                    <div class="d-flex justify-content-center mb-4">
                        <i class="fa fa-bullseye my-3" style="font-size: 120px;"></i>
                    </div>
                    <b-form class="my-3" @keydown.enter="save_amenities_category()">
                        <h6 class="text-success font-weight-bold text-black-70 text-black-70">Amenities Category Information</h6>
                        <div class="font-weight-lighter ml-2 mb-3 text-danger w-100" style="font-size: 9px;">* Required Field</div>
                        <b-form-group label="Amenities Category Name *">
                            <b-form-input class="form-control rounded-pill" v-model="amenities_category_info.category_name" @keyup="is_valid('category_name')"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.category_name">{{ valid.category_name }}</span>
                        </b-form-group>
                        <b-form-group label="Amenities Category Description *">
                            <div class="border p-2 text-area-wrapper">
                                <b-form-textarea class="border-0" v-model="amenities_category_info.description" rows="3" @keyup="is_valid('description')"></b-form-textarea>
                            </div>
                            <span class="text-danger i-label" v-if="valid.description">{{ valid.description }}</span>
                        </b-form-group>
                    </b-form>
                </b-container>
            </b-overlay>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button class="m-2 w-50" size="sm" variant="danger" @click="discard_changes('amenities_category_modal')" :disabled="modalIsBusy" block pill>Cancel</b-button>
                    <b-button class="m-2 w-50" size="sm" variant="primary" @click="save_amenities_category()" :disabled="modalIsBusy" block pill>Done</b-button>
                </div>
            </template>
        </b-modal>
        <alert id="amenities_category_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            all_amenities_categories: '',
            table: {
                current_page: 1,
                filter: '',
                filter_on: ['category_name', 'category_name', 'description'],
                fields: [
                    {
                        key: 'category_name',
                        label: 'Amenities Category Name',
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
            amenities_category_info: {
                category_id: -1,
                category_name: '',
                description: ''
            },
            valid: {
                category_name: '',
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

            this.all_amenities_categories = await axios.get('/api/amenities_category/allAmenitiesCategories')
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = this.all_amenities_categories

            this.table.isBusy = false
        },

        is_valid(field) {
            this.isModified = true

            var regex_name = /^.{3,}$/
            var regex_description = /^.{10,}$/

            switch (field) {
                case 'category_name':
                    var valid_aname = regex_name.test(this.amenities_category_info.category_name)
                    this.valid.category_name = !valid_aname ? 'Name too short (At least 3 characters)' : ''
                    break

                case 'description':
                    var valid_cname = regex_description.test(this.amenities_category_info.description)
                    this.valid.description = !valid_cname ? 'Description too short (At least 10 characters)' : ''
                    break

                default:
                    break
            }
        },

        open_amenities_category_modal(is_new, amenities_category = null) {
            this.isModified = false

            if(is_new) {
                this.modal_new = true

                this.amenities_category_info.category_id = -1
                this.amenities_category_info.category_name = ''
                this.amenities_category_info.description = ''

                this.valid.category_name = ''
                this.valid.description = ''

                this.$bvModal.show('amenities_category_modal')
            }
            else {
                this.modal_new = false

                this.amenities_category_info.category_id = amenities_category.category_id
                this.amenities_category_info.category_name = amenities_category.category_name
                this.amenities_category_info.description = amenities_category.description

                this.$bvModal.show('amenities_category_modal')
            }
        },

        async save_amenities_category() {
            var all_valid = !(this.valid.category_name != '' || this.amenities_category_info.category_name == '') &&
                            !(this.valid.description != '' || this.amenities_category_info.description == '')

            if(!all_valid) {
                this.alert.confirm = false
                this.alert.message = 'Please fill all the required fields!'

                this.alert.okClicked = () => {
                    this.$bvModal.hide('amenities_category_alert')
                }

                this.$bvModal.show('amenities_category_alert')
                return
            }

            this.modalIsBusy = true

            var _amenities_category_response = await axios.post('/api/amenities_category/' + (this.modal_new ? 'create' : 'update'),
                    this.amenities_category_info
                )
                .then((resp) => {
                    this.modalIsBusy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('amenities_category_modal')
                        this.$bvModal.hide('amenities_category_alert')

                        this.amenities_category_info.category_name == ''
                        this.amenities_category_info.description == ''

                        this.getAllAmenitiesCategory()
                    }

                    this.$bvModal.show('amenities_category_alert')

                    return resp.data.message
                })
                .catch((err) => {
                    console.log(err)
                })
        },

        delete_amenities_category(category_id) {
            this.alert.confirm = true
            this.alert.message = 'Delete Amenities Category and its related Amenities record?'

            this.alert.okClicked = async () => {
                var delete_amenities_category_response = await axios.post('/api/amenities_category/delete', {
                        category_id: category_id
                    })
                    .then((resp) => {
                        this.modalIsBusy = false

                        this.alert.confirm = false
                        this.alert.message = resp.data.message

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('amenities_category_alert')
                            this.getAllAmenitiesCategory()
                        }

                        this.$bvModal.show('amenities_category_alert')

                        return resp.data.message
                    })
                    .catch((err) => {
                        console.log(err)
                    })


                this.$bvModal.hide('amenities_category_modal')
                this.$bvModal.hide('amenities_category_alert')
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('amenities_category_alert')
            }

            this.$bvModal.show('amenities_category_alert')
        },

        discard_changes(modal_id){
            if(this.isModified){
                this.alert.confirm = true
                this.alert.message = 'Discard Changes?'

                this.alert.okClicked = () => {
                    this.$bvModal.hide(modal_id)
                    this.$bvModal.hide('amenities_category_alert')
                }

                this.alert.cancelClicked = () => {
                    this.$bvModal.hide('amenities_category_alert')
                }

                this.$bvModal.show('amenities_category_alert')
            }
            else {
                this.$bvModal.hide(modal_id)
            }
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
