<template>
    <nav class="border-top navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <div class="row w-100">
                <ul class="col navbar-nav justify-content-between w-100 my-2">
                    <router-link class="nav-item" :to="{ name: 'home' }">
                        <div class="col-auto navbar-brand m-0 h-100 text-center">
                            <img :src="logoUrl" class="navbar-logo" alt="">
                        </div>
                    </router-link>
                    <router-link :class="'nav-item' + (location == '/about' ? ' active' : '')" :to="{ name: 'about' }">
                        <a class="nav-link">ABOUT US</a>
                    </router-link>
                    <router-link :class="'nav-item' + (location == '/faqs' ? ' active' : '')" :to="{ name: 'faqs' }">
                        <a class="nav-link">FAQS</a>
                    </router-link>
                    <router-link :class="'nav-item' + (location == '/termsconditions' ? ' active' : '')" :to="{ name: 'termsconditions' }">
                        <a class="nav-link">TERMS & CONDITIONS</a>
                    </router-link>
                    <router-link :class="'nav-item' + (location == '/privacypolicy' ? ' active' : '')" :to="{ name: 'privacypolicy' }">
                        <a class="nav-link">PRIVACY POLICY</a>
                    </router-link>
                    <router-link :class="'nav-item' + (location == '/contactus' ? ' active' : '')" :to="{ name: 'contactus' }">
                        <a class="nav-link">CONTACT US</a>
                    </router-link>
                    <li class="nav-item d-flex justify-content-around">
                        <div class="btn-social">
                            <b-button class="rounded-circle" size="sm" variant="transparent" target="_blank" href="https://www.facebook.com/vidahotelsandresorts" title="Like us on Facebook" v-b-tooltip.noninteractive>
                                <i class="fa fa-facebook-official"></i>
                            </b-button>
                            <b-button class="rounded-circle" size="sm" variant="transparent" target="_blank" href="https://www.instagram.com/vidahotels/" title="Follow us on Instagram" v-b-tooltip.noninteractive>
                                <i class="fa fa-instagram"></i>
                            </b-button>
                            <b-button class="rounded-circle" size="sm" variant="transparent" target="_blank" href="https://twitter.com/vidadowntown?lang=en" title="Follow us on Twitter" v-b-tooltip.noninteractive>
                                <i class="fa fa-twitter"></i>
                            </b-button>
                            <b-button class="rounded-circle" size="sm" variant="transparent" target="_blank" href="https://www.youtube.com/user/vidahotels" title="Subscribe on Youtube" v-b-tooltip.noninteractive>
                                <i class="fa fa-youtube-play"></i>
                            </b-button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    data() {
        return {
            location: location.pathname
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
            this.$emit('signedout')
        },

        ucwords(str) {
            return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }
    },

    props: ['logoUrl'],

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
        padding: 20px;
        height: auto;
    }

    .navbar-logo {
        width: 120px;
    }

    .nav-item {
        text-align: center;
    }

    .nav-item.active, .nav-item:hover {
        color: rgb(102, 99, 99) !important;
        text-decoration: none;
    }

    .nav-link {
        margin: 10px auto;
    }

    .btn-social {
        margin: 13px auto;
    }
</style>
