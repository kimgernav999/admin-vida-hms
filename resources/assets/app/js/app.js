import Topbar from './Vue/topbar.vue'
import Footr from './Vue/footer.vue'
import PageNotFound from './Vue/pagenotfound.vue'

import Home from './Vue/home.vue'
import Amenities from './Vue/amenities.vue'
import Rooms from './Vue/rooms.vue'
import Vtours from './Vue/vtours.vue'
import Offers from './Vue/offers.vue'
import About from './Vue/about.vue'
import Faqs from './Vue/faqs.vue'
import Termsconditions from './Vue/termsconditions.vue'
import Privacypolicy from './Vue/privacypolicy.vue'
import Contactus from './Vue/contactus.vue'
import Booking from './Vue/booking.vue'
import Settings from './Vue/settings.vue'
import Reservations from './Vue/reservations.vue'
import Signin from './Vue/signin.vue'
import Signup from './Vue/signup.vue'
import Alert from './Vue/alert.vue'

// import Newsletter from './Vue/newsletter.vue'
// import Alert from './Vue/alert.vue'

const router = new VueRouter({
    base: '/',
    mode: 'history',
    routes: [
        { path: '/', name: 'home', component: Home },
        { path: '/signin', name: 'signin', component: Signin },
        { path: '/signup', name: 'signup', component: Signup },
        { path: '/amenities', name: 'amenities', component: Amenities },
        { path: '/rooms', name: 'rooms', component: Rooms },
        { path: '/vtours', name: 'vtours', component: Vtours },
        { path: '/offers', name: 'offers', component: Offers },
        { path: '/about', name: 'about', component: About },
        { path: '/faqs', name: 'faqs', component: Faqs },
        { path: '/termsconditions', name: 'termsconditions', component: Termsconditions },
        { path: '/privacypolicy', name: 'privacypolicy', component: Privacypolicy },
        { path: '/contactus', name: 'contactus', component: Contactus },
        { path: '/booking', name: 'booking', component: Booking },
        { path: '/settings', name: 'settings', component: Settings },
        { path: '/reservations', name: 'reservations', component: Reservations },
        { path: '*', name: 'pagenotfound', component: PageNotFound }
    ]
  })

const app = new Vue({
    el: "#app",

    components: {
        Topbar, Footr, Alert
    },

    data() {
        return {
            user_fullname: 'N/A',
            checkSession: null,
            checkSessionInterval: 5000,
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
        profileChanged() {
            this.getCurrentUser()
        },

        signedIn() {
            this.getCurrentUser()

            // this.startCheckSession(true)

            this.isLoggedIn = true
            this.$router.push('/settings')
        },

        signedOut() {
            this.getCurrentUser()

            // this.startCheckSession(false)

            this.isLoggedIn = false
            this.$router.push('/signin')
        },

        startCheckSession(isChecking) {
            if(isChecking) {
                this.checkSession = setInterval(() => {
                    this.isLoggedIn = false

                    if(!this.isLoggedIn){
                        alert('Your Session has expired, please Sign In again!')
                        this.$router.push('signin')
                        clearInterval(this.checkSession)
                    }
                }, this.checkSessionInterval)
            }
            else {
                clearInterval(this.checkSession)
            }
        },

        fixDropdownUnshow() {
            var dropMenu = document.querySelector('.show')
            if(dropMenu) dropMenu.classList.remove('show')
        },

        async getCurrentUser() {
            var curruser_reponse = await axios.post('/api/accounts/current')
                .then((resp) => {
                    return resp.data
                }).catch((err) => {
                    console.log(err)
                })

            if(curruser_reponse.username) {
                if(this.$moment(curruser_reponse.expires_at).diff(this.$moment(), 'minutes', true) > 0) {
                    var user_info = curruser_reponse.current.user

                    this.user_fullname = user_info.first_name + ' ' + (user_info.middle_name ? user_info.middle_name[0] + '. ' : '') + user_info.last_name
                    this.isLoggedIn = true
                }
                else {
                    this.alert.confirm = false
                    this.alert.message = 'Your Session has expired, please Sign In again!'

                    this.alert.okClicked = () => {
                        axios.post('/api/accounts/signout').then((resp) => {
                                return resp.data
                            }).catch((err) => {
                                console.log(err)
                            })

                        this.$emit('signedout')
                        this.isLoggedIn = false
                        this.alert.show = false;
                    }

                    this.alert.show = true;
                }
            }
            else {
                this.$emit('signedout')
                this.isLoggedIn = false
            }

            this.route_allowed()
        },

        route_allowed() {
            var restricted_route = [
                'settings',
                'reservations'
            ]

            var cred_route = [
                'signin',
                'signup'
            ]

            if(!this.isLoggedIn) {
                if(restricted_route.includes(this.$route.name)) {
                    this.alert.confirm = false
                    this.alert.message = 'Your Session has expired, please Sign In again!'

                    this.alert.okClicked = () => {
                        this.alert.show = false;
                        this.$router.push('/signin')
                    }

                    this.alert.show = true;
                }
            }
            else {
                if(cred_route.includes(this.$route.name)) {
                    this.$router.push('/')
                }
            }
        }
    },

    mounted() {
        this.getCurrentUser()

        if(this.$route.path == '/signin' && this.isLoggedIn){
            this.$router.push('/')
        }
        // this.startCheckSession(true)

        document.addEventListener("click", this.fixDropdownUnshow);
    },

    watch: {
        $route: function () {
            this.route_allowed()
        }
    },

    router: router
});
