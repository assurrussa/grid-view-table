class AmiGridJS {
    constructor(opts) {
        const defaults = {
            /**
             * The ID of the html element containing the grid
             */
            id: 'js_amiGridList_amiGrid_1',
            timer: null,
            milliSeconds: 600,
        };
        Object.assign(defaults, opts || {});
        this.options = defaults;

        this.$el = document.querySelector('#' + this.options.id);
        this.history = [];

        this.init();
    }

    /**
     * Initialize
     */
    init() {
        this.onPopState();
        this.initComponent();
    }


    /**
     * Initialize
     */
    initComponent() {
        this.newInstanceEvent('click', '.js_amiTableHeader', this.filterTableHeader);
        this.newInstanceEvent('enter', '#js_amiSearchInput', this.filterSubmitForm);
        this.newInstanceEvent('enter', '.js_textFilter > input[type="text"]', this.filterSubmitForm);
        this.newInstanceEvent('change', '.js_selectFilter', this.filterSubmitForm);
        this.newInstanceEvent('change', '#js_amiSelectCount', this.filterSubmitForm);
        this.newInstanceEvent('click', '.js_filterSearchPagination .pagination a', this.filterPagination);
        this.newInstanceEvent('click', '#js_filterSearchClearSubmit', this.filterSearchClearSubmit);
        this.newInstanceEvent('click', '#js_filterButtonSubmitForm', this.filterSubmitForm);
        this.newInstanceEvent('change', '.js_adminSelectAll', this.filterSelectCheckedInput);
        this.newInstanceEvent('change', '.js_adminCheckboxRow', this.filterCheckboxArrow);
    }

    /**
     *
     */
    loadingShow() {
        this.$el.querySelector('table tbody').style.opacity = 0.3;
    }

    /**
     *
     */
    loadingHide() {
        this.$el.querySelector('table tbody').style.opacity = 1;
    }

    /**
     *
     */
    filterTableHeader(e) {
        e.preventDefault();

        let $that = e.target,
            $childrens = $that.querySelectorAll('span'),
            allTh = this.$el.querySelectorAll('.js_amiTableHeader');
        if ($that.querySelectorAll('input').length == 1) {
            return;
        }


        for (let i = 0, di = allTh.length; i < di; i++) {
            let th = allTh[i];
            if (th) {
                th.classList.remove('active');
            }
        }

        for (let i = 0, di = $childrens.length; i < di; i++) {
            let children = $childrens[i];
            if (children) {
                if (children.classList.contains('asc')) {
                    children.classList.remove('asc');
                    children.classList.add('desc');
                    this.$el.querySelector('#js_amiOrderBy').value = 'desc';
                } else if (children.classList.contains('desc')) {
                    children.classList.remove('desc');
                    children.classList.add('asc');
                    this.$el.querySelector('#js_amiOrderBy').value = 'asc';
                }
            }
        }
        this.$el.querySelector('#js_amiSortName').value = $that.getAttribute('data-name');
        $that.classList.add('active');

        this.filterSubmitForm(e);
    }

    /**
     *
     */
    filterPagination(e) {
        e.preventDefault();
        this.onSend(e.target.getAttribute('href'));
    }

    /**
     *
     */
    filterSearchClearSubmit(e) {
        e.preventDefault();
        this.onSend(window.location.pathname);
    }

    /**
     *
     */
    filterSubmitForm(e) {
        e.preventDefault();
        let form = this.$el,
            url = form.getAttribute('action'),
            formLocation = form.querySelector('#js_amiLocation');
        if (formLocation) {
            formLocation.value = window.location.pathname;
        }
        setTimeout(() => {
            let formData = FormSerialize(form, {hash: true}),
                formDataString = FormSerialize(form, {hash: false});
            this.onSend(url, formDataString);
        }, 10);
    }

    /**
     *
     */
    filterSelectCheckedInput() {
        // let checked = $(this).is(':checked');
        // $('.js_adminCheckboxRow').prop('checked', checked).filter(':first').change();
    }

    /**
     *
     */
    filterCheckboxArrow() {
        // let selected = [];
        // $('.js_adminCheckboxRow:checked').each(function () {
        //     selected.push($(this).val());
        // });
        // $('.js_btnCustomAction').each(function () {
        //     let $this = $(this);
        //     let url = $this.data('href') + selected.join(',');
        //     $this.attr('href', url);
        // });
    }

    /**
     *
     * @param {String} event
     * @param {String} elementName
     * @param {Function} callback
     * @returns {*}
     */
    newInstanceEvent(event, elementName, callback) {
        setTimeout(() => {
            let data = this.$el.querySelectorAll(elementName);
            if (data && data.length) {
                for (let i = 0, dl = data.length; i < dl; i++) {
                    data[i].addEventListener(event, callback.bind(this));
                }
            }
        }, 10);
    }

    /**
     *  Поиск родительсткого элемента по tag
     *
     * @param {Object} el
     * @param {String} tagName
     * @returns {*}
     */
    findParentTag(el, tagName) {
        while ((el = el.parentElement) && !(el.tagName === tagName.toUpperCase())) ;
        return el;
    }

    /**
     *
     * @param {string} url
     * @param {string} data
     */
    onSend(url, data) {
        data = ((data === 'undefined') || (data === undefined)) ? '' : '?' + data;
        this.loadingShow();
        clearTimeout(this.options.timer);
        this.options.timer = setTimeout(() => {
            axios.get(url + data).then((response) => {
                this.history.push({url: response.data.url, data: response.data});
                window.history.pushState(this.history, null, response.data.url);
                this.onSuccess(response.data);
            }).catch((error) => this.onError(error));
        }, this.options.milliSeconds);
    }

    /**
     *
     * @param {Object} response
     */
    onSuccess(response) {
        this.loadingHide();
        this.$el.innerHTML = response.data;
        setTimeout(() => this.initComponent(), 10);
    }

    /**
     *
     * @param {Object} error
     */
    onError(error) {
        console.info('could not be loaded.');
    }

    /**
     * Initialize
     */
    onPopState() {
        window.addEventListener('popstate', function (e) {
            e.preventDefault();

            this.loadingShow();
            if (this.history.length > 0) {
                let historyCurrent = this.history.pop();
                window.history.pushState(this.history, null, historyCurrent.url);
                setTimeout(() => this.onSuccess(historyCurrent.data), 300);
            } else {
                let state = e.state,
                    stateCheck = state ? state[0] : null;
                if (stateCheck !== undefined) {
                    setTimeout(() => this.loadingHide(), 300);
                } else if (state == null) {
                    setTimeout(() => this.loadingHide(), 300);
                } else if(state.length === 0) {
                    setTimeout(() => this.loadingHide(), 300);
                } else {
                    setTimeout(() => this.onSuccess(state), 300);
                }
            }
        }.bind(this));
    }
}

window.AmiGridJS = AmiGridJS;