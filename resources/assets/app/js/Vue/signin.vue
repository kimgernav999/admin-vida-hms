<template>
    <div class="d-flex justify-content-center px-5">
        <div class="align-self-center signin-wrapper bg-white border shadow-sm rounded-sm p-5" @keydown.enter="signIn">
            <div class="mb-5 text-center">
                <img src="/storage/media/logo-vida.svg" class="mb-3 navbar-logo" alt="">
                <h2 class="font-weight-bold text-center">Administrator Login</h2>
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
                            <a href="#" class="text-primary fpass-label">Forgot Password</a>
                        </div>
                    </b-form-group>
                    <b-form-group class="mt-4">
                        <b-button variant="primary" @click="signIn" :disabled="!(loginInfo.username && loginInfo.password)" block pill>Sign In</b-button>
                    </b-form-group>
                </b-form>
            </b-overlay>
        </div>
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
                            this.alert.show = false
                        }

                        this.alert.show = true
                    }

                    return resp.data
                }).catch((err) => {
                    this.isBusy = true
                    console.log(err)
                })
        },
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
