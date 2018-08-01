@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<form id="{{ $data->getElementName() }}" action="{{ $data->formAction }}" class="AmiTableBox">
    <div class="js_amiProgressBar AmiTableBox_progress"></div>
    @include('amiGrid::part.grid', ['data' => $data])
    <div id="js_loadingNotification" class="position-fixed-center" style="display: none">
        <div class="spinner" style="margin: 100px auto;">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
</form>
{{--@push('scripts')--}}
    {{--<script>--}}
        {{--$(function () {--}}
            {{--'use strict';--}}

            {{--window.AmiGridJS = {--}}
                {{--gridId: 'js_amiGridList_1',--}}
                {{--eventPopstate: false,--}}
                {{--timer: null,--}}
                {{--milliSeconds: 600,--}}
                {{--/**--}}
                 {{--* Initialize--}}
                 {{--*/--}}
                {{--init: function () {--}}
                    {{--this.initEventPopstate();--}}
                    {{--$(document).on('click', '#' + this.gridId + ' .js_amiTableHeader', this.filterTableHeader);--}}
                    {{--$(document).on('input', '#' + this.gridId + ' #js_amiSearchInput', this.filterCheckedChanged);--}}
                    {{--$(document).on('input', '#' + this.gridId + ' .js_textFilter > input[type="text"]', this.filterCheckedChanged);--}}
                    {{--$(document).on('change', '#' + this.gridId + ' .js_selectFilter', this.filterCheckedChanged);--}}
                    {{--$(document).on('change', '#' + this.gridId + ' #js_amiSelectCount', this.filterCheckedChanged);--}}
                    {{--$(document).on('click', '#' + this.gridId + ' .js_filterSearchPagination .pagination a', this.filterPagination);--}}
                    {{--$(document).on('click', '#' + this.gridId + ' #js_filterSearchClearSubmit', this.filterSearchClearSubmit);--}}
                    {{--$(document).on('click', '#' + this.gridId + ' #js_filterButtonSubmitForm', this.filterSubmitForm);--}}
                    {{--$(document).on('change', '#' + this.gridId + ' .js_adminSelectAll', this.filterSelectCheckedInput);--}}
                    {{--$(document).delegate('#' + this.gridId + ' .js_adminCheckboxRow', 'change', this.filterCheckboxArrow);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--initEventPopstate: function () {--}}
                    {{--if (this.eventPopstate) {--}}
                        {{--// Глобальные события--}}
                        {{--addEventListener("popstate", function (e) {--}}
                            {{--e.preventDefault();--}}
                            {{--// window.location = window.location.href;--}}
                        {{--}, false);--}}
                    {{--}--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--loadingShow: function () {--}}
                    {{--$('#js_loadingNotification').show();--}}
                    {{--$('#js_loadCatalogItems').find('.js_loaderBody').css('opacity', 0.3);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--loadingHide: function () {--}}
                    {{--$('#js_loadingNotification').hide();--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--* @param  url--}}
                 {{--* @param  data--}}
                 {{--*/--}}
                {{--getEntities: function (url, data) {--}}
                    {{--data = data == 'undefined' ? [] : data;--}}
                    {{--var dataResult = [];--}}

                    {{--dataResult.push({name: 'location', value: window.location.pathname});--}}
                    {{--$(data).each(function (i, elem) {--}}
                        {{--if (elem.name == '_token') {--}}
                        {{--} else {--}}
                            {{--dataResult.push(elem);--}}
                        {{--}--}}
                    {{--});--}}
                    {{--clearTimeout(this.timer);--}}
                    {{--this.timer = setTimeout(function () {--}}
                        {{--AmiGridJS.loadingShow();--}}
                        {{--$.ajax({--}}
                            {{--url: url,--}}
                            {{--data: dataResult,--}}
                        {{--}).done(function (data) {--}}
                            {{--AmiGridJS.loadingHide();--}}
                            {{--data = JSON.parse(data);--}}
                            {{--window.history.pushState("", "", data.url);--}}
                            {{--$('#' + AmiGridJS.gridId + '').html(data.data);--}}
                        {{--}).fail(function () {--}}
                            {{--console.info('could not be loaded.');--}}
                        {{--});--}}
                    {{--}, this.milliSeconds);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterTableHeader: function (e) {--}}
                    {{--var $that = $(this),--}}
                        {{--$children = $that.children('span');--}}
                    {{--if ($that.find('input').length == 1) {--}}
                        {{--return;--}}
                    {{--}--}}
                    {{--e.preventDefault();--}}

                    {{--if ($children.hasClass('asc')) {--}}
                        {{--$children.removeClass('asc').addClass('desc');--}}
                        {{--$that.siblings().removeClass('active');--}}
                        {{--$that.removeClass('active');--}}
                        {{--$('#js_amiOrderBy').val('desc');--}}
                    {{--} else if ($children.hasClass('desc')) {--}}
                        {{--$('#js_amiOrderBy').val('asc');--}}
                        {{--$children.removeClass('desc').addClass('asc');--}}
                        {{--$that.siblings().removeClass('active');--}}
                        {{--$that.removeClass('active');--}}
                    {{--} else {--}}
                        {{--return;--}}
                    {{--}--}}
                    {{--$('#js_amiSortName').val($that.data('name'));--}}
                    {{--$that.addClass('active');--}}

                    {{--clearTimeout(this.timer);--}}
                    {{--this.timer = setTimeout(function () {--}}
                        {{--$('#' + AmiGridJS.gridId + ' #js_filterButtonSubmitForm').click();--}}
                    {{--}, this.milliSeconds);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterCheckedChanged: function (e) {--}}
                    {{--e.preventDefault();--}}
                    {{--clearTimeout(this.timer);--}}
                    {{--this.timer = setTimeout(function () {--}}
                        {{--$('#' + AmiGridJS.gridId + ' #js_filterButtonSubmitForm').click();--}}
                    {{--}, this.milliSeconds);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterPagination: function (e) {--}}
                    {{--e.preventDefault();--}}
                    {{--var $button = $(this),--}}
                        {{--url = $button.attr('href');--}}
                    {{--AmiGridJS.getEntities(url);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterSearchClearSubmit: function (e) {--}}
                    {{--e.preventDefault();--}}
                    {{--var $that = $(this),--}}
                        {{--url = window.location.pathname;--}}
                    {{--AmiGridJS.getEntities(url);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterSubmitForm: function (e) {--}}
                    {{--e.preventDefault();--}}
                    {{--var $form = $(this).parents('form'),--}}
                        {{--data = $form.serializeArray(),--}}
                        {{--url = $form.attr('action');--}}
                    {{--AmiGridJS.getEntities(url, data);--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterSelectCheckedInput: function () {--}}
                    {{--var checked = $(this).is(':checked');--}}
                    {{--$('.js_adminCheckboxRow').prop('checked', checked).filter(':first').change();--}}
                {{--},--}}
                {{--/**--}}
                 {{--*--}}
                 {{--*/--}}
                {{--filterCheckboxArrow: function () {--}}
                    {{--var selected = [];--}}
                    {{--$('.js_adminCheckboxRow:checked').each(function () {--}}
                        {{--selected.push($(this).val());--}}
                    {{--});--}}
                    {{--$('.js_btnCustomAction').each(function () {--}}
                        {{--var $this = $(this);--}}
                        {{--var url = $this.data('href') + selected.join(',');--}}
                        {{--$this.attr('href', url);--}}
                    {{--});--}}
                {{--},--}}
            {{--};--}}
            {{--AmiGridJS.milliSeconds = parseInt('{{ $data->milliSeconds }}');--}}
            {{--AmiGridJS.gridId = '{{ $data->getElementName() }}';--}}
            {{--AmiGridJS.init();--}}
        {{--});--}}
    {{--</script>--}}
{{--@endpush--}}