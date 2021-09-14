<template>
    <div class="d-flex flex-column">
        <div class="d-flex align-items-end p-5 img-header">
            <h1 class="mb-4 text-white">Contact Us</h1>
        </div>
        <div class="d-flex justify-content-center flex-fill">
            <div class="align-self-center py-5">
                <div class="mx-auto w-75">
                    <h5 class="mb-3">We would like to hear from you! Here are a few ways to get in touch with us.</h5>
                    <h3 class="my-4">Hotel Reservation</h3>
                    <p>For room reservations at Vida Downtown, Manzil Downtown, Vida Emirates Hills and Vida Creek Harbour please book online&nbsp;<a href="http://www.vidahotels.com/en/">www.vidahotels.com/en/</a>&nbsp;or email&nbsp;<a href="mailto:stay@vidahotels.com" onmousedown="dataLayer.push({'event': 'eventTracker', 'eventCat': 'Email Clicks', 'eventAct':'Click', 'eventLbl': 'stay@vidahotels.com', 'eventVal': 0, 'nonInteraction': 0});">stay@vidahotels.com</a>.<br>You may also contact us on any of the toll free or international direct dial numbers below:      </p>
                    <div class="separator separator-lightgray mb-4"></div>
                    <h3 class="my-4">United Arab Emirates</h3>
                    <div class="bg-white p-3 rounded-sm my-4 w-100">+97144238899</div>
                    <div class="separator separator-lightgray mb-4"></div>
                    <h3 class="my-4">Toll Free Numbers</h3>
                    <div class="d-flex mb-4">
                        <div class="bg-white col p-4 rounded-sm w-100">
                            <h6 class="font-weight-bold">Asia & Australia</h6>
                            <h6>India: 0008009713005</h6>
                            <h6>Japan: 00531930005</h6>
                            <h6>Australia: 1800791513</h6>
                            <h6>Singapore: 8009710004</h6>
                            <h6>China: 4008424100</h6>
                        </div>
                        <div class="separator-side"></div>
                        <div class="bg-white col p-4 rounded-sm w-100">
                            <h6 class="font-weight-bold">Europe</h6>
                            <h6>United Kingdom: 08082344454</h6>
                            <h6>Germany: 08001834048</h6>
                            <h6>Russia: 81080020011971</h6>
                            <h6>France: 800903805</h6>
                            <h6>Italy: 800879729</h6>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="bg-white col p-4 rounded-sm w-100">
                            <h6 class="font-weight-bold">Middle East & Africa</h6>
                            <h6>Kingdom of Saudi Arabia: 8008971470</h6>
                            <h6>South Africa: 0800991720</h6>
                        </div>
                        <div class="separator-side"></div>
                        <div class="bg-white col p-4 rounded-sm w-100">
                            <h6 class="font-weight-bold">United States of America</h6>
                            <h6>18443980614</h6>
                        </div>
                    </div>
                    <div class="separator separator-lightgray mb-4"></div>
                    <h3 class="my-4">Development</h3>
                    <div class="bg-white p-3 rounded-sm my-4 w-100">
                        <p>For development queries, please email mkirby@emaar.ae or contact us on the below mailing address:</p>
                        <p>Box: 9440 Dubai, United Arab Emirates</p>
                    </div>
                    <div class="separator separator-lightgray mb-4"></div>
                    <h3 class="my-4">Media</h3>
                    <div class="bg-white p-3 rounded-sm my-4 w-100">
                        <p>For media enquiries, please contact: media@jesmedia.ae</p>
                    </div>
                    <div class="separator separator-lightgray mb-4"></div>
                    <h3 class="my-4">Our Location</h3>
                    <div class="bg-white p-3 rounded-sm my-4 w-100">
                        <div class="border border-gray rounded-sm shadow-sm mapwrapper">
                            <l-map :zoom="map.zoom" :center="map.center">
                                <l-tile-layer :url="map.url" :attribution="map.attribution"></l-tile-layer>
                                <l-marker :lat-lng="map.markerLatLng"></l-marker>
                            </l-map>
                        </div>
                    </div>
                    <div class="separator separator-lightgray mb-4"></div>
                    <h3 class="my-4">Have a question?</h3>
                    <div class="d-flex justify-content-center my-4">
                        <div class="align-self-center signin-wrapper bg-white border shadow-sm rounded-sm p-5">
                            <h6 class="mb-5 text-secondary">Fill out the form below and we will get in touch with you.</h6>
                            <div class="d-flex flex-row">
                                <b-form-group class="mx-1" label="First Name *">
                                    <b-form-input class="form-control rounded-pill"></b-form-input>
                                </b-form-group>
                                <b-form-group class="mx-1" label="Last Name *">
                                    <b-form-input class="form-control rounded-pill"></b-form-input>
                                </b-form-group>
                            </div>
                            <div class="d-flex flex-row">
                                <b-form-group class="mx-1" label="Email Address *">
                                    <b-form-input class="form-control rounded-pill"></b-form-input>
                                </b-form-group>
                                <b-form-group class="mx-1" label="Mobile Number">
                                    <b-form-input class="form-control rounded-pill"></b-form-input>
                                </b-form-group>
                            </div>
                            <div class="d-flex flex-column mb-4">
                                <b-form-group class="mx-1" label="Subject *">
                                    <b-form-input class="form-control rounded-pill"></b-form-input>
                                </b-form-group>
                                <b-form-group class="mx-1" label="Message *">
                                    <div class="border border-gray p-2" style="border-radius: 0.8rem;">
                                        <b-form-textarea class="border-0" rows="4" placeholder="Tell us about your enquiry (Optional)"></b-form-textarea>
                                    </div>
                                </b-form-group>
                                <span class="d-block font-weight-lighter text-center text-black-50 note-text">Please visit our <router-link class="priv-label" :to="{ name: 'privacypolicy' }">Privacy Policy</router-link> to understand how we handle your personal data</span>
                            </div>
                            <div class="d-flex">
                                <b-button class="mx-auto w-50" variant="info" pill>Send</b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    components: {
        'l-map': window.Vue2Leaflet.LMap,
        'l-tile-layer': window.Vue2Leaflet.LTileLayer,
        'l-marker': window.Vue2Leaflet.LMarker,
    },

    data() {
        return {
            map: {
                center: [25.078913170827587, 55.15748866820168],
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                url: 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoia2ltZ2VyYWxkbmF2YSIsImEiOiJja2prdzI4bW4wMDV1MnBuN2lwdHV1NGN4In0.wTE1EulgEQ_jgi4WVFh5Fw',
                markerLatLng: [25.078913170827587, 55.15748866820168],
                zoom: 13
            }
        }
    }
}
</script>

<style scoped>
    .img-header {
        background-image: url('/storage/media/connect-desk.jpg');
        background-repeat: no-repeat;
        background-size: 100% auto;
        height: 320px;
    }

    div > p:last-child {
        margin: 0;
    }

    .separator {
        border-bottom: solid 1px gray;
        height: 1px;
    }

    .separator-lightgray {
        border-bottom: solid 1px lightgray;
    }

    .separator-side {
        border-left: solid 1px lightgray;
        margin: 15px 0;
        width: 1px;
    }

    .mapwrapper {
        height: 420px;
        width: 100%;
    }

    .note-text {
        font-size: 13px;
    }

    .priv-label:hover {
        text-decoration: none;
    }
</style>
