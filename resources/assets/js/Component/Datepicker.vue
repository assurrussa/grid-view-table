<template>
    <div class="AmiDatePicker">
        <v-datepicker @selected="onSelected"
                      @opened="onOpened"
                      @closed="onClosed"
                      @cleared="onCleared"
                      :name="name"
                      :placeholder="placeholder"
                      v-model="model"
                      :format="customFormatter"
                      :inline="false"
                      :inputClass="inputClass"
                      :clearButton="true"
                      :mondayFirst="true"
                      :disabledDates="disabledDates"
                      :language="language"
        ></v-datepicker>
    </div>
</template>

<script>
    import VDatepicker from 'vuejs-datepicker';
    import moment from 'moment';
    import {en, ru} from 'vuejs-datepicker/dist/locale';
    export default {
        props: {
            name: {
                type: String,
                default: '',
            },
            inputClass: {
                type: String,
                default: 'form-control',
            },
            value: {
                type: String,
                default: '',
            },
            placeholder: {
                type: String,
                default: '-- date --',
            },
            gridId: {
                type: String,
                default: '',
            },
            format: {
                type: String,
                default: 'DD-MM-YYYY',
            },
            i18n: {
                type: String,
                default: 'en',
            },
        },
        components: {
            VDatepicker
        },
        data() {
            return {
                model: null,
                en: en,
                ru: ru,
                disabledDates: {
                    customPredictor: function (date) {
                        // return true;
                    },
                },
            }
        },
        created() {
            this.setLanguage();
            this.model = this.value;
        },
        methods: {
            customFormatter(date) {
                return moment(date).format(this.format);
            },
            onSelected(event) {
                this.model = event.toISOString();
                this.$emit('selected', event);
                this.onSend();
            },
            onOpened(event) {
                this.$emit('opened', event);
            },
            onClosed(event) {
                this.$emit('closed', event);
            },
            onCleared(event) {
                this.model = '';
                this.$emit('cleared', event);
                this.onSend();
            },
            onSend() {
                setTimeout(() => {
                    amiGridOnSend(this.gridId);
                }, 20);
            },
            setLanguage() {
                let lang = this.i18n.toLocaleLowerCase();
                if(this[lang]) {
                    this.language = this[lang];
                }
            },
        }
    }
</script>

<style lang="scss">
    .AmiDatePicker {
        input {
            width: 80%;
            display: inline-block;
        }
        .vdp-datepicker__clear-button {
            display: inline-block;
            width: 15%;
            position: relative;
            span {
                position: absolute;
                top: -16px;
                left: 0;
                font-weight: 700;
                font-size: 32px;
                color: #ca0000;
            }
        }
    }
</style>