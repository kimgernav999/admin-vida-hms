<template>
    <div class="d-flex justify-content-center p-5">
        <div class="align-self-center signin-wrapper bg-white border shadow-sm rounded-sm p-5" @keydown.enter="signIn">
            <div class="mb-5 text-center">
                <img src="/storage/media/logo-vida.svg" class="mb-3 navbar-logo" alt="">
                <h3 class="font-weight-bold text-center">Administrator Login</h3>
            </div>
            <b-overlay :show="isBusy" opacity="0.3">
                <b-form class="my-3">
                    <b-form-group label="Username">
                        <b-form-input class="form-control rounded-pill" v-model="loginInfo.username"></b-form-input>
                    </b-form-group>
                    <b-form-group label="Password">
                        <b-form-input class="form-control rounded-pill" type="password" v-model="loginInfo.password"></b-form-input>
                    </b-form-group>
                    <b-form-group class="px-2">
                        <div class="d-flex justify-content-between">
                            <b-form-checkbox v-model="loginInfo.remember">
                                <span class="cbox-label">Remember Me</span>
                            </b-form-checkbox>
                            <a href="#" class="text-primary fpass-label" @click="$bvModal.show('forgot_pass_modal')">Forgot Password</a>
                        </div>
                    </b-form-group>
                    <b-form-group class="mt-4">
                        <b-button variant="primary" @click="signIn" :disabled="!(loginInfo.username && loginInfo.password)" block pill>Sign In</b-button>
                    </b-form-group>
                </b-form>
            </b-overlay>
        </div>
        <b-modal id="forgot_pass_modal" centered hide-footer>
            <b-overlay :show="modalIsBusy" opacity="0.3" class="mx-4 p-2">
                <i class="d-block fa fa-key text-center mb-2" style="font-size: 48px;"></i>
                <h4 class="font-weight-bold text-center mb-3">Forgot Password</h4>
                <h6 class="font-weight-lighter text-center text-black-60 mb-4">Send a mail to email address to reset password</h6>
                <b-form-group class="mb-4" label="Email Address">
                    <b-form-input type="email" class="form-control rounded-pill text-center" v-model="forgotPassInfo.email_address" @keyup="is_valid('email_address')"></b-form-input>
                    <span class="text-danger i-label" v-if="valid.email_address">{{ valid.email_address }}</span>
                </b-form-group>
                <div class="d-flex justify-content-center w-100">
                    <b-button class="m-2 w-50" size="sm" variant="primary" @click="send_passchange_mail()" :disabled="modalIsBusy || forgotPassInfo.email_address == '' || valid.email_address != ''" block pill>Done</b-button>
                </div>
            </b-overlay>
        </b-modal>
        <alert id="signin_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
            useUsername: true,
            loginInfo: {
                username: '',
                password: '',
                remember: false,
            },
            forgotPassInfo: {
                email_address: ''
            },
            valid: {
                email_address: ''
            },
            modalIsBusy: false,
            isBusy: false,
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
        async signIn() {
            this.isBusy = true

            var signin_response = await axios.post('/api/accounts/signin',
                    this.loginInfo
                ).then((resp) => {
                    this.isBusy = false

                    if(resp.data.username) {
                        this.$emit('signedin')
                    }
                    else {
                        this.alert.confirm = false
                        this.alert.message = resp.data.message

                        this.alert.okClicked = () => {
                            this.$bvModal.hide('signin_alert')
                        }

                        this.$bvModal.show('signin_alert')
                    }

                    return resp.data
                }).catch((err) => {
                    this.isBusy = true
                    console.log(err)
                })
        },

        is_valid(field) {
            var regex_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

            switch (field) {
                case 'email_address':
                    var valid_email = regex_email.test(this.forgotPassInfo.email_address)
                    this.valid.email_address = !valid_email ? 'Invalid email address' : ''
                    break
            }
        },

        async send_passchange_mail() {
            this.modalIsBusy = true

            var passchange_response = await axios.post('/api/token/passchange/create',
                    this.forgotPassInfo
                ).then((resp) => {
                    this.modalIsBusy = false

                    this.alert.confirm = false
                    this.alert.message = resp.data.message

                    this.alert.okClicked = () => {
                        this.$bvModal.hide('signin_alert')
                        this.$bvModal.hide('forgot_pass_modal')
                        this.forgotPassInfo.email_address = ''
                        this.valid.email_address = ''
                    }

                    this.$bvModal.show('signin_alert')

                    return resp.data
                }).catch((err) => {
                    this.isBusy = true
                    console.log(err)
                })
        }
    }
}
</script>

<style scoped>
    .navbar-logo {
        height: 90px;
    }

    .signin-wrapper {
        width: 420px;
    }

    .separator {
        border-bottom: solid 1px gray;
        height: 1px;
    }

    .cbox-label, .fpass-label {
        cursor: pointer;
        font-size: 12px;
    }

    .fpass-label:hover, .id-label:hover {
        color: #007BFF;
        text-decoration: none;
    }
</style>
