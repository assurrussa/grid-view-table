<template>
    <div :class="{ 'multiselect--active': isSelected, 'multiselect--placeholder': !isSelected }">
        <input type="hidden" :name="name" :value="value[fieldSelect]">
        <multiselect v-model="value"
                     :selectLabel="selectLabel"
                     :deselectLabel="deselectLabel"
                     :trackBy="label"
                     :label="label"
                     :placeholder="placeholder"
                     :options="options"
                     :searchable="isSearch"
                     :allow-empty="false"
                     :multiple="isMultiple"
                     :loading="isLoading"
                     :internalSearch="!isSearchAjax"
                     :clearOnSelect="true"
                     :closeOnSelect="true"
                     :optionsLimit="300"
                     :showLabels="true"
                     :limit="3"
                     :limitText="limitText"
                     :maxHeight="600"
                     :showNoResults="true"
                     :hideSelected="false"
                     open-direction="bottom"
                     @search-change="asyncFind"
                     @select="onSelect"
                     @remove="onRemove">
            <template slot="clear" slot-scope="props">
                <div class="multiselect__clear" v-if="isClear && isSelected" @mousedown.prevent.stop="clearAll(props.search)">
                    <span class="fa fa-close"></span>
                </div>
            </template>
            <span slot="noResult">{{ notFound }}</span>
            <template slot="caret" slot-scope="{ toggle }">
                <span @mousedown.prevent.stop="toggle()" v-if="isSearchAjax && !isSelected" class="multiselect-icon fa fa-search"></span>
            </template>
        </multiselect>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        props: {
            name: {
                required: true,
                default: '',
            },
            jsInitSubmit: {
                required: true,
                default: '',
            },
            selectLabel: {
                Type: String,
                default: '',
            },
            deselectLabel: {
                Type: String,
                default: '',
            },
            multiple: {
                Type: String,
                default: '',
            },
            action: {
                default: '',
            },
            fieldSelect: {
                default: 'id',
            },
            notFound: {
                default: 'not found',
            },
            label: {
                Type: String,
                default: 'label',
            },
            method: {
                Type: String,
                default: 'get',
            },
            placeholder: {
                Type: String,
                default: '-- search --',
            },
            selected: {
                default: null,
            },
            minLength: {
                default: 2,
            },
            clear: {
                default: false,
            },
            searchNoAjax: {
                default: true,
            },
            searchAjax: {
                default: false,
            },
            data: {
                Type: String,
                default: null,
            },
            paramsString: {
                Type: String,
                default: '',
            },
        },
        components: {
            Multiselect
        },
        data() {
            return {
                value: [],
                options: [],
                isClear: false,
                isMultiple: false,
                isLoading: false,
                isSearch: true,
                isSearchAjax: false
            }
        },
        created() {
            this.options = this.data ? JSON.parse(this.data) : [];
            this.isClear = this.clear ? true : false;
            this.isSearch = this.searchNoAjax ? true : false;
            this.isSearchAjax = this.searchAjax ? true : false;
            this.isMultiple = !!this.multiple;
            this.value = this.selected ? JSON.parse(this.selected) : [];
        },
        computed: {
            isSelected() {
                if(typeof this.value === 'object') {
                    return this.value && this.value[this.label] && this.value[this.label].length;
                }
                return false;
            },
        },
        methods: {
            onSelect(value) {
                this.$el.querySelector('input[name="' + this.name + '"]').value = value[this.fieldSelect];
                this.$emit('select', value);
                if(this.jsInitSubmit && window[this.jsInitSubmit]) {
                    window[this.jsInitSubmit].onSend();
                }
            },
            onRemove(value) {
                this.$el.querySelector('input[name="' + this.name + '"]').value = '';
                this.$emit('remove', value);
                if(this.jsInitSubmit && window[this.jsInitSubmit]) {
                    window[this.jsInitSubmit].onSend();
                }
            },
            clearAll() {
                this.$el.querySelector('input[name="' + this.name + '"]').value = '';
                this.$emit('clear', this.value);
                this.value = [];
                if(this.jsInitSubmit && window[this.jsInitSubmit]) {
                    window[this.jsInitSubmit].onSend();
                }
            },
            limitText(count) {
                return `and ${count} other values`
            },
            asyncFind(query) {
                if(this.isSearchAjax) {
                    this.isLoading = true;
                    this.search(query, this);
                }
            },
            search(search, vm) {
                clearTimeout(this.timer);
                this.timer = setTimeout(() => {
                    if(search.length >= vm.minLength) {
                        if(vm.method === 'get') {
                            axios.get(vm.action + `?query=${search}${vm.paramsString}`).then(res => {
                                vm.options = res.data.items;
                                vm.isLoading = false;
                            }).catch(error => console.log(error));
                        } else if(vm.method === 'post') {
                            axios.post(vm.action, {
                                query: search,
                            }).then(res => {
                                vm.options = res.data.data.items;
                                vm.isLoading = false;
                            }).catch(error => console.log(error));
                        }
                    } else {
                        vm.isLoading = false;
                    }
                }, 350);
            },
        }
    }
</script>
