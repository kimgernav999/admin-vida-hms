<template>
    <div class="d-flex justify-content-center p-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <h3 class="mb-5">Account Settings</h3>
            <b-row>
                <b-col class="px-3 py-3 text-center" xl>
                    <i class="fa fa-user-circle mb-4" style="font-size: 120px;"></i>
                    <h5 class="font-weight-bolder">{{ user_fullname }}</h5>
                    <h6 class="font-weight-bold">Registered since {{ user_regdate }}</h6>
                    <div class="d-none mt-5" id="alerts">
                        <b-alert class="d-flex" variant="warning"  :show="!account_info.email_address_verified">
                            <span class="mt-1">Email not verified!</span>
                            <b-button class="ml-auto alert-btn" variant="secondary" size="sm" :disabled="ev_busy" @click="send_emailverify_mail" pill>
                                <i :class="'fa fa-spinner' + (ev_busy ? ' mr-1 fa-spin' : 'd-none')"></i>
                                Verify
                            </b-button>
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
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.first_name" :disabled="username == 'superadmin'" @keyup="is_valid('first_name')"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.first_name">{{ valid.first_name }}</span>
                                    </b-form-group>
                                    <b-form-group label="Middle Name" v-if="username != 'superadmin'">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.middle_name"></b-form-input>
                                    </b-form-group>
                                    <b-form-group label="Last Name *">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.last_name" :disabled="username == 'superadmin'" @keyup="is_valid('last_name')"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.lastname">{{ valid.lastname }}</span>
                                    </b-form-group>
                                    <b-form-row v-if="username != 'superadmin'">
                                        <b-form-group class="col" label="Gender *">
                                            <b-form-select class="form-control rounded-pill" v-model="account_info.gender" :options="gender_options" @change="is_valid('gender')"></b-form-select>
                                            <span class="text-danger i-label" v-if="valid.gender">{{ valid.gender }}</span>
                                        </b-form-group>
                                        <b-form-group class="col" label="Birth Date *">
                                            <b-form-input type="date" class="form-control rounded-pill" v-model="account_info.birth_date" @change="is_valid('birth_date')"></b-form-input>
                                            <span class="text-danger i-label" v-if="valid.birth_date">{{ valid.birth_date }}</span>
                                        </b-form-group>
                                    </b-form-row>
                                    <b-form-group :label="'Email Address *' + (account_info.email_address_verified ? ' (Verified)' : ' (Not Verified)')">
                                        <b-form-input type="email" class="form-control rounded-pill" v-model="account_info.email_address" @keyup="check_email"></b-form-input>
                                        <span class="text-danger i-label" v-if="valid.email_address">{{ valid.email_address }}</span>
                                    </b-form-group>
                                    <b-form-group label="Mobile Number">
                                        <b-form-input class="form-control rounded-pill" v-model="account_info.mobile_number"></b-form-input>
                                    </b-form-group>
                                    <b-form-group label="Position *">
                                        <b-form-select class="form-control rounded-pill" v-model="account_info.position" :options="position_options" :disabled="username == 'superadmin'" @change="is_valid('position')"></b-form-select>
                                        <span class="text-danger i-label" v-if="valid.position">{{ valid.position }}</span>
                                    </b-form-group>
                                    <b-form-group class="mt-4">
                                        <b-button variant="primary" @click="update_profile" :disabled="(valid.first_name != '' && account_info.first_name == '') || (valid.last_name != '' && account_info.last_name == '') || (valid.email_address != '' && account_info.email_address == '') || (valid.birth_date != '' && account_info.birth_date == '') || (valid.gender != '' && account_info.gender == 'Select Gender')" block pill>Save Changes</b-button>
                                    </b-form-group>
                                </b-overlay>
                            </b-tab>
                            <b-tab title="Login Credentials">
                                <b-overlay class="mx-auto p-4 w-xl-75" :show="lcu_busy" opacity="0.3" v-if="username != 'superadmin'">
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
                                    <h6 class="font-weight-lighter mb-3 text-center text-black-70">Change Password</h6>
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
        <alert id="settings_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            username: 'N/A',
            user_fullname: 'N/A',
            user_regdate: 'N/A',
            account_info: {
                first_name: '',
                middle_name: '',
                last_name: '',
                gender: 'Select Gender',
                birth_date: '',
                email_address: '',
                old_email_address: '',
                email_address_verified: false,
                mobile_number: '',
                position: ''
            },
            gender_options: ['Select Gender', 'Male', 'Female'],
            position_options: ['Select Position', 'Receptionist', 'Housekeeper', 'Administrator'],
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
                gender: '',
                birth_date: '',
                email_address: '',
                position: '',
                username: '',
                password_length: '',
                password_cap: '',
                password_num: '',
                password_confirmation: ''
            },
            ev_busy: false,
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
                okClicked: () => {this.$bvModal.hide('settings_alert')},
                cancelClicked: () => {this.$bvModal.hide('settings_alert')},
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

                case 'gender':
                    this.valid.gender = this.account_info.gender == 'Select Gender' ? 'Field is required' : ''
                    break

                case 'birth_date':
                    this.valid.birth_date = !this.account_info.birth_date ? 'Field is required' : ''
                    break

                case 'position':
                    this.valid.position = this.account_info.position == 'Select Position' ? 'Field is required' : ''
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
                this.valid.email_address = await axios.get('/api/accounts/checkEmail?email_address=' + this.account_info.email_address + '&user_role=employee&self=1')
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
            document.getElementById('alerts').classList.add('d-none')

            var uprofile_reponse = await axios.post('/api/accounts/updateProfile', this.account_info)
                .then((resp) => {
                    this.$emit('profilechanged')
                    this.getCurrentUser()
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            this.alert.confirm = false
            this.alert.message = uprofile_reponse.message

            this.alert.okClicked = () => {
                this.$bvModal.hide('settings_alert')
            }

            this.$bvModal.show('settings_alert')
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

            this.alert.confirm = false
            this.alert.message = uusername_reponse.message

            this.alert.okClicked = () => {
                this.uchange.password = ''

                this.$bvModal.hide('settings_alert')
            }

            this.$bvModal.show('settings_alert')
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

            this.alert.confirm = false
            this.alert.message = upassword_reponse.message

            this.alert.okClicked = () => {
                this.pchange.old_password = ''
                this.pchange.new_password = ''
                this.pchange.password_confirmation = ''

                this.$bvModal.hide('settings_alert')
            }

            this.$bvModal.show('settings_alert')
        },

        async getCurrentUser() {
            var curruser_reponse = await axios.post('/api/accounts/current')
                .then((resp) => {
                    if(resp.data.username) {
                        var user_info = resp.data.current.admin

                        this.username = resp.data.current.username
                        this.uchange.username = resp.data.username
                        this.user_fullname = user_info.first_name + ' ' + (user_info.middle_name ? user_info.middle_name[0] + '. ' : '') + user_info.last_name
                        this.user_regdate = this.$moment(user_info.created_at).format('LL')
                        this.account_info.first_name = user_info.first_name
                        this.account_info.middle_name = user_info.middle_name
                        this.account_info.last_name = user_info.last_name
                        this.account_info.gender = user_info.gender
                        this.account_info.birth_date = user_info.birth_date
                        this.account_info.email_address = user_info.email_address
                        this.account_info.old_email_address = user_info.email_address
                        this.account_info.email_address_verified = user_info.email_address_verified
                        this.account_info.mobile_number = user_info.mobile_number
                        this.account_info.position = user_info.position
                    }

                    setTimeout(() => {
                        document.getElementById('alerts').classList.remove('d-none')
                    }, 1000);

                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })
        },

        async send_emailverify_mail() {
            this.ev_busy = true

            var emailverify_response = await axios.post('/api/token/emailverify/create',
                    this.emailVerifyInfo
                ).then((resp) => {
                    this.ev_busy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('settings_alert')
                        this.emailVerifyInfo.email_address = ''
                        this.$router.push('/')
                    }

                    this.$bvModal.show('settings_alert')

                    return resp.data
                }).catch((err) => {
                    this.ev_busy = false
                    console.log(err)
                })
        }
    },

    computed: {
        emailVerifyInfo() {
            return {
                email_address: this.account_info.old_email_address
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
