class AmiGridJS {
    constructor(opts) {
        const defaults = {
            /**
             * The ID of the html element containing the grid
             */
            id: 'js_amiGridList_amiGrid_1',
            ajax: true,
            timer: null,
            milliSeconds: 400,
        };
        Object.assign(defaults, opts || {});
        this.options = defaults;

        this.$el = document.querySelector('#' + this.options.id);

        this.init();
    }

    /**
     * Initialize
     */
    init() {
        this._onPopState();
        this.initComponent();
        this.initVueComponent();
    }


    /**
     * Initialize
     */
    initComponent() {
        this.newInstanceEvent('click', '.js_amiGridFilterTable', this.filterSubmitForm);
        this.newInstanceEvent('click', '.js_amiTableHeader', this.filterTableHeader);
        this.newInstanceEvent('enter', '#js_amiSearchInput', this.filterSubmitForm);
        this.newInstanceEvent('enter', '.js_textFilter > input[type="text"]', this.filterSubmitForm);
        this.newInstanceEvent('change', '.js_selectFilter', this.filterSubmitForm);
        this.newInstanceEvent('change', '#js_amiSelectCount', this.filterSubmitForm);
        this.newInstanceEvent('click', '.js_filterSearchPagination .pagination a', this.filterPagination);
        this.newInstanceEvent('click', '#js_filterSearchClearSubmit', this.filterSearchClearSubmit);
        this.newInstanceEvent('change', '.js_adminSelectAll', this.filterSelectCheckedInput);
        this.newInstanceEvent('change', '.js_adminCheckboxRow', this.filterCheckboxArrow);
    }

