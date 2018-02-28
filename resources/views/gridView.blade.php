@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<form id="{{ $data->getElementName() }}" action="{{ $data->formAction }}">
    @include('amiGrid::part.grid', ['data' => $data])
    <div id="js-loadingNotification" class="position-fixed-center">
        <div class="cssload-loader"></div>
    </div>
</form>
<style>
    .box {
        padding: 15px;
    }

    .position-fixed-center {
        display: none;
        position: fixed;
        top: 45%;
        left: 48%;
    }

    .images ul li {
        position: relative;
        text-align: center;
    }

    .table > thead > tr > th {
        vertical-align: middle !important;
    }

    th {
        width: auto;
        background-color: #f4f4f4 !important;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        position: relative;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        padding-right: 15px !important;
    }

    th.active {
        background-color: #e5e5e5 !important;
    }

    th.active .arrow {
        opacity: 1;
    }

    tr {
        max-height: 200px;
        overflow: hidden;
    }

    .arrow {
        display: inline-block;
        vertical-align: middle;
        position: absolute;
        right: 5px;
        top: 17px;
    }

    .arrow.asc {
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 5px solid #333;
    }

    .arrow.desc {
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid #333;
    }

    .content-box {
        overflow: auto;
    }
</style>
@push('scripts')
    <script>
        $(function () {
            'use strict';

            window.AmiGridJS = {
                gridId: 'js-amiGridList_1',
                eventPopstate: false,
                timer: null,
                milliSeconds: 600,
                /**
                 * Initialize
                 */
                init: function () {
                    this.initEventPopstate();
                    $(document).on('click', '#' + this.gridId + ' .js-amiTableHeader', this.filterTableHeader);
                    $(document).on('input', '#' + this.gridId + ' #js-amiSearchInput', this.filterCheckedChanged);
                    $(document).on('input', '#' + this.gridId + ' .js-textFilter > input[type="text"]', this.filterCheckedChanged);
                    $(document).on('change', '#' + this.gridId + ' .js-selectFilter', this.filterCheckedChanged);
                    $(document).on('change', '#' + this.gridId + ' #js-amiSelectCount', this.filterCheckedChanged);
                    $(document).on('click', '#' + this.gridId + ' .js-filterSearchPagination .pagination a', this.filterPagination);
                    $(document).on('click', '#' + this.gridId + ' #js-filterSearchClearSubmit', this.filterSearchClearSubmit);
                    $(document).on('click', '#' + this.gridId + ' #js-filterButtonSubmitForm', this.filterSubmitForm);
                    $(document).on('change', '#' + this.gridId + ' .js-adminSelectAll', this.filterSelectCheckedInput);
                    $(document).delegate('#' + this.gridId + ' .js-adminCheckboxRow', 'change', this.filterCheckboxArrow);
                },
                /**
                 *
                 */
                initEventPopstate: function () {
                    if (this.eventPopstate) {
                        // Глобальные события
                        addEventListener("popstate", function (e) {
                            e.preventDefault();
                            // window.location = window.location.href;
                        }, false);
                    }
                },
                /**
                 *
                 */
                loadingShow: function () {
                    $('#js-loadingNotification').show();
                    $('#js-loadCatalogItems').find('.js-loaderBody').css('opacity', 0.3);
                },
                /**
                 *
                 */
                loadingHide: function () {
                    $('#js-loadingNotification').hide();
                },
                /**
                 *
                 * @param  url
                 * @param  data
                 */
                getEntities: function (url, data) {
                    data = data == 'undefined' ? [] : data;
                    var dataResult = [];

                    dataResult.push({name: 'location', value: window.location.pathname});
                    $(data).each(function (i, elem) {
                        if (elem.name == '_token') {
                        } else {
                            dataResult.push(elem);
                        }
                    });
                    clearTimeout(this.timer);
                    this.timer = setTimeout(function () {
                        AmiGridJS.loadingShow();
                        $.ajax({
                            url: url,
                            data: dataResult,
                        }).done(function (data) {
                            AmiGridJS.loadingHide();
                            data = JSON.parse(data);
                            window.history.pushState("", "", data.url);
                            $('#' + AmiGridJS.gridId + '').html(data.data);
                        }).fail(function () {
                            console.info('could not be loaded.');
                        });
                    }, this.milliSeconds);
                },
                /**
                 *
                 */
                filterTableHeader: function (e) {
                    var $that = $(this),
                        $children = $that.children('span');
                    if ($that.find('input').length == 1) {
                        return;
                    }
                    e.preventDefault();

                    if ($children.hasClass('asc')) {
                        $children.removeClass('asc').addClass('desc');
                        $that.siblings().removeClass('active');
                        $that.removeClass('active');
                        $('#js-amiOrderBy').val('desc');
                    } else if ($children.hasClass('desc')) {
                        $('#js-amiOrderBy').val('asc');
                        $children.removeClass('desc').addClass('asc');
                        $that.siblings().removeClass('active');
                        $that.removeClass('active');
                    } else {
                        return;
                    }
                    $('#js-amiSortName').val($that.data('name'));
                    $that.addClass('active');

                    clearTimeout(this.timer);
                    this.timer = setTimeout(function () {
                        $('#' + AmiGridJS.gridId + ' #js-filterButtonSubmitForm').click();
                    }, this.milliSeconds);
                },
                /**
                 *
                 */
                filterCheckedChanged: function (e) {
                    e.preventDefault();
                    clearTimeout(this.timer);
                    this.timer = setTimeout(function () {
                        $('#' + AmiGridJS.gridId + ' #js-filterButtonSubmitForm').click();
                    }, this.milliSeconds);
                },
                /**
                 *
                 */
                filterPagination: function (e) {
                    e.preventDefault();
                    var $button = $(this),
                        url = $button.attr('href');
                    AmiGridJS.getEntities(url);
                },
                /**
                 *
                 */
                filterSearchClearSubmit: function (e) {
                    e.preventDefault();
                    var $that = $(this),
                        url = window.location.pathname;
                    AmiGridJS.getEntities(url);
                },
                /**
                 *
                 */
                filterSubmitForm: function (e) {
                    e.preventDefault();
                    var $form = $(this).parents('form'),
                        data = $form.serializeArray(),
                        url = $form.attr('action');
                    AmiGridJS.getEntities(url, data);
                },
                /**
                 *
                 */
                filterSelectCheckedInput: function () {
                    var checked = $(this).is(':checked');
                    $('.js-adminCheckboxRow').prop('checked', checked).filter(':first').change();
                },
                /**
                 *
                 */
                filterCheckboxArrow: function () {
                    var selected = [];
                    $('.js-adminCheckboxRow:checked').each(function () {
                        selected.push($(this).val());
                    });
                    $('.js-btnCustomAction').each(function () {
                        var $this = $(this);
                        var url = $this.data('href') + selected.join(',');
                        $this.attr('href', url);
                    });
                },
            };
            AmiGridJS.milliSeconds = parseInt('{{ $data->milliSeconds }}');
            AmiGridJS.gridId = '{{ $data->getElementName() }}';
            AmiGridJS.init();
        });
    </script>
@endpush