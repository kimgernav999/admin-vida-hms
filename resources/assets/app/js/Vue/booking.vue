<template>
    <div class="d-flex justify-content-center m-2">
        <div class="align-self-center booking-wrapper bg-white border shadow-sm rounded-sm p-5">
            <div class="mb-5">
                <h2 class="font-weight-bold text-center">View Booking</h2>
                <h6 class="font-weight-lighter text-center text-black-50">Complete the form below to review, modify or cancel your booking.</h6>
            </div>
            <b-form class="my-3">
                <b-form-group label="Email Address">
                    <b-form-input class="form-control rounded-pill"></b-form-input>
                </b-form-group>
                <b-form-group label="Last Name">
                    <b-form-input class="form-control rounded-pill"></b-form-input>
                </b-form-group>
                <b-form-group class="mb-4" label="Confirmation Code">
                    <b-form-input class="form-control rounded-pill"></b-form-input>
                </b-form-group>
                <b-form-group>
                    <b-button variant="primary" block pill>Continue</b-button>
                </b-form-group>
            </b-form>
        </div>
  </div>
</template>

<script>
export default {

}
</script>

<style scoped>
    .booking-wrapper {
        width: 420px;
    }
</style>