    initVueComponent() {
        this.newVueInstanceByClassName('js_InitComponent', true);
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
     * @param e
     * @returns {Promise<any>}
     */
    filterTableHeader(e) {
        let $that = e.target,
            $childrens = $that.querySelectorAll('span'),
            allTh = this.$el.querySelectorAll('.js_amiTableHeader');
        if ($that.type === 'checkbox') {
            return;
        }
        if ($that.querySelectorAll('input').length === 1) {
            return;
        }
        if ($that.querySelectorAll('span.asc, span.desc').length === 0) {
            return;
        }

        e.preventDefault();


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

        return this.onSend();
    }

    /**
     *
     * @param e
     * @returns {Promise<any>}
     */
    filterPagination(e) {
        e.preventDefault();
        return this.onSend(e.target.getAttribute('href'), '');
    }

    /**
     *
     * @param e
     * @returns {Promise<any>}
     */
    filterSearchClearSubmit(e) {
        e.preventDefault();
        return this.onSend(window.location.pathname, '');
    }

    /**
     *
     * @param e
     * @returns {Promise<any>}
     */
    filterSubmitForm(e) {
        e.preventDefault();
        return this.onSend();
    }

    /**
     *
     */
    filterSelectCheckedInput() {
        let checkboxAll = this.$el.querySelectorAll('.js_adminCheckboxRow');
        for (let i = 0, di = checkboxAll.length; i < di; i++) {
            let checkbox = checkboxAll[i];
            if (checkbox) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                } else {
                    checkbox.checked = true;
                }
            }
        }
        this.filterCheckboxArrow();
    }

    /**
     *
     */
    filterCheckboxArrow() {
        let btnCustom = this.$el.querySelector('.js_btnCustomAction');
        if (btnCustom) {
            let selected = [],
                checkboxAll = this.$el.querySelectorAll('.js_adminCheckboxRow');
            for (let i = 0, di = checkboxAll.length; i < di; i++) {
                let checkbox = checkboxAll[i];
                if (checkbox) {
                    if (checkbox.checked) {
                        selected.push(checkbox.value);
                    }
                }
            }
            btnCustom.setAttribute('href', btnCustom.getAttribute('data-url') + selected.join(','));
        }
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
     *
     * @param {String} name
     * @param {Boolean} circleBool
     * @returns false
     */
    newVueInstanceByClassName(name, circleBool) {
        circleBool = circleBool || false;
        let items = document.getElementsByClassName(name);
        if (circleBool) {
            if (items.length) {
                for (let i = 0, dl = items.length; i < dl; i++) {
                    let item = items[i];
                    if (item) {
                        setTimeout(() => {
                            item.classList.remove(name);
                            new Vue({
                                el: item,
                            });
                        }, 10);
                    }
                }
            }
        } else if (items.length) {
            if (items[0]) {
                setTimeout(() => {
                    new Vue({
                        el: items[0],
                    });
                }, 10);
            }
        }

        return false;
    }

    /**
     *
     * @param {string} url
     * @param {string} data
     * @returns {Promise<any>}
     */
    onSend(url = null, data = null) {
        let form = this.$el;

        url = url ? url : form.getAttribute('action');
        if (data === null) {
            let formData = FormSerialize(form, {hash: true}),
                formDataString = FormSerialize(form, {hash: false});

            data = '?' + formDataString;
        } else {
            data = data ? '?' + data : '';
        }

        if (this.options.ajax) {
            this.loadingShow();
            return new Promise((resolve, reject) => {
                clearTimeout(this.options.timer);
                this.options.timer = setTimeout(() => {
                    let urlPath = url + data,
                        windowPathResolve = ('url' + urlPath + 'resolve').replace(/[^a-zA-Z0-9]/g, ''),
                        windowPathReject = ('url' + urlPath + 'reject').replace(/[^a-zA-Z0-9]/g, '');
                    window[windowPathResolve] = resolve;
                    window[windowPathReject] = reject;
                    if(urlPath === window.location.search) {
                        this._onLoadContent(urlPath);
                    } else {
                        History.pushState(null, null, urlPath);
                    }
                }, this.options.milliSeconds);
            });
        } else {
            window.location = url + data;
        }
    }

    /**
     *
     * @param {String} url
     * @private
     */
    _onLoadContent(url) {
        window.sessionStorage.setItem('amiGridAjax', true);
        let windowPathResolve = ('url' + url + 'resolve').replace(/[^a-zA-Z0-9]/g, ''),
            windowPathReject = ('url' + url + 'reject').replace(/[^a-zA-Z0-9]/g, '');
        axios.get(url)
            .then((response) => {
                this._onSuccess(response.data);
                if (window[windowPathResolve]) {
                    window[windowPathResolve](response.data);
                }
            })
            .catch((error) => {
                this._onError(error);
                if (window[windowPathReject]) {
                    window[windowPathReject](error);
                }
            });
    }

    /**
     *
     * @param {Object} response
     * @private
     */
    _onSuccess(response) {
        this.loadingHide();
        this.$el.innerHTML = response;
        setTimeout(() => {
            this.initComponent();
            this.initVueComponent();
        }, 10);
    }

    /**
     *
     * @param {Object} error
     * @private
     */
    _onError(error) {
        console.info('could not be loaded.');
    }

    /**
     * @TODO
     *
     * Initialize
     *
     * @private
     */
    _onPopState() {
        if (this.options.ajax) {
            History.Adapter.bind(window, 'statechange', function () {
                var State = History.getState();

                this.loadingShow();
                if (State && State.url) {
                    this._onLoadContent(State.url);
                } else {
                    this.loadingHide();
                }
            }.bind(this));
        }
    }
}

/**
 * @param  {string} $jsNameProperty
 *
 * @returns  {Promise<any>}
 */
window.amiGridOnSend = function ($jsNameProperty) {
    if (window[$jsNameProperty]) {
        return window[$jsNameProperty].onSend();
    } else {
        console.warn('not found window property "' + $jsNameProperty + '" for AmiGridJS');
        return new Promise((resolve, reject) => {
            reject();
        });
    }
};

// export default AmiGridJS;
window.AmiGridJS = AmiGridJS;