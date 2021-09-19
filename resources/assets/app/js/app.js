import Topbar from './Vue/topbar.vue'
import PageNotFound from './Vue/pagenotfound.vue'

import Dashboard from './Vue/dashboard.vue'
import Schedules from './Vue/schedules.vue'
import Billings from './Vue/billings.vue'
import Inventory from './Vue/inventory.vue'
import Rooms from './Vue/rooms.vue'
import Amenities from './Vue/amenities.vue'
import Offers from './Vue/offers.vue'
import Settings from './Vue/settings.vue'
import Employees from './Vue/employees.vue'
import Guests from './Vue/guests.vue'
import Signin from './Vue/signin.vue'
import Signup from './Vue/signup.vue'
import Sidebar from './Vue/sidebar.vue'
import Alert from './Vue/alert.vue'

// import Newsletter from './Vue/newsletter.vue'
// import Alert from './Vue/alert.vue'

const router = new VueRouter({
    base: '/',
    mode: 'history',
    routes: [
        { path: '/', name: 'signin', component: Signin },
        { path: '/signup', name: 'signup', component: Signup },
        { path: '/dashboard', name: 'dashboard', component: Dashboard },
        { path: '/schedules', name: 'schedules', component: Schedules },
        { path: '/billings', name: 'billings', component: Billings },
        { path: '/inventory', name: 'inventory', component: Inventory },
        { path: '/amenities', name: 'amenities', component: Amenities },
        { path: '/offers', name: 'offers', component: Offers },
        { path: '/rooms', name: 'rooms', component: Rooms },
        { path: '/employees', name: 'employees', component: Employees },
        { path: '/guests', name: 'guests', component: Guests },
        { path: '/settings', name: 'settings', component: Settings },
        { path: '*', name: 'pagenotfound', component: PageNotFound }
    ]
  })

const app = new Vue({
    el: "#app",

    components: {
        Topbar, Sidebar, Alert
    },

    data() {
        return {
            sidebar_visible: true,
            username: 'N/A',
            user_fullname: 'N/A',
            checkSession: null,
            checkSessionInterval: 1800000,
            isLoggedIn: false,
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
        toggleSidebar() {
            this.sidebar_visible = !this.sidebar_visible
        },

        profileChanged() {
            this.getCurrentUser()
        },

        signedIn() {
            this.getCurrentUser()

            this.startCheckSession(true)

            this.isLoggedIn = true
        },

        signedOut() {
            this.getCurrentUser()

            this.startCheckSession(false)

            this.isLoggedIn = false
        },

        startCheckSession(isChecking) {
            if(isChecking) {
                this.checkSession = setInterval(() => {
                    this.getCurrentUser()

                    if(!this.isLoggedIn){
                        alert('Your Session has expired, please Sign In again!')
                        this.$router.push('/')
                        clearInterval(this.checkSession)
                    }
                }, this.checkSessionInterval)
            }
            else {
                clearInterval(this.checkSession)
            }
        },

        fixDropdownUnshow() {
            var dropMenu = document.querySelector('.dropdown-menu.show')
            if(dropMenu) dropMenu.classList.remove('show')
        },

        async getCurrentUser() {
            var cred_route = [
                'signin'
            ]

            var curruser_reponse = await axios.post('/api/accounts/current')
                .then((resp) => {
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            if(curruser_reponse.username) {
                if(this.$moment(curruser_reponse.expires_at).diff(this.$moment(), 'minutes', true) > 0) {
                    var user_info = curruser_reponse.current.admin

                    this.username = curruser_reponse.current.username
                    this.user_fullname = user_info.first_name + ' ' + (user_info.middle_name ? user_info.middle_name[0] + '. ' : '') + user_info.last_name
                    this.isLoggedIn = true

                    if(cred_route.includes(this.$route.name))
                        this.$router.push('/dashboard')
                }
                else {
                    axios.post('/api/accounts/signout').then((resp) => {
                            return resp.data
                        }).catch((err) => {
                            console.log(err)
                        })

                    if(!cred_route.includes(this.$route.name))
                        this.$router.push('/')

                    this.$emit('signedout')
                }
            }
            else {
                if(!cred_route.includes(this.$route.name))
                    this.$router.push('/')

                this.$emit('signedout')
            }
        },
    },

    mounted() {
        this.getCurrentUser()

        if(this.isLoggedIn)
            this.startCheckSession(true)

        document.addEventListener("click", this.fixDropdownUnshow);
    },

    watch: {
        $route: function () {
            this.getCurrentUser()
        }
    },

    router: router
});
