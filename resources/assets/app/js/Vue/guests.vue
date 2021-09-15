<template>
    <div class="d-flex justify-content-center px-5">
        <div class="align-self-center bg-white border shadow-sm rounded-sm p-5 w-100">
            <h3 class="mb-4">Guests</h3>
            <div class="border py-3 rounded-sm">
                <div class="d-flex mb-3">
                    <div class="col">
                        <b-button variant="success" class="rounded-circle" size="sm" title="New Guest" v-b-tooltip.hover.noninteractive>
                            <i class="fa fa-plus"></i>
                        </b-button>
                        <b-button variant="primary" class="rounded-circle" size="sm" title="Refresh" v-b-tooltip.hover.noninteractive @click="getAllGuests">
                            <i class="fa fa-refresh"></i>
                        </b-button>
                    </div>
                    <div class="col">
                        <b-input v-model="table.filter" class="form-control rounded-pill" size="sm" placeholder="Search Guest"></b-input>
                    </div>
                </div>
                <div class="d-flex flex-column">
                    <b-table class="border-bottom vw-75" :busy="table.isBusy" :fields="table.fields" :filter="table.filter" :filter-included-fields="table.filter_on" :current-page="table.current_page" :items="table.items" striped responsive show-empty>
                        <template #cell(full_name)="row">
                            {{ row.item.first_name + ' ' + (row.item.middle_name ? row.item.middle_name[0] + '. ' : '') + row.item.last_name }}
                        </template>
                        <template #cell(email_address)="row">
                            {{ row.item.email_address }}
                        </template>
                        <template #cell(paypal_linked)="row">
                            {{ row.item.paypal_id ? 'Yes' : 'No' }}
                        </template>
                        <template #cell(actions)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="success" class="rounded-circle mx-1" size="sm" title="Update Record" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-edit"></i>
                                </b-button>
                                <b-button variant="primary" class="rounded-circle mx-1" size="sm" title="Delete Record" v-b-tooltip.hover.noninteractive>
                                    <i class="fa fa-trash"></i>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <b-pagination class="mx-auto my-0" v-model="table .current_page" :total-rows="table.items.length" :per-page="table.per_page"></b-pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            all_accounts: '',
            table: {
                isBusy: false,
                current_page: 1,
                filter: '',
                filter_on: ['full_name', 'email_address'],
                fields: [
                    {
                        key: 'full_name',
                        label: 'Full Name',
                        sortable: true
                    },
                    {
                        key: 'email_address',
                        label: 'Email Address',
                        sortable: true
                    },
                    {
                        key: 'paypal_linked',
                        label: 'Paypal Linked',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: 'Actions',
                        sortable: false,
                    }
                ],
                items: [],
                per_page: 6
            }
        }
    },

    methods: {
        async getAllGuests() {
            this.table.isBusy = true

            this.all_accounts = await axios.post('/api/accounts/allAccounts', {
                    user_role: 'guest'
                })
                .then((resp) => {
                    this.table.isBusy = false
                    return resp.data
                })
                .catch((err) => {
                    console.log(err)
                })

            this.table.items = []

            this.all_accounts.forEach((item) => {
                this.table.items.push(item.user)
            })
        }
    },

    mounted() {
        this.getAllGuests()
    }
}
</script>

<style scoped>

</style>
