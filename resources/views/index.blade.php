<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- <link href="http://fonts.cdnfonts.com/css/segoe-ui-4" rel="stylesheet"> --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.css">
        <link rel="stylesheet" href="https://unpkg.com/bootstrap-vue@2.21.2/dist/bootstrap-vue.css">
        <link rel="stylesheet" href="{{ asset('assets/app/css/app.css') }}">
        <link rel="stylesheet" href="//unpkg.com/leaflet/dist/leaflet.css" />

        <script src="https://use.fontawesome.com/1204a62f15.js"></script>
        <title>Vida Emirate Hills Hotel</title>
    </head>
    <body class="antialiased">
        <div id="app">
            <topbar logo-url="{{ asset('storage/media/logo-vida.svg') }}" v-if="isLoggedIn" :is-logged-in="isLoggedIn" :user-fullname="user_fullname" @signedout="signedOut" @togglesidebar="toggleSidebar"></topbar>
            <div class="d-flex flex-row content">
                <sidebar class="vh-100" :username="username" v-if="sidebar_visible && isLoggedIn"></sidebar>
                <router-view :class="'flex-fill' + (sidebar_visible && isLoggedIn ? ' sidebar_shown' : '')" @signedin="signedIn" @profilechanged="profileChanged"></router-view>
            </div>
            <alert id="main_alert" :visible="alert.show" :title="alert.title" :confirm="alert.confirm" :message="alert.message" :ok-text="alert.okText" :cancel-text="alert.cancelText" :ok-clicked="alert.okClicked" :cancel-clicked="alert.cancelClicked" ></alert>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <script src="https://unpkg.com/vue-router@3.5.2/dist/vue-router.js"></script>
        <script src="https://unpkg.com/bootstrap-vue@2.21.2/dist/bootstrap-vue.js"></script>
        <script src="//unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="//unpkg.com/vue2-leaflet"></script>
        <script type="text/javascript" src="{{ asset('assets/app/js/vendor.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/app/js/app.js') }}"></script>
    </body>
</html>
