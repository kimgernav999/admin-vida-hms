<template>
    <div class="d-flex flex-column h-100 sidebar-wrapper">
        <div class="sb-items-header">
            Main Management
        </div>
        <router-link :class="'sb-items' + (location == '/dashboard' ? ' active' : '')" :to="{ name: 'dashboard' }">
            <i class="fa fa-dashboard mr-1"></i>
            Dashboard
        </router-link>
        <router-link :class="'sb-items' + (location == '/schedules' ? ' active' : '')" :to="{ name: 'schedules' }">
            <i class="fa fa-calendar mr-1"></i>
            Bookings & Reservations
        </router-link>
        <router-link :class="'sb-items' + (location == '/billings' ? ' active' : '')" :to="{ name: 'billings' }">
            <i class="fa fa-money mr-1"></i>
            Billings
        </router-link>
        <router-link :class="'sb-items' + (location == '/inventory' ? ' active' : '')" :to="{ name: 'inventory' }">
            <i class="fa fa-hotel mr-1"></i>
            Room Inventory
        </router-link>
        <div class="separator"></div>
        <div class="sb-items-header">
            Data Management
        </div>
        <router-link :class="'sb-items' + (location == '/amenities' ? ' active' : '')" :to="{ name: 'amenities' }">
            <i class="fa fa-bullseye mr-1"></i>
            Amenities
        </router-link>
        <router-link :class="'sb-items' + (location == '/offers' ? ' active' : '')" :to="{ name: 'offers' }">
            <i class="fa fa-handshake-o mr-1"></i>
            Promo Offers
        </router-link>
        <router-link :class="'sb-items' + (location == '/rooms' ? ' active' : '')" :to="{ name: 'rooms' }">
            <i class="fa fa-building-o mr-1"></i>
            Rooms & Suites
        </router-link>
        <div class="separator"></div>
        <div class="sb-items-header">
            Accounts Management
        </div>
        <router-link :class="'sb-items' + (location == '/employees' ? ' active' : '')" :to="{ name: 'employees' }" v-if="username == 'superadmin'">
            <i class="fa fa-cogs mr-1"></i>
            Employees
        </router-link>
        <router-link :class="'sb-items' + (location == '/guests' ? ' active' : '')" :to="{ name: 'guests' }">
            <i class="fa fa-users mr-1"></i>
            Guests
        </router-link>
    </div>
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

            document.title = 'Vida Emirates Hills Hotel | ' + this.ucwords(this.$route.name)
        },

        ucwords(str) {
            return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }
    },

    watch: {
        $route: function() {
            window.scrollTo(0, 0)
            this.getPath()
        }
    },

    props: ['username']
}
</script>

<style>
    .sidebar-wrapper {
        background-color: rgb(9, 22, 145);
        padding: 20px;
        position: fixed;
        width: 258px;
        z-index: 500;
    }

    .sb-items-header {
        color: whitesmoke;
        font-size: 15px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .sb-items {
        color: lightblue;
        font-size: 14px;
        padding: 10px 0;
    }

    .sb-items > i {
        color: rgb(171, 247, 194);
        text-align: center;
        width: 30px;
    }

    .sb-items.active, .sb-items:hover {
        color: rgb(220, 235, 240);
        text-decoration: none;
        font-weight: bold;
    }

    .sb-items.active > i, .sb-items:hover > i {
        color: rgb(114, 247, 154);
    }

    .separator {
        margin: 15px 0;
        border-bottom: solid 1px whitesmoke;
        height: 2px;
    }
</style>
