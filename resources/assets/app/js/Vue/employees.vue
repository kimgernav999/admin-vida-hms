<template>
    <div class="d-flex justify-content-center px-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <h3 class="mb-4">Employees</h3>
            <div class="border py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="col">
                        <b-button variant="success" class="rounded-circle" size="sm" title="New Employee" @click="$bvModal.show('new_employee')" v-b-tooltip.hover.noninteractive>
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" size="sm" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllEmployees">
                            <i class="fa fa-refresh"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" size="sm" placeholder="Search Employee"></b-input>
                    </div>
                </div>
                <div class="d-flex flex-column">
                    <b-table class="border-bottom vw-75" :busy="table.isBusy" :fields="table.fields" :filter="table.filter" :filter-included-fields="table.filter_on" :current-page="table.current_page" :items="table.items" striped responsive show-empty>
                        <template #cell(full_name)="row">
                            {{ row.item.first_name + ' ' + (row.item.middle_name ? row.item.middle_name[0] + '. ' : '') + row.item.last_name }}
                        </template>
                        <template #cell(email_address)="row">
                            {{ row.item.email_address }}
                        </template>
                        <template #cell(position)="row">
                            {{ row.item.position }}
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="success" class="rounded-circle mx-1" size="sm" title="Update Record" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-edit"></i>
                                </b-button>
                                <b-button variant="primary" class="rounded-circle mx-1" size="sm" title="Delete Record" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-trash"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
        <b-modal id="new_employee" title="New Employee" centered scrollable no-close-on-backdrop>
            <b-container fluid>
                <b-form class="my-3" @keydown.enter="register_employee()">
                    <h6 class="font-weight-lighter text-center text-black-70">Basic Information</h6>
                    <div class="font-weight-lighter mb-3 text-center text-danger w-100" style="font-size: 9px;">* Required Field</div>
                    <b-form-group label="First Name *">
                        <b-form-input class="form-control rounded-pill" v-model="account_info.first_name" @keyup="is_valid('first_name')"></b-form-input>
                        <span class="text-danger i-label" v-if="valid.first_name">{{ valid.first_name }}</span>
                    </b-form-group>
                    <b-form-group label="Last Name *">
                        <b-form-input class="form-control rounded-pill" v-model="account_info.last_name" @keyup="is_valid('last_name')"></b-form-input>
                        <span class="text-danger i-label" v-if="valid.lastname">{{ valid.lastname }}</span>
                    </b-form-group>
                    <b-form-group label="Email Address *">
                        <b-form-input type="email" class="form-control rounded-pill" v-model="account_info.email_address" @keyup="check_email"></b-form-input>
                        <span class="text-danger i-label" v-if="valid.email_address">{{ valid.email_address }}</span>
                    </b-form-group>
                    <b-form-group label="Position *">
                        <b-form-select class="form-control rounded-pill" v-model="account_info.position" :options="position_options" @change="is_valid('position')"></b-form-select>
                        <span class="text-danger i-label" v-if="valid.position">{{ valid.position }}</span>
                    </b-form-group>
                    <div class="d-flex mx-2 my-3">
                        <div class="col align-self-center separator separator-lightgray"></div>
                    </div>
                    <h6 class="font-weight-lighter text-center text-black-70">Login Credentials</h6>
                    <div class="font-weight-lighter mb-3 text-center text-danger w-100" style="font-size: 9px;">* Required Field</div>
                    <b-form-group label="Username *">
                        <b-form-input class="form-control rounded-pill" v-model="account_info.username" @keyup="check_username"></b-form-input>
                        <span class="text-danger i-label" v-if="valid.username">{{ valid.username }}</span>
                    </b-form-group>
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
                </b-form>
            </b-container>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button class="mx-2 w-50" size="sm" variant="danger" @click="$bvModal.hide('new_employee')" block pill>Cancel</b-button>
                    <b-button class="mx-2 w-50" size="sm" variant="primary" @click="register_employee()" block pill>Done</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            all_accounts: '',
            table: {
                isBusy: false,
                current_page: 1,
                filter: '',
                filter_on: ['full_name', 'email_address', 'position'],
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
                        key: 'position',
                        label: 'Position',
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
            account_info: {
                first_name: '',
                last_name: '',
                email_address: '',
                position: 'Select Position',
                username: '',
                password: '',
                password_confirmation: ''
            },
            position_options: ['Select Position', 'Receptionist', 'Housekeeper', 'Administrator'],
            valid: {
                first_name: '',
                last_name: '',
                email_address: '',
                position: '',
                username: '',
                password_length: '',
                password_cap: '',
                password_num: '',
                password_confirmation: ''
            },
            isBusy: false
        }
    },

    methods: {
        async getAllEmployees() {
            this.table.isBusy = true

            this.all_accounts = await axios.post('/api/accounts/allAccounts', {
                    user_role: 'employee'
                })
                .then((resp) => {
                    this.table.isBusy = false
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = []

            this.all_accounts.forEach((item) => {
                this.table.items.push(item.admin)
            })
        },

        is_valid(field) {
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

                case 'position':
                    this.valid.position = this.account_info.position == 'Select Position' ? 'Field is required' : ''
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
                    break

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
                this.valid.email_address = await axios.get('/api/accounts/checkEmail?email_address=' + this.account_info.email_address)
                    .then((resp) => {
                        return resp.data ? 'Email already taken' : null
                    }).catch((err) => {
                        console.log(err)
                    })
            }
        },

        check_username: async function () {
            this.is_valid('username')

            if(!this.valid.username) {
                this.valid.username = await axios.get('/api/accounts/checkUsername?username=' + this.account_info.username)
                    .then((resp) => {
                        return resp.data ? 'Username already taken' : null
                    }).catch((err) => {
                        console.log(err)
                    })
            }
        }
    },

    mounted() {
        this.getAllEmployees()
    }
}
</script>

<style scoped>
    .separator {
        border-bottom: solid 1px gray;
        height: 1px;
    }

    .separator-lightgray {
        border-bottom: solid 1px lightgray;
    }
</style>
