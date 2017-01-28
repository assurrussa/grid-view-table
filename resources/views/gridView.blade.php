<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
?>
<form id="<?= $data->getElementName(); ?>" action="">
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
        var timer, milliSeconds = 600;

        window.AmiGridJS = {
            /**
             * Initialize
             */
            initialize: function () {
                this.modules();
                this.setUpListeners();
            },
            /**
             * Modules
             */
            modules: function () {
            },
            /**
             * Set up listeners
             */
            setUpListeners: function () {
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
             * @param url
             * @param data
             */
            getEntities: function (url, data) {
                data = data == 'undefined' ? [] : data;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    $.ajax({
                        url: url,
                        data: data,
                        onDropdownHide: function (event) {
                            alert('Dropdown closed.');
                        }
                    }).done(function (data) {
                        AmiGridJS.loadingHide();
                        $('#<?= $data->getElementName(); ?>').html(data);
                    }).fail(function () {
                        console.info('could not be loaded.');
                    });
                }, milliSeconds);
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

                clearTimeout(timer);
                timer = setTimeout(function () {
                    $('#<?= $data->getElementName(); ?> #js-filterButtonSubmitForm').click();
                }, milliSeconds);
            },
            /**
             *
             */
            filterCheckedChanged: function (e) {
                e.preventDefault();
                clearTimeout(timer);
                timer = setTimeout(function () {
                    $('#<?= $data->getElementName(); ?> #js-filterButtonSubmitForm').click();
                }, milliSeconds);
            },
            /**
             *
             */
            filterPagination: function (e) {
                e.preventDefault();
                var $button = $(this),
                        url = $button.attr('href');
                AmiGridJS.loadingShow();
                AmiGridJS.getEntities(url);
                window.history.pushState("", "", url);
            },
            /**
             *
             */
            filterSearchClearSubmit: function (e) {
                e.preventDefault();
                var $that = $(this),
                        url = window.location.pathname;
                AmiGridJS.loadingShow();
                AmiGridJS.getEntities(url);
                window.history.pushState("", "", url);
            },
            /**
             *
             */
            filterSubmitForm: function (e) {
                e.preventDefault();
                var $form = $(this).parents('form'),
                        data = $form.serializeArray(),
                        url = $form.attr('action'),
                        dataResult = [],
                        result;
                console.log($form);
                $(data).each(function (i, elem) {
                    if (elem.name == '_token') {
                    } else {
                        dataResult.push(elem);
                    }
                });
                AmiGridJS.loadingShow();
                AmiGridJS.getEntities(url, dataResult);
                result = $.param(dataResult);
                if (result != '') {
                    result = '?' + result;
                }
                window.history.pushState("", "", url + result);
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
            /**
             * Распрсивает json текст и выводит нужные сообщения.
             * тип массива [{message: '...', type: '...', ...}, {...}]
             *
             * @param data
             */
            notifyParseJson: function (data) {
                // list = $.parseJSON(data);
                $(data).each(function (i, element) {
                    $.notify({message: element.message}, {type: element.type});
                });
            }
        };
        AmiGridJS.initialize();

        $(document).on('click', '#<?= $data->getElementName(); ?> .js-amiTableHeader', AmiGridJS.filterTableHeader);
        $(document).on('input', '#<?= $data->getElementName(); ?> #js-amiSearchInput', AmiGridJS.filterCheckedChanged);
        $(document).on('input', '#<?= $data->getElementName(); ?> .js-textFilter > input[type="text"]', AmiGridJS.filterCheckedChanged);
        $(document).on('change', '#<?= $data->getElementName(); ?> .js-selectFilter', AmiGridJS.filterCheckedChanged);
        $(document).on('change', '#<?= $data->getElementName(); ?> #js-amiSelectCount', AmiGridJS.filterCheckedChanged);
        $(document).on('click', '#<?= $data->getElementName(); ?> .js-filterSearchPagination .pagination a', AmiGridJS.filterPagination);
        $(document).on('click', '#<?= $data->getElementName(); ?> #js-filterSearchClearSubmit', AmiGridJS.filterSearchClearSubmit);
        $(document).on('click', '#<?= $data->getElementName(); ?> #js-filterButtonSubmitForm', AmiGridJS.filterSubmitForm);
        $(document).on('change', '#<?= $data->getElementName(); ?> .js-adminSelectAll', AmiGridJS.filterSelectCheckedInput);
        $(document).delegate('#<?= $data->getElementName(); ?> .js-adminCheckboxRow', 'change', AmiGridJS.filterCheckboxArrow);
    });
</script>
@endpush