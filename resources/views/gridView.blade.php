<!-- component template -->
<template id="grid-template">
    <table class="table table-stripped table-hover table-condensed">
        <thead>
        <tr>
            <th v-for="column in columns"
            @click="sortBy(column.key, column.sortBool)"
            :class="{active: sort == column.key}">
            <span v-if="column.screening && (column.key == 'checkbox')">
                @{{{ column.value }}}
            </span>
            <span v-else>
                @{{ column.value }}
            </span>
            <span class="arrow" v-if="column.sortBool"
                  :class="sortOrders ? sortOrders[column.key] > 0 ? 'dsc' : 'asc' : (column.key == sort) ? 'asc' : 'dsc'"></span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="entry in data | filterBy | sortKey sortOrders[sortKey]">
            <td v-for="column in columns">
                <span v-if="column.screening">
                    @{{{ entry[column.key] }}}
                </span>
                <span v-else>@{{ entry[column.key] }}</span>
            </td>
        </tr>
        <tr>
            <td v-for="column in columns">
                <select v-if="column.filter.data" v-model="selected[column.filter.name]"
                @change="selectedSelect(column.filter.name, selected[column.filter.name])"
                class="form-control js-selectFilter"
                data-name="@{{ column.filter.name }}">
                <option disabled>{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.selectFilter') }}</option>
                <option value=""></option>
                <option v-for="(id, name) in column.filter.data" value="@{{ id }}"> @{{ name }}</option>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<!-- gridList root element -->
<div id="gridList">
    <div class="box">

        <div class="row">
            <div class="col-sm-6">
                <label>Отображать
                    <select class=" input-sm"
                            v-model="gridCountPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                    записей</label>
                @{{{ createButton }}}
            </div>
            <div class="col-sm-6">
                @{{{ customButton }}}
                <form id="search" class="pull-right">
                    <label>Поиск:
                        <input type="search" spellcheck="true" name="query" class="form-control input-sm"
                               v-model="searchQueries"
                               debounce=300>
                    </label>
                </form>
            </div>
        </div>

        <div class="content-box">
            <grid-view
                    :data="gridData"
                    :columns="gridColumns"
                    :sort="sortName"
                    :order="orderBy">
            </grid-view>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div>
                    Записи с @{{ gridFrom }} по @{{ gridTo }} из @{{ gridTotal }}
                </div>
            </div>
            <div class="col-sm-6">
                <ul class="pagination pagination-sm pull-right">

                    <li :class="link.status" v-for="link in gridPagination">
                        <a @click="onPage(link.page, $event)" v-href="link.url">
                        @{{{ link.page }}} @{{{ link.text }}}
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <span v-if="loadIcon" style="position:fixed; top:45%;left:48%;">
        <div class="cssload-loader"></div>
    </span>
</div>

<style>
    .box {
        padding: 15px;
    }

    .content-box {
        overflow: auto;
    }

    .table > thead > tr > th {
        vertical-align: middle !important;
    }

    th {
        background-color: #42b983 !important;
        color: rgba(255, 255, 255, 0.66);
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -user-select: none;
    }

    th.active {
        color: #fff;
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
        width: 0;
        height: 0;
        margin-left: 5px;
        opacity: 0.66;
    }

    .arrow.asc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: 4px solid #fff;
    }

    .arrow.dsc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #fff;
    }

    #search {
        margin-bottom: 10px;
    }
</style>
<script src="{{ asset('js/vendor.js') }}"></script>
<script src="{{ asset('js/gridView.js') }}"></script>
<script src="{{ asset('js/jQuery-2.1.4.min.js') }}"></script>
<script>
    $(function () {
        $(document).on('change', '.js-adminSelectAll', function () {
            var checked = $(this).is(':checked');
            $('.js-adminCheckboxRow').prop('checked', checked).filter(':first').change();
        });
        $(document).delegate('.js-adminCheckboxRow', 'change', function () {
            var selected = [];
            $('.js-adminCheckboxRow:checked').each(function () {
                selected.push($(this).val());
            });
            $('.js-btnCustomAction').each(function () {
                var $this = $(this);
                var url = $this.data('href') + selected.join(',');
                $this.attr('href', url);
            });
        });
    });
</script>