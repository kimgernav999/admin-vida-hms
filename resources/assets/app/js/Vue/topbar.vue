<template>
    <nav class="navbar navbar-expand-xl navbar-light bg-light fixed-top p-0 shadow-sm">
        <a class="d-flex navbar-brand ml-3 p-0 h-100">
            <div class="my-3 p-2 no-arrow" @click="$emit('togglesidebar')">
                <i class="fa fa-bars text-secondary"></i>
            </div>
            <img :src="logoUrl" class="align-self-center navbar-logo" alt="">
        </a>

        <ul class="navbar-nav ml-auto mx-4">
            <div :class="'nav-item position-relative m-0 ' + (location == '/settings' ? ' active' : '')" data-target="#profilemenu" data-toggle="collapse">
                <a class="d-flex nav-link font-weight-bold text-dark" href="javascript:void(0)">
                    <i class="fa fa-user-circle mr-2" style="font-size: 32px;"></i>
                    <div style="line-height: 32px; vertical-align: center;">{{ userFullname }}</div>
                </a>
                <div id="profilemenu" class="bg-white border-light dropdown-menu dropdown-menu-right mt-2 p-2 position-absolute shadow">
                    <div class="d-flex flex-column">
                        <router-link class="menu-item" :to="{ name: 'settings' }">
                            <i class="fa fa-user-circle mx-1 text-info"></i>
                            Profile Settings
                        </router-link>
                        <a class="menu-item" href="javascript:void(0)" @click="signOut">
                            <i class="fa fa-sign-out mx-1 text-info"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </ul>
        <alert id="topbar_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
    </nav>
</template>

<script>
import Alert from './alert.vue'

export default {
    components: {
        Alert
    },

    data() {
        return {
            location: location.pathname,
            dropnavShown: false,
            alert: {
                show: false,
                title: 'Notice',
                message: '',
                confirm: false,
                okText: 'Ok',
                cancelText: 'Cancel',
                okClicked: () => {this.$bvModal.hide('topbar_alert')},
                cancelClicked: () => {this.$bvModal.hide('topbar_alert')},
            }
        }
    },

    methods: {
        getPath: function() {
            var path = location.pathname;

            path = path.substring(path.indexOf("/") + 1);
            path = path.substring(path.indexOf("/"));

            this.location = location.host == "localhost" ? path : location.pathname;

            document.title = 'Vida Emirates Hotel | ' + this.ucwords(this.$route.name)
        },

        signOut() {
            this.alert.confirm = true
            this.alert.message = 'Sign Out Account?'

            this.alert.okClicked = () => {
                axios.post('/api/accounts/signout').then((resp) => {
                        return resp.data
                    }).catch((err) => {
                        console.log(err)
                    })

                this.$emit('signedout')

                this.$bvModal.hide('topbar_alert');
            }

            this.alert.cancelClicked = () => {
                this.$bvModal.hide('topbar_alert');
            }

            this.$bvModal.show('topbar_alert');
        },

        ucwords(str) {
            return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }
    },

    props: {
        isLoggedIn: {
            type: Boolean,
            default: false,
            required: true
        },
        logoUrl: {
            type: String,
            default: null,
            required: true
        },
        userFullname: {
            type: String,
            default: 'N/A',
            required: true
        }
    },

    watch: {
        $route: function() {
            window.scrollTo(0, 0)
            this.getPath()
        }
    }
}
</script>

<style scoped>
    .navbar {
        height: 83px;
        padding: 20px;
    }

    .navbar-logo {
        width: 180px;
    }

    .nav-item {
        margin: 0 15px;
        margin-top: 20px;
    }

    .nav-item.active, .nav-item:hover {
        color: rgb(102, 99, 99) !important;
        text-decoration: none;
    }

    #profilemenu {
        width: 200px;
    }

    #profilemenu .menu-item {
        color: gray;
        padding: 6px 3px;
        text-decoration: none;
    }
</style>
