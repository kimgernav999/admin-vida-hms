<template>
    <div class="d-flex justify-content-center m-2 py-5">
        <div class="align-self-center signup-wrapper bg-white border shadow-sm rounded-sm p-5">
            <b-overlay :show="isBusy">
                <div class="mb-5">
                    <h2 class="font-weight-bold text-center">Create Account</h2>
                    <h6 class="font-weight-lighter text-center text-black-50">Do more, book more! Create and account now to book reservations at our hotel.</h6>
                </div>
                <b-form class="my-3" v-if="step == 0">
                    <b-form-group class="mt-4">
                        <b-button variant="primary" @click="signUp(++step)" block pill>Continue</b-button>
                    </b-form-group>
                </b-form>
                <b-form class="my-3" v-if="step == 1" @keydown.enter="signUp(2)">
<<<<<<< HEAD
                    <h6 class="font-weight-lightertext-center text-black-70">Basic Information</h6>
=======
                    <h6 class="font-weight-lighter text-center text-black-70">Basic Information</h6>
>>>>>>> fbd520de73869736d783d9f058c62881b5369de3
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
                    <b-form-group>
                        <b-form-checkbox v-model="accept_terms">
                            <span class="cbox-label">
                                I have read and agree to the <router-link class="terms-label" :to="{ name:'termsconditions' }">Terms and Conditions</router-link>.
                            </span>
                        </b-form-checkbox>
                    </b-form-group>
                    <div class="d-flex mx-2 my-3">
                        <div class="col align-self-center separator separator-lightgray"></div>
                    </div>
                    <div class="mb-4 px-2">
                        <span class="d-block font-weight-lighter text-center text-black-50 note-text">Please visit our <router-link class="priv-label" :to="{ name: 'privacypolicy' }">Privacy Policy</router-link> to understand how we handle your personal data</span>
                    </div>
                    <b-form-group class="mt-4">
                        <b-button variant="primary" @click="signUp(2)" block pill>Next</b-button>
                    </b-form-group>
                </b-form>
                <b-form class="my-3" v-if="step == 2" @keydown.enter="signUp(3)">
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
                    <b-form-group class="mt-4">
                        <b-button variant="primary" @click="signUp(3)" block pill>Done</b-button>
                    </b-form-group>
                </b-form>
                <div v-if="step == 0">
                    <div class="d-flex my-4">
                        <div class="col align-self-center separator"></div>
                        <span class="col-auto align-self-center px-1">or</span>
                        <div class="col align-self-center separator"></div>
                    </div>
                    <div class="mb-5">
                        <h2 class="font-weight-bold text-center">Sign In</h2>
                        <h6 class="font-weight-lighter text-center text-black-50">Already have an account? Sign In now!</h6>
                    </div>
                    <b-form-group class="mt-4">
                        <router-link class="btn btn-block btn-warning rounded-pill" :to="{ name: 'signin' }">Sign In</router-link>
                    </b-form-group>
                </div>
            </b-overlay>
        </div>
        <b-modal id="emailVerify" centered scrollable hide-header hide-footer no-close-on-backdrop>
            Test
        </b-modal>
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
            completed: [false, false],
            step: 0,
            account_info: {
                first_name: '',
                last_name: '',
                email_address: '',
                username: '',
                password: '',
                password_confirmation: ''
            },
            accept_terms: false,
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
            username_status: '',
            isBusy: false,
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
        async signUp(step) {
            switch (step) {
                case 1:
                    this.step = 1
                    break

                case 2:
                    this.completed[0] = this.account_info.first_name && this.account_info.last_name && this.account_info.email_address

                    if(this.completed[0]){
                        if(this.accept_terms) {
                            this.step = 2
                        }
                        else {
                            this.alert.confirm = false
                            this.alert.message = 'Please agree to the Terms and Conditions to continue!'

                            this.alert.okClicked = () => {
                                this.alert.show = false
                            }

                            this.alert.show = true
                        }
                    }
                    else {
                        this.alert.confirm = false
                        this.alert.message = 'Please complete all fields!'

                        this.alert.okClicked = () => {
                            this.alert.show = false
                        }

                        this.alert.show = true
                    }
                    break

                case 3:
                    this.completed[1] = this.account_info.username && this.account_info.password && this.account_info.password_confirmation

                    if(this.completed[1]){
                        this.isBusy = true

                        var register_response = await axios.post('/api/accounts/signup',
                                this.account_info
                            ).then((resp) => {
                                this.isBusy = false

                                this.alert.confirm = false
                                this.alert.message = resp.data.message

                                this.alert.okClicked = () => {
                                    this.alert.show = false
                                    this.$emit('signedin')
                                }

                                this.alert.show = true

                                return resp.data
                            }).catch((err) => {
                                console.log(err)
                            })
                    }
                    else {
                        this.alert.confirm = false
                        this.alert.message = 'Please complete all fields!'

                        this.alert.okClicked = () => {
                            this.alert.show = false
                        }

                        this.alert.show = true
                    }
                    break

                default:
                    break
            }
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

    watch: {

    }
}
</script>

<style scoped>
    .signup-wrapper {
        width: 420px;
    }

    .separator {
        border-bottom: solid 1px gray;
        height: 1px;
    }

    .separator-lightgray {
        border-bottom: solid 1px lightgray;
    }

    .cbox-label, .fpass-label, .terms-label, .priv-label {
        font-size: 14px;
    }

    .priv-label {
        font-size: 13px;
    }

    .note-text {
        font-size: 13px;
    }

    .fpass-label:hover, .terms-label:hover, .priv-label:hover {
        color: #007BFF;
        text-decoration: none;
    }
</style>
