<template>
    <div class="d-flex justify-content-center p-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <h3 class="mb-4">Guests</h3>
            <div class="border py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="justify-content-center col-auto">
                        <b-button variant="success" class="rounded-circle" title="New Guest" @click="open_guest_modal(true)" v-b-tooltip.hover.noninteractive>
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllGuests">
                            <i :class="'fa fa-refresh' + (table.isBusy ? ' fa-spin' : '')"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" placeholder="Search Guest"></b-input>
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
                            {{ row.item.first_name + ' ' + (row.item.middle_name ? row.item.middle_name[0] + '. ' : '') + row.item.last_name }}
                        </template>
                        <template #cell(email_address)="row">
                            {{ row.item.email_address + (row.item.email_address_verified ? ' (Verified)' : ' (Not Verified)') }}
                        </template>
                        <template #cell(paypal_linked)="row">
                            {{ row.item.paypal_id ? 'Yes' : 'No' }}
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="success" class="rounded-lg mx-1" size="sm" title="View / Update Record" @click="open_guest_modal(false, row.item)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-edit"></i>
                                </b-button>
                                <b-button variant="primary" class="rounded-lg mx-1" size="sm" title="Delete Record" @click="delete_guest(row.item.account_id)" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-trash"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
        <b-modal id="guest_modal" :title="(modal_new ? 'New' : 'View / Update') + ' Guest Record'" centered scrollable no-close-on-backdrop hide-header-close>
            <b-overlay :show="modalIsBusy" opacity="0.3">
                <b-container fluid>
                    <div class="d-flex justify-content-center mb-4">
                        <i class="fa fa-user-circle my-3" style="font-size: 120px;"></i>
                    </div>
                    <b-form class="my-3" @keydown.enter="save_guest()">
                        <h6 class="text-success font-weight-bold text-black-70 text-black-70">Basic Information</h6>
                        <div class="font-weight-lighter ml-2 mb-3 text-danger w-100" style="font-size: 9px;">* Required Field</div>
                        <b-form-group label="First Name *">
                            <b-form-input class="form-control rounded-pill" v-model="account_info.first_name" @keyup="is_valid('first_name')"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.first_name">{{ valid.first_name }}</span>
                        </b-form-group>
                        <b-form-group label="Middle Name">
                            <b-form-input class="form-control rounded-pill" v-model="account_info.middle_name"></b-form-input>
                        </b-form-group>
                        <b-form-row>
                            <b-form-group class="col" label="Gender *">
                                <b-form-select class="form-control rounded-pill" v-model="account_info.gender" :options="gender_options" @change="is_valid('gender')"></b-form-select>
                                <span class="text-danger i-label" v-if="valid.gender">{{ valid.gender }}</span>
                            </b-form-group>
                            <b-form-group class="col" label="Birth Date *">
                                <b-form-input type="date" class="form-control rounded-pill" v-model="account_info.birth_date" @change="is_valid('birth_date')"></b-form-input>
                                <span class="text-danger i-label" v-if="valid.birth_date">{{ valid.birth_date }}</span>
                            </b-form-group>
                        </b-form-row>
                        <b-form-group label="Last Name *">
                            <b-form-input class="form-control rounded-pill" v-model="account_info.last_name" @keyup="is_valid('last_name')"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.last_name">{{ valid.last_name }}</span>
                        </b-form-group>
                        <b-form-group label="Email Address *">
                            <b-form-input type="email" class="form-control rounded-pill" v-model="account_info.email_address" @keyup="check_email"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.email_address">{{ valid.email_address }}</span>
                        </b-form-group>
                        <b-form-group label="Mobile Number" class="mb-5">
                            <b-form-input class="form-control rounded-pill" v-model="account_info.mobile_number"></b-form-input>
                        </b-form-group>
                        <h6 class="text-success font-weight-bold text-black-70">Login Credentials</h6>
                        <div class="font-weight-lighter ml-2 mb-3 text-danger w-100" style="font-size: 9px;">* Required Field</div>
                        <b-form-group label="Username *">
                            <b-form-input class="form-control rounded-pill" v-model="account_info.username" @keyup="check_username"></b-form-input>
                            <span class="text-danger i-label" v-if="valid.username">{{ valid.username }}</span>
                        </b-form-group>
                        <b-form-group class="mb-3">
                            <div v-if="!same_password || modal_new">
                                <b-form-group label="Password *">
                                    <b-form-input type="password" class="form-control rounded-pill" v-model="account_info.password" @keyup="is_valid('password')"></b-form-input>
                                    <div class="text-danger i-label">
                                        <div v-if="valid.password_length || valid.password_cap || valid.password_num">Password should contain atleast</div>
                                        <div class="ml-2" v-if="valid.password_length">9 characters</div>
                                        <div class="ml-2" v-if="valid.password_cap">1 capital letter</div>
                                        <div class="ml-2" v-if="valid.password_num">1 number</div>
                                    </div>
                                </b-form-group>
                                <b-form-group label="Confirm Password *">
                                    <b-form-input type="password" class="form-control rounded-pill" v-model="account_info.password_confirmation" @keyup="is_valid('password_confirmation')"></b-form-input>
                                    <span class="text-danger i-label" v-if="valid.password_confirmation">{{ valid.password_confirmation }}</span>
                                </b-form-group>
                            </div>
                            <b-form-checkbox v-model="same_password" v-if="!modal_new">
                                <span class="cbox-label">
                                    Same password as before?
                                </span>
                            </b-form-checkbox>
                        </b-form-group>
                    </b-form>
                </b-container>
            </b-overlay>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button class="m-2 w-50" size="sm" variant="danger" @click="discard_changes('guest_modal')" :disabled="modalIsBusy" block pill>Cancel</b-button>
                    <b-button class="m-2 w-50" size="sm" variant="primary" @click="save_guest()" :disabled="modalIsBusy" block pill>Done</b-button>
                </div>
            </template>
        </b-modal>
        <alert id="guests_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            all_accounts: '',
            table: {
                current_page: 1,
                filter: '',
                filter_on: ['username', 'full_name', 'email_address'],
                fields: [
                    {
                        key: 'full_name',
                        label: 'Full Name',
                        sortable: true
                    },
                    {
                        key: 'email_address',
                        label: 'Email Address',
                        sortable: true
                    },
                    {
                        key: 'paypal_linked',
                        label: 'Paypal Linked',
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
            account_info: {
                sel_username: '',
                first_name: '',
                last_name: '',
                middle_name: '',
                gender: 'Select Gender',
                birth_date: '',
                email_address: '',
                mobile_number: '',
                user_role: 'guest',
                username: '',
                password: '',
                password_confirmation: ''
            },
            same_password: true,
            gender_options: ['Select Gender', 'Male', 'Female'],
            valid: {
                account_id: -1,
                first_name: '',
                last_name: '',
                gender: '',
                birth_date: '',
                email_address: '',
                username: '',
                password_length: '',
                password_cap: '',
                password_num: '',
                password_confirmation: ''
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
        async getAllGuests() {
            this.table.isBusy = true

            this.all_accounts = await axios.post('/api/accounts/allAccounts', {
                    user_role: 'guest'
                })
                .then(function (resp){
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = []

            this.all_accounts.forEach((item) => {
                var account = item.user

                account['account_id'] = item.account_id
                account['username'] = item.username

                this.table.items.push(account)
            })

            this.table.isBusy = false
        },

        is_valid(field) {
            this.isModified = true

            var regex_name = /^.{2,}$/
            var regex_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            var regex_unamelength = /^.{6,}$/
            var regex_passlength = /^.{8,}$/
            var regex_passcap = /[A-Z]/
            var regex_passnum = /[0-9]/

            switch (field) {
                case 'first_name':
                    var valid_fname = regex_name.test(this.account_info.first_name)
                    this.valid.first_name = !valid_fname ? 'Name too short (At least 2 characters)' : ''
                    break

                case 'last_name':
                    var valid_lname = regex_name.test(this.account_info.last_name)
                    this.valid.last_name = !valid_lname ? 'Name too short (At least 2 characters)' : ''
                    break

                case 'email_address':
                    var valid_email = regex_email.test(this.account_info.email_address)
                    this.valid.email_address = !valid_email ? 'Invalid email address' : ''
                    break

                case 'gender':
                    this.valid.gender = this.account_info.gender == 'Select Gender' ? 'Field is required' : ''
                    break

                case 'birth_date':
                    this.valid.birth_date = !this.account_info.birth_date ? 'Field is required' : ''
                    break

                case 'username':
                    var valid_username = regex_unamelength.test(this.account_info.username)
                    this.valid.username = !valid_username ? 'Userame too short (At least 6 characters)' : ''
                    break

                case 'password':
                    var valid_passlength = regex_passlength.test(this.account_info.password)
                    var valid_passcap = regex_passcap.test(this.account_info.password)
                    var valid_passnum = regex_passnum.test(this.account_info.password)
                    this.valid.password_length = !valid_passlength
                    this.valid.password_cap = !valid_passcap
                    this.valid.password_num = !valid_passnum

                case 'password_confirmation':
                    var valid_passconfirm = this.account_info.password == this.account_info.password_confirmation
                    this.valid.password_confirmation = !valid_passconfirm ? 'New password doesn\'t match' : ''
                    break

                default:
                    break
            }
        },

        check_email: async function () {
            this.is_valid('email_address')

            if(!this.valid.email_address) {
                this.valid.email_address = await axios.get('/api/accounts/checkEmail?email_address=' + this.account_info.email_address + '&user_role=guest&username=' + this.account_info.sel_username)
                    .then((resp) => {
                        return resp.data ? 'Email already taken' : ''
                    }).catch((err) => {
                        console.log(err)
                    })
            }
        },

        check_username: async function () {
            this.is_valid('username')

            if(!this.valid.username) {
                this.valid.username = await axios.get('/api/accounts/checkUsername?username=' + this.account_info.username + '&old_username=' + this.account_info.sel_username)
                    .then((resp) => {
                        return resp.data ? 'Username already taken' : ''
                    }).catch((err) => {
                        console.log(err)
                    })
            }
        },

        open_guest_modal(is_new, account = null) {
            this.isModified = false

            if(is_new) {
                this.modal_new = true

                this.account_info.first_name = ''
                this.account_info.last_name = ''
                this.account_info.middle_name = ''
                this.account_info.email_address = ''
                this.account_info.mobile_number = ''
                this.account_info.username = ''
                this.account_info.password = ''
                this.account_info.password_confirmation = ''

                this.valid.first_name = ''
                this.valid.last_name = ''
                this.valid.email_address = ''
                this.valid.username = ''
                this.valid.password_length = ''
                this.valid.password_cap = ''
                this.valid.password_num = ''
                this.valid.password_confirmation = ''

                this.$bvModal.show('guest_modal')
            }
            else {
                this.modal_new = false

                this.account_info.account_id = account.account_id
                this.account_info.first_name = account.first_name
                this.account_info.last_name = account.last_name
                this.account_info.middle_name = account.middle_name
                this.account_info.gender = account.gender
                this.account_info.birth_date = account.birth_date
                this.account_info.email_address = account.email_address
                this.account_info.mobile_number = account.mobile_number
                this.account_info.username = account.username
                this.account_info.sel_username = account.username

                this.$bvModal.show('guest_modal')
            }
        },

        async save_guest() {
            var all_valid = !(this.valid.first_name != '' || this.account_info.first_name == '') &&
                            !(this.valid.last_name != '' || this.account_info.last_name == '') &&
                            !(this.valid.gender != '' || this.account_info.gender == 'Select Gender') &&
                            !(this.valid.birth_date != '' || this.account_info.birth_date == '') &&
                            !(this.valid.email_address != '' || this.account_info.email_address == '') &&
                            !(this.valid.username != '' || this.account_info.username == '')

            var valid_password = !(
                                        (
                                            this.valid.password_length != '' ||
                                            this.valid.password_cap != '' ||
                                            this.valid.password_num != ''
                                        ) || this.account_info.password == ''
                                    ) &&
                                    !(this.valid.password_confirmation != '' || this.account_info.password_confirmation == '')

            var valid = false

            valid = all_valid

            if(!this.modal_new) {
                if(this.same_password) {
                    this.account_info.password = ''
                    this.account_info.password_confirmation = ''
                } else {
                    valid = valid_password
                }
            }

            if(!valid) {
                this.alert.confirm = false
                this.alert.message = 'Please fill all the required fields!'

                this.alert.okClicked = () => {
                    this.$bvModal.hide('guests_alert')
                }

                this.$bvModal.show('guests_alert')
                return
            }

            this.modalIsBusy = true

            var _guest_response = await axios.post('/api/accounts/' + (this.modal_new ? 'create' : 'updateProfile'),
                    this.account_info
                )
                .then((resp) => {
                    this.modalIsBusy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('guest_modal')
                        this.$bvModal.hide('guests_alert')

                        this.account_info.first_name = ''
                        this.account_info.last_name = ''
                        this.account_info.middle_name = ''
                        this.account_info.email_address = ''
                        this.account_info.mobile_number = ''
                        this.account_info.username = ''
                        this.account_info.password = ''
                        this.account_info.password_confirmation = ''

                        this.getAllGuests()
                    }

                    this.$bvModal.show('guests_alert')

                    return resp.data.message
                })
                .catch((err) => {
                    console.log(err)
                })
        },

        delete_guest(account_id) {
            this.alert.confirm = true
            this.alert.message = 'Delete Guest record?'

            this.alert.okClicked = async () => {
                var delete_guest_response = await axios.post('/api/accounts/delete', {
                        account_id: account_id
                    })
                    .then((resp) => {
                        this.modalIsBusy = false

                        this.alert.confirm = false
                        this.alert.message = resp.data.message

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('guests_alert')
                            this.getAllGuests()
                        }

                        this.$bvModal.show('guests_alert')

                        return resp.data.message
                    })
                    .catch((err) => {
                        console.log(err)
                    })
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('guests_alert')
            }

            this.$bvModal.show('guests_alert')
        },

        discard_changes(modal_id){
            if(this.isModified){
                this.alert.confirm = true
                this.alert.message = 'Discard Changes?'

                this.alert.okClicked = () => {
                    this.$bvModal.hide(modal_id)
                    this.$bvModal.hide('guests_alert')
                }

                this.alert.cancelClicked = () => {
                    this.$bvModal.hide('guests_alert')
                }

                this.$bvModal.show('guests_alert')
            }
            else {
                this.$bvModal.hide(modal_id)
            }
        }
    },

    mounted() {
        this.getAllGuests()
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
