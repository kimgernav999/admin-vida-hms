<template>
    <b-modal v-model="show" id="alert" size="sm" :title="title" centered hide-header-close no-close-on-backdrop hide-header>
        <b-container fluid>
            <div class="text-center mt-2 alert-header w-100">
                {{ title.toUpperCase() }}
            </div>
            <div class="text-center pt-4 pb-2 alert-message">
                {{ message }}
            </div>
        </b-container>
        <template #modal-footer>
            <div class="d-flex justify-content-center w-100">
                <b-button pill variant="success" size="sm" class="w-50 mx-2" @click="okClicked()">{{ okText.toUpperCase() }}</b-button>
                <b-button pill variant="warning" size="sm" class="w-50 mx-2" v-if="confirm" @click="cancelClicked()">{{ cancelText.toUpperCase() }}</b-button>
            </div>
        </template>
    </b-modal>
</template>

<script>
    export default {
        data(){
            return {

            }
        },

        props: {
            visible: Boolean,
            title: {
                type: String,
                default: 'Notice'
            },
            message: String,
            confirm: Boolean,
            okText: {
                type: String,
                default: 'Ok'
            },
            cancelText: {
                type: String,
                default: 'Cancel'
            },
            okClicked: {
                type: Function,
                required: false,
                default: () => { return }
            },
            cancelClicked: {
                type: Function,
                required: false,
                default: () => { return }
            }
        },

        computed: {
            show: {
                get: function() {
                    return this.visible
                },
                set: function(value) {
                    this.$emit('visible', value)
                }
            }
        }
    }
</script>

<style scoped>
    .alert-header {
        display: block;
        font-size: 24px;
        font-weight: bold;
        color:darkslategray;
    }

    .alert-message {
        display: block;
        font-size: 16px;
        color:rgb(20, 20, 20);
    }
</style>
