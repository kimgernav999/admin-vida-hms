<template>
    <div class="d-flex justify-content-center m-2 py-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-75">
            <h3 class="mb-5">Account Settings</h3>
            <b-row>
                <b-col class="px-3 py-3 text-center" xl>
                    <i class="fa fa-user-circle mb-4" style="font-size: 120px;"></i>
                    <h5 class="font-weight-bolder">{{ user_fullname }}</h5>
                    <h6 class="font-weight-bold">Registered since {{ user_regdate }}</h6>
                    <div class="mt-5">
                        <b-alert class="d-flex" variant="warning" show>
                            <span class="mt-1">Email not verified!</span>
                            <b-button class="ml-auto alert-btn" variant="secondary" size="sm" pill>Verify</b-button>
                        </b-alert>
                        <b-alert class="d-flex" variant="warning" show>
                            <span class="mt-1">No Paypal Account linked!</span>
                            <b-button class="ml-auto alert-btn" variant="secondary" size="sm" pill>Link</b-button>
                        </b-alert>
                    </div>
                </b-col>
                <b-col xl>
                    <b-card class="flex-grow-1" no-body>
                        <b-tabs card>
                            <b-tab title="Profile" active>
                                <b-overlay class="mx-auto p-4 w-xl-75" :show="pi_busy" opacity="0.3">
                                    <h6 class="font-weight-lighter text-center text-black-70">Profile Information</h6>
                                    <div class="font-weight-lighter mb-3 text-center text-danger w-100" style="font-size: 9px;">* Required Field</div>
                                    <b-form-group label="First Name *">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.first_name" @keyup="is_valid('first_name')"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.first_name">{{ valid.first_name }}</span>
                                    </b-form-group>
                                    <b-form-group label="Middle Name">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.middle_name"></b-form-input>
                                    </b-form-group>
                                    <b-form-group label="Last Name *">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.last_name" @keyup="is_valid('last_name')"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.lastname">{{ valid.lastname }}</span>
                                    </b-form-group>
                                    <b-form-group label="Email Address *">
                                        <b-form-input type="email" class="form-control rounded-pill" v-model="account_info.email_address" @keyup="check_email"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.email_address">{{ valid.email_address }}</span>
                                    </b-form-group>
                                    <b-form-group label="Mobile Number">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.mobile_number"></b-form-input>
                                    </b-form-group>
                                    <b-form-group class="mt-4">
                                        <b-button variant="primary" @click="update_profile" :disabled="(valid.first_name != '' || valid.last_name != '' || valid.email_address != '') && (account_info.first_name != '' || account_info.last_name != '' || account_info.email_address != '')" block pill>Save Changes</b-button>
                                    </b-form-group>
                                </b-overlay>
                            </b-tab>
                            <b-tab title="Login Credentials">
                                <b-overlay class="mx-auto p-4 w-xl-75" :show="lcu_busy" opacity="0.3">
                                    <h6 class="font-weight-lighter text-center text-black-70">Change Username</h6>
                                    <div class="font-weight-lighter mb-3 text-center text-danger w-100" style="font-size: 9px;">* Required Field</div>
                                    <b-form-group label="New Username *">
                                        <b-form-input class="form-control rounded-pill" v-model="uchange.username" @keyup="check_username"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.username">{{ valid.username }}</span>
                                    </b-form-group>
                                    <b-form-group label="Confirm Password *">
                                        <b-form-input type="password" class="form-control rounded-pill" v-model="uchange.password"></b-form-input>
                                    </b-form-group>
                                    <b-form-group class="mt-4">
                                        <b-button variant="primary" @click="update_username" :disabled="valid.username != '' || !uchange.password" block pill>Save Changes</b-button>
                                    </b-form-group>
                                </b-overlay>
                                <div class="mx-2 my-4 separator separator-lightgray"></div>
                                <b-overlay class="p-4 px-5" :show="lcp_busy" opacity="0.3">
                                    <h6 class="font-weight-lighter mb-3 text-center text-black-70">Change Username</h6>
                                    <b-form-group label="Old Password *">
                                        <b-form-input type="password" class="form-control rounded-pill" v-model="pchange.old_password"></b-form-input>
                                    </b-form-group>
                                    <b-form-group label="New Password *">
                                        <b-form-input type="password" class="form-control rounded-pill" v-model="pchange.new_password" @keyup="is_valid('password')"></b-form-input>
                                        <div class="text-danger i-label">
                                            <div v-if="valid.password_length || valid.password_cap || valid.password_num">Password should contain atleast</div>
                                            <div class="ml-2" v-if="valid.password_length">9 characters</div>
                                            <div class="ml-2" v-if="valid.password_cap">1 capital letter</div>
                                            <div class="ml-2" v-if="valid.password_num">1 number</div>
                                        </div>
                                    </b-form-group>
                                    <b-form-group label="Confirm New Password *">
                                        <b-form-input type="password" class="form-control rounded-pill" v-model="pchange.password_confirmation" @keyup="is_valid('password_confirmation')"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.password_confirmation">{{ valid.password_confirmation }}</span>
                                    </b-form-group>
                                    <b-form-group class="mt-4">
                                        <b-button variant="primary" @click="update_password" :disabled="!pchange.old_password || valid.password_length != '' || valid.password_cap != '' || valid.password_num != '' || valid.password_confirmation != ''" block pill>Save Changes</b-button>
                                    </b-form-group>
                                </b-overlay>
                            </b-tab>
                        </b-tabs>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <alert :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            user_fullname: 'N/A',
            user_regdate: 'N/A',
            account_info: {
                first_name: '',
                middle_name: '',
                last_name: '',
                email_address: '',
                mobile_number: ''
            },
            uchange: {
                username: '',
                password: ''
            },
            pchange: {
                old_password: '',
                new_password: '',
                password_confirmation: ''
            },
            valid: {
                first_name: '',
                last_name: '',
                email_address: '',
                username: '',
                password_length: '',
                password_cap: '',
                password_num: '',
                password_confirmation: ''
            },
            pi_busy: false,
            lcu_busy: false,
            lcp_busy: false,
            alert: {
                show: false,
                title: 'Notice',
                message: '',
                confirm: false,
                okText: 'Ok',
                cancelText: 'Cancel',
                okClicked: () => {this.alert.show = false},
                cancelClicked: () => {this.alert.show = false},
            }
        }
    },

    methods: {
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

                case 'username':
                    var valid_username = regex_unamelength.test(this.uchange.username)
                    this.valid.username = !valid_username ? 'Userame too short (At least 6 characters)' : ''
                    break

                case 'password':
                    var valid_passlength = regex_passlength.test(this.pchange.new_password)
                    var valid_passcap = regex_passcap.test(this.pchange.new_password)
                    var valid_passnum = regex_passnum.test(this.pchange.new_password)
                    this.valid.password_length = !valid_passlength
                    this.valid.password_cap = !valid_passcap
                    this.valid.password_num = !valid_passnum
                    break

                case 'password_confirmation':
                    var valid_passconfirm = this.pchange.new_password == this.pchange.password_confirmation
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
                this.valid.username = await axios.get('/api/accounts/checkUsername?username=' + this.uchange.username)
                    .then((resp) => {
                        return resp.data ? 'Username already taken' : null
                    }).catch((err) => {
                        console.log(err)
                    })
            }
        },

        async update_profile() {
            var uprofile_reponse = await axios.post('/api/accounts/updateProfile', this.account_info)
                .then((resp) => {
                    this.$emit('profilechanged')
                    this.getCurrentUser()
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            alert(uprofile_reponse.message)
        },

        async update_username() {
            var uusername_reponse = await axios.post('/api/accounts/updateUsername', this.uchange)
                .then((resp) => {
                    this.$emit('profilechanged')
                    this.getCurrentUser()
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            alert(uusername_reponse.message)

            this.uchange.password = ''
        },

        async update_password() {
            var upassword_reponse = await axios.post('/api/accounts/updatePassword', this.pchange)
                .then((resp) => {
                    this.$emit('profilechanged')
                    this.getCurrentUser()
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            alert(upassword_reponse.message)

            this.pchange.old_password = ''
            this.pchange.new_password = ''
            this.pchange.password_confirmation = ''
        },

        async getCurrentUser() {
            var curruser_reponse = await axios.post('/api/accounts/current')
                .then((resp) => {
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            if(curruser_reponse.username) {
                var user_info = curruser_reponse.current.user

                this.user_fullname = user_info.first_name + ' ' + (user_info.middle_name ? user_info.middle_name[0] + '. ' : '') + user_info.last_name
                this.user_regdate = this.$moment(user_info.created_at).format('LL')
                this.account_info.first_name = user_info.first_name
                this.account_info.middle_name = user_info.middle_name
                this.account_info.last_name = user_info.last_name
                this.account_info.email_address = user_info.email_address
                this.account_info.mobile_number = user_info.mobile_number
                this.uchange.username = curruser_reponse.username
            }
        }
    },

    mounted() {
        this.getCurrentUser()
    }
}
</script>

<style scoped>
    .alert-btn {
        min-width: 100px;
    }

    .border-divider {
        border-right: solid 3px lightgray;
    }

    .separator {
        border-bottom: solid 1px gray;
        height: 1px;
    }

    .separator-lightgray {
        border-bottom: solid 1px lightgray;
    }
</style>
