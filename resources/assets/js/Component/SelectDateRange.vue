<template>
    <div class="SelectDateRange">
        <input type="hidden" :name="name" v-model="model">
        <ami-date-range @selected="onDateSelected"
                        :clear="clear"
                        :createDate="createDate"
                        :start="from"
                        :end="to"
                        :compact="compact"
                        :format="format"
                        :placeholder="placeholder"
                        :righttoleft="righttoleft"
                        :width="width"
                        :i18n="i18n"></ami-date-range>
    </div>
</template>
<script>
    /**
     * example:
     **/
    export default {
        props: {
            clear: {
                type: String,
                default: '1',
            },
            name: {
                type: String,
                default: '',
            },
            value: {
                type: String,
                default: '',
            },
            createDate: {
                type: String,
                default: '1',
            },
            placeholder: {
                type: String,
                default: ' -- select date range -- '
            },
            gridId: {
                type: String,
                default: '',
            },
            start: {
                type: String,
                default: null,
            },
            end: {
                type: String,
                default: null,
            },
            compact: {
                type: String,
                default: '',
            },
            righttoleft: {
                type: String,
                default: '',
            },
            i18n: {
                type: String,
                default: 'EN',
            },
            format: {
                type: String,
                default: 'DD MMM YYYY'
            },
            width: {
                type: String,
                default: '200px'
            },
        },
        data() {
            return {
                model: null,
                from: null,
                to: null,
                selectedDate: {
                    start: null,
                    end: null
                },
            }
        },
        created() {
            let dateRange = this.value && this.value.length > 8 ? this.value.split('_') : null;
            if (dateRange) {
                this.from = dateRange[0].split('.').reverse().join('.'); // from DD.MM.YYYY to YYYY.MM.DD
                this.to = dateRange[1].split('.').reverse().join('.'); // from DD.MM.YYYY to YYYY.MM.DD
            }
            if (!this.from) {
                this.from = this.start;
            }
            if (!this.to) {
                this.to = this.end;
            }
        },
        mounted() {
        },
        methods: {
            onDateSelected(daterange) {
                let vm = this;
                if (daterange.start) {
                    daterange.start = new Date(daterange.start.getFullYear(), daterange.start.getMonth(), daterange.start.getDate(), 0, 0);
                    daterange.end = new Date(daterange.end.getFullYear(), daterange.end.getMonth(), daterange.end.getDate(), 23, 59);
                    vm.selectedDate = daterange;
                    this.model = daterange.start.toLocaleDateString() + '_' + daterange.end.toLocaleDateString();
                } else {
                    vm.selectedDate = daterange;
                    this.model = '';
                }
                setTimeout(() => {
                    amiGridOnSend(vm.gridId)
                        .then((response) => {
                            //
                        })
                        .catch((error) => {
                            console.log(error)
                        });
                }, 20);
            },
        },
    }
</script>

<style>
    .SelectDateRange {
        display: inline-block;
        background-color: #fff;
    }
</style>