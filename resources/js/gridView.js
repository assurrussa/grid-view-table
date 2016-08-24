Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

// register the grid component
Vue.component('grid-view', {
    template: '#grid-template',
    props: {
        data: [],
        columns: [],
        sort: ''
    },
    data: {
        sortKey: '',
        sortOrders: []
    },
    methods: {
        sortList: function () {
            if (!this.sortOrders) {
                var sortOrders = {};
                if (this.columns) {
                    this.columns.forEach(function (column) {
                        sortOrders[column.key] = 1
                    });
                }
                this.sortOrders = sortOrders;
            }
        },
        sortBy: function (key, bool) {
            if (!bool) {
                return false;
            }
            this.sortList();
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
            var orderBy = this.sortOrders[key] > 0;
            this.$parent.sortBy(key, orderBy);
        },
        selectedSelect: function (key, value) {
            this.$parent.selectedSelect(key, value);
        }
    }
});

Vue.filter('timeAgo', function(value) {
    return moment.utc(value).local().fromNow();
});

// gridList
var gridList = new Vue({
    el: '#gridList',
    ready: function () {
        if ((history.state == null) || (history.state.data == undefined)) {
            this.fetchListAll();
        } else {
            this.$set('gridColumns', history.state.data.headers);
            this.$set('gridData', history.state.data.data.data);
            this.$set('gridPagination', history.state.data.pagination);
            this.$set('gridFrom', history.state.data.data.from);
            this.$set('gridTo', history.state.data.data.to);
            this.$set('gridTotal', history.state.data.data.total);
            this.$set('createButton', history.state.data.createButton);
            this.$set('customButton', history.state.data.customButton);
            this.$set('gridCurrentPage', history.state.data.page);
            this.$set('orderBy', history.state.data.orderBy);
            this.$set('sortName', history.state.data.sortName);
            // this.$set('filter', history.state.data.filter);
            // this.$set('searchQuery', history.state.data.search);
            // this.$set('gridCountPage', history.state.data.count);
            this.loadIcon = false;
        }
    },

    data: {
        searchQuery: '', // поиск по слову
        createButton: '', // кнопка создания страницы
        customButton: '', // кнопка кастомная
        loadIcon: false, // показ иконки
        sortBool: true, // сортировка по умолчанию включена для колонки
        screening: false, // Выводить экранированные данные или нет
        sortName: '', // сортровка какой колонки требуется
        gridCountPage: 10, // количество элементов на страние списка
        gridCurrentPage: 1, // текущая страница
        gridPagination: '', // пагинация
        gridFrom: 0, // от
        gridTo: 0, // до
        gridTotal: 0, // Общее количество
        orderBy: 'dsc', // сортировка - порядок
        filter: {}, // массив для фильтрации
        resultFilter: '' // результат фильтрации массивов
    },

    watch: {
        gridCountPage: function (value, oldValue) {
            if ((value == '') || (value == undefined)) {
                return false;
            }
            this.gridCurrentPage = 1;
            this.gridCountPage = value;
            this.fetchListAll();
        },

        searchQueries: function (value, oldValue) {
            this.gridCurrentPage = 1;
            this.searchQuery = value;
            this.fetchListAll();
        }
    },
    methods: {
        fetchListAll: function () {
            this.loadIcon = true;
            var currentPage = window.location.href,
                object,
                urlSearch = window.location.search,
                listSearch = {},
                str = '';
            /**
             *
             * @param array
             * @param arrayConcat
             * @returns {{}}
             */
            objects = function (array, arrayConcat) {
                var listResult = {},
                    key;
                for (key in array) {
                    if (array.hasOwnProperty(key)) {
                        listResult[key] = array[key];
                    }
                }
                for (key in arrayConcat) {
                    if (arrayConcat.hasOwnProperty(key)) {
                        listResult[key] = arrayConcat[key];
                    }
                }
                return listResult;
            };
            object = {
                sortName: this.sortName,
                orderBy: this.orderBy,
                search: this.searchQuery,
                page: this.gridCurrentPage,
                count: this.gridCountPage
            };
            object = objects(this.filter, object);

            currentPage = currentPage.replace(urlSearch, '');
            currentPage = currentPage.replace(/\/create$/, '');
            currentPage = currentPage.replace(/\/([0-9]+)\/edit/, '');
            currentPage = currentPage.replace(/\/$/, '');
            if(urlSearch != '') {
                // для изменения урла.
                var key;
                tmp = decodeURIComponent(urlSearch.substr(1)).split('&');   // разделяем переменные
                for(var i=0; i < tmp.length; i++) {
                    tmp2 = tmp[i].split('=');     // массив param будет содержать
                    listSearch[tmp2[0]] = tmp2[1];       // пары ключ(имя переменной)->значение
                }
                for (key in object) {
                    console.log(object);
                    delete listSearch[key];
                }
                for (key in listSearch) {
                    str += '&' + key + '=' + listSearch[key];
                }
                str = (str.substr(1) != '') ? '?' + str.substr(1) : '';
                history.pushState(null, null, currentPage + str);
            }
            for (key in object) {
                urlSearch += '&' + key + '=' + object[key];
            }

            urlSearch = (urlSearch.substr(1) != '') ? '?' + urlSearch.substr(1) : '';

            this.$http
                .get(currentPage + '/sync' + urlSearch)
                .then(function (response) {
                    if (!history.state) {
                        history.pushState(response, null);
                    } else {
                        history.replaceState(response, null);
                    }
                    this.$set('gridColumns', response.data.headers);
                    this.$set('gridData', response.data.data.data);
                    this.$set('gridPagination', response.data.pagination);
                    this.$set('gridFrom', response.data.data.from);
                    this.$set('gridTo', response.data.data.to);
                    this.$set('gridTotal', response.data.data.total);
                    this.$set('createButton', response.data.createButton);
                    this.$set('customButton', response.data.customButton);
                    this.$set('gridCurrentPage', response.data.page);
                    this.$set('orderBy', response.data.orderBy);
                    this.$set('sortName', response.data.sortName);
                    // this.$set('filter', _response.data.filter);
                    // this.$set('searchQuery', _response.data.search);
                    // this.$set('gridCountPage', _response.data.count);
                    this.loadIcon = false;
                }, function ($errors) {
                    // console.log('errors' + $errors);
                });
        },
        onPage: function (value, e) {
            e.preventDefault();
            if ((value == '') || (value == undefined)) {
                return false;
            }
            this.gridCurrentPage = value;
            this.fetchListAll();
        },
        sortBy: function (key, orderBy) {
            this.sortName = key;
            this.orderBy = (orderBy) ? 'ASC' : "DESC";
            this.fetchListAll();
        },
        selectedSelect: function (key, value) {
            console.log(key, value);
            if(value == '') {
                delete this.filter[key];
            } else {
                this.filter[key] = value;
            }
            this.gridCurrentPage = 1;
            this.fetchListAll();
        }
    }
});