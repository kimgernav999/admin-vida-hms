<template>
    <nav class="navbar navbar-expand-xl navbar-light bg-light fixed-top p-0 shadow-sm">
        <a class="d-flex navbar-brand ml-3 p-0 h-100">
            <img :src="logoUrl" class="align-self-center navbar-logo" alt="">
        </a>

        <ul class="navbar-nav ml-auto mx-4">
            <div :class="'nav-item position-relative m-0 d-flex d-xl-none' + ((location == '/settings' || location == '/reservations') ? ' active' : '')" v-if="isLoggedIn" data-target="#profilemenu" data-toggle="collapse">
                <a class="nav-link font-weight-bold text-dark" href="javascript:void(0)">
                    {{ userFullname }}
                </a>
                <div id="profilemenu" class="bg-white border-light dropdown-menu dropdown-menu-right mt-2 p-2 position-absolute shadow">
                    <div class="d-flex flex-column">
                        <router-link class="menu-item" :to="{ name: 'settings' }">
                            <i class="fa fa-user-circle mx-1 text-info"></i>
                            Profile Settings
                        </router-link>
                        <router-link class="menu-item" :to="{ name: 'reservations' }">
                            <i class="fa fa-calendar mx-1 text-info"></i>
                            Reservations
                        </router-link>
                        <a class="menu-item" href="javascript:void(0)" @click="signOut">
                            <i class="fa fa-sign-out mx-1 text-info"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
            <router-link class="nav-item m-0 d-flex d-xl-none" :to="{ name: 'signin' }" v-else>
                <a class="nav-link">Sign In</a>
            </router-link>
        </ul>
        <div class="border-0 navbar-toggler rounded-circle mr-3 " data-target="#collapsemenu" data-toggle="collapse">
            <i class="fa fa-bars text-secondary" v-if="!dropnavShown"></i>
            <span v-else>&times;</span>
        </div>
        <div id="collapsemenu" class="bg-light collapse navbar-collapse">
            <ul class="navbar-nav ml-auto px-lg-0 px-3">
                <router-link :class="'nav-item mx-4 mx-xl-3' + (location == '/' ? ' active' : '')" :to="{ name: 'home' }">
                    <a class="nav-link">Home</a>
                </router-link>
                <router-link :class="'nav-item mx-4 mx-xl-3' + (location == '/amenities' ? ' active' : '')" :to="{ name: 'amenities' }">
                    <a class="nav-link">Amenities</a>
                </router-link>
                <router-link :class="'nav-item mx-4 mx-xl-3' + (location == '/rooms' ? ' active' : '')" :to="{ name: 'rooms' }">
                    <a class="nav-link">Rooms & Suites</a>
                </router-link>
                <router-link :class="'nav-item mx-4 mx-xl-3' + (location == '/vtours' ? ' active' : '')" :to="{ name: 'vtours' }">
                    <a class="nav-link">Virtual Tours</a>
                </router-link>
                <router-link :class="'nav-item mx-4 mx-xl-3' + (location == '/offers' ? ' active' : '')" :to="{ name: 'offers' }">
                    <a class="nav-link">Offers</a>
                </router-link>
                <router-link :class="'nav-item mx-4 mx-xl-3' + (location == '/booking' ? ' active' : '')" :to="{ name: 'booking' }">
                    <a class="nav-link">Manage Booking</a>
                </router-link>
                <li :class="'nav-item position-relative mx-4 mx-xl-3 d-none d-xl-flex' + ((location == '/settings' || location == '/reservations') ? ' active' : '')" v-if="isLoggedIn" data-target="#profilemenu" data-toggle="collapse">
                    <a class="nav-link font-weight-bold text-dark" href="javascript:void(0)">
                        {{ userFullname }}
                    </a>
                    <div id="profilemenu" class="bg-white border-light dropdown-menu dropdown-menu-right mt-2 p-2 position-absolute shadow">
                        <div class="d-flex flex-column">
                            <router-link class="menu-item" :to="{ name: 'settings' }">
                                <i class="fa fa-user-circle mx-1 text-info"></i>
                                Profile Settings
                            </router-link>
                            <router-link class="menu-item" :to="{ name: 'reservations' }">
                                <i class="fa fa-calendar mx-1 text-info"></i>
                                Reservations
                            </router-link>
                            <a class="menu-item" href="javascript:void(0)" @click="signOut">
                                <i class="fa fa-sign-out mx-1 text-info"></i>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </li>
                <router-link class="nav-item mx-4 mx-xl-3 d-none d-xl-flex" :to="{ name: 'signin' }" v-else>
                    <a class="nav-link">Sign In</a>
                </router-link>
            </ul>
        </div>
        <alert :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :okText="alert.okText" :cancelText="alert.cancelText" :okClicked="alert.okClicked" :cancelClicked="alert.cancelClicked"></alert>
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
                okClicked: () => {this.alert.show = false},
                cancelClicked: () => {this.alert.show = false},
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

                this.alert.show = false;
            }

            this.alert.cancelClicked = () => {
                this.alert.show = false;
            }

            this.alert.show = true;
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
