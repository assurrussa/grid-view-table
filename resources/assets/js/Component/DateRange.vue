<template>
    <div class="calendar-root">
        <div class="input-date" :style="styleInputDate" @click="toggleCalendar()"> {{ onGetDateStringRender }}</div>
        <div class="calendar"
             :class="{'calendar-mobile ': isCompact, 'calendar-right-to-left': isRighttoLeft, 'calendar-right-to-left-mobile': isCompact}"
             v-if="isOpen">
            <div class="calendar-head" v-if="!isCompact">
                <h2>{{captionsLocale.title}}</h2>
                <i class="close" @click="toggleCalendar()">&times</i>
            </div>
            <div class="calendar-wrap" :class="{'calendar-wrap-mobile': isCompact}">
                <div class="calendar_month_left" :class="{'calendar-left-mobile': isCompact}" v-if="showMonth">
                    <div class="months-text">
                        <i class="left" @click="goPrevMonth"></i>
                        <i class="right" @click="goNextMonth" v-if="isCompact"></i>
                        {{monthsLocale[activeMonthStart] +' '+ activeYearStart}}
                    </div>
                    <ul :class="s.daysWeeks">
                        <li v-for="item in shortDaysLocale" :key="item">{{item}}</li>
                    </ul>
                    <ul v-for="r in 6" :class="[s.days]" :key="r">
                        <li :class="[{[s.daysSelected]: isDateSelected(r, i, 'first', startMonthDay, endMonthDate),
              [s.daysInRange]: isDateInRange(r, i, 'first', startMonthDay, endMonthDate),
              [s.dateDisabled]: isDateDisabled(r, i, startMonthDay, endMonthDate, activeYearStart, activeMonthStart)}]"
                            v-for="i in numOfDays" :key="i"
                            v-html="getDayCell(r, i, startMonthDay, endMonthDate)"
                            @click="selectFirstItem(r, i)"></li>
                    </ul>
                    <button v-if="isCompact" class="calendar-btn-clear" @click.prevent="showCalendar()">{{captionsLocale.hide_calendar}}
                    </button>
                </div>
                <div class="calendar_month_right" v-if="!isCompact">
                    <div class="months-text">
                        {{monthsLocale[startNextActiveMonth] +' '+ activeYearEnd}}
                        <i class="right" @click="goNextMonth"></i>
                    </div>
                    <ul :class="s.daysWeeks">
                        <li v-for="item in shortDaysLocale" :key="item">{{item}}</li>
                    </ul>
                    <ul v-for="r in 6" :class="[s.days]" :key="r">
                        <li :class="[{[s.daysSelected]: isDateSelected(r, i, 'second', startNextMonthDay, endNextMonthDate),
            [s.daysInRange]: isDateInRange(r, i, 'second', startNextMonthDay, endNextMonthDate),
            [s.dateDisabled]: isDateDisabled(r, i, startNextMonthDay, endNextMonthDate, activeYearEnd, startNextActiveMonth)}]"
                            v-for="i in numOfDays" :key="i" v-html="getDayCell(r, i, startNextMonthDay, endNextMonthDate)"
                            @click="selectSecondItem(r, i)"></li>
                    </ul>
                </div>
            </div>
            <div class="calendar-range" :class="{'calendar-range-mobile ': isCompact}" v-if="!showMonth || !isCompact">
                <ul class="calendar_preset">
                    <li
                            class="calendar_preset-ranges"
                            v-for="(item, idx) in finalPresetRanges"
                            :key="idx"
                            @click="updatePreset(item)"
                            :class="{'active-preset': presetActive === item.label}">
                        {{item.label}}
                    </li>
                    <li v-if="isCompact">
                        <button class="calendar-btn-show" @click.prevent="showCalendar()">{{captionsLocale.show_calendar}}</button>
                    </li>
                    <li v-if="isClear">
                        <button class="calendar-btn-clear" @click.prevent="setDateClear()">{{captionsLocale.clear_button}}</button>
                    </li>
                    <li>
                        <button class="calendar-btn-apply" @click.prevent="setDateValue()">{{captionsLocale.ok_button}}</button>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</template>

<script>
    import fecha from 'fecha'

    const defaultConfig = {};
    const defaultI18n = 'EN';
    const availableMonths = {
        EN: [
            'January', 'February', 'March', 'April', 'May', 'June', 'July',
            'August', 'September', 'October', 'November', 'December'
        ],
        RU: [
            'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль',
            'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ],
    };

    const availableShortDays = {
        EN: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        RU: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    };

    const presetRangeLabel = {
        EN: {
            today: 'Today',
            yesterday: 'Yesterday',
            last7Days: 'Last 7 Days',
            last14Days: 'Last 14 Days',
            thisMonth: 'This Month',
            lastMonth: 'Last Month',
        },
        RU: {
            today: 'Сегодня',
            yesterday: 'Вчера',
            last7Days: 'Последние 7 дней',
            last14Days: 'Последние 14 дней',
            thisMonth: 'Текущий месяц',
            lastMonth: 'Предыдущий месяц',
        },
    };

    const defaultCaptions = {
        EN: {
            title: 'Choose Dates',
            ok_button: 'Apply',
            clear_button: 'Clear',
            show_calendar: 'Calendar',
            hide_calendar: 'Back',
        },
        RU: {
            title: 'Выберите даты',
            ok_button: 'Принять',
            clear_button: 'Очистить',
            show_calendar: 'Календарь',
            hide_calendar: 'Назад',
        },
    };

    const defaultStyle = {
        daysWeeks: 'calendar_weeks',
        days: 'calendar_days',
        daysSelected: 'calendar_days_selected',
        daysInRange: 'calendar_days_in-range',
        firstDate: 'calendar_month_left',
        secondDate: 'calendar_month_right',
        presetRanges: 'calendar_preset-ranges',
        dateDisabled: 'calendar_days--disabled'
    };

    const defaultPresets = function (i18n = defaultI18n) {
        return {
            today: function () {
                const n = new Date();
                const startToday = new Date(n.getFullYear(), n.getMonth(), n.getDate(), 0, 0);
                const endToday = new Date(n.getFullYear(), n.getMonth(), n.getDate(), 23, 59);
                return {
                    label: presetRangeLabel[i18n].today,
                    active: false,
                    dateRange: {
                        start: startToday,
                        end: endToday
                    }
                }
            },
            yesterday: function () {
                const n = new Date();
                const startToday = new Date(n.getFullYear(), n.getMonth(), n.getDate() - 1, 0, 0);
                const endToday = new Date(n.getFullYear(), n.getMonth(), n.getDate() - 1, 23, 59);
                return {
                    label: presetRangeLabel[i18n].yesterday,
                    active: false,
                    dateRange: {
                        start: startToday,
                        end: endToday
                    }
                }
            },
            last7days: function () {
                const n = new Date();
                const start = new Date(n.getFullYear(), n.getMonth(), n.getDate() - 6);
                const end = new Date(n.getFullYear(), n.getMonth(), n.getDate());
                return {
                    label: presetRangeLabel[i18n].last7Days,
                    active: false,
                    dateRange: {
                        start: start,
                        end: end
                    }
                }
            },
            last14days: function () {
                const n = new Date();
                const start = new Date(n.getFullYear(), n.getMonth(), n.getDate() - 13);
                const end = new Date(n.getFullYear(), n.getMonth(), n.getDate());
                return {
                    label: presetRangeLabel[i18n].last14Days,
                    active: false,
                    dateRange: {
                        start: start,
                        end: end
                    }
                }
            },
            thisMonth: function () {
                const n = new Date();
                const startMonth = new Date(n.getFullYear(), n.getMonth(), 1);
                const endMonth = new Date(n.getFullYear(), n.getMonth() + 1, 0);
                return {
                    label: presetRangeLabel[i18n].thisMonth,
                    active: false,
                    dateRange: {
                        start: startMonth,
                        end: endMonth
                    }
                }
            },
            lastMonth: function () {
                const n = new Date();
                const startMonth = new Date(n.getFullYear(), n.getMonth() - 1, 1);
                const endMonth = new Date(n.getFullYear(), n.getMonth(), 0);
                return {
                    label: presetRangeLabel[i18n].lastMonth,
                    active: false,
                    dateRange: {
                        start: startMonth,
                        end: endMonth
                    }
                }
            },
        }
    };

    export default {
        name: 'vue-rangedate-picker',
        props: {
            placeholder: {
                type: String,
                default: ' -- select date range -- '
            },
            clear: {
                type: String,
                default: ''
            },
            createDate: {
                type: String,
                default: '1'
            },
            configs: {
                type: Object,
                default: () => defaultConfig
            },
            i18n: {
                type: String,
                default: defaultI18n
            },
            months: {
                type: Array,
                default: () => null
            },
            shortDays: {
                type: Array,
                default: () => null
            },
            // options for captions are: title, ok_button
            captions: {
                type: Object,
                default: () => null
            },
            styles: {
                type: Object,
                default: () => {
                }
            },
            initRange: {
                type: Object,
                default: () => null
            },
            start: {
                type: String,
                default: null
            },
            end: {
                type: String,
                default: null
            },
            startActiveMonth: {
                type: Number,
                default: new Date().getMonth()
            },
            startActiveYear: {
                type: Number,
                default: new Date().getFullYear()
            },
            subtractYearLast: {
                type: String,
                default: '1',
            },
            presetRanges: {
                type: Object,
                default: () => null
            },
            compact: {
                type: String,
                default: 'false'
            },
            righttoleft: {
                type: String,
                default: 'false'
            },
            format: {
                type: String,
                default: 'DD MMM YYYY' // DD MMM YYYY
            },
            width: {
                type: String,
                default: '200px'
            }
        },
        data() {
            return {
                dateRange: {
                    start: null,
                    end: null,
                },
                i18nLocale: 'EN',
                numOfDays: 7,
                subtractYearLastNumber: 1,
                isFirstChoice: true,
                isOpen: false,
                isClear: false,
                isCreateDate: false,
                presetActive: '',
                showMonth: false,
                activeMonthStart: this.startActiveMonth,
                activeYearStart: this.startActiveYear,
                activeYearEnd: this.startActiveYear,
            }
        },
        created() {
            this.i18nLocale = this.i18n.toUpperCase();
            this.isClear = !!this.clear;
            this.isCreateDate = !!this.createDate;
            this.subtractYearLastNumber = !isNaN(parseInt(this.subtractYearLast)) ? parseInt(this.subtractYearLast) : this.subtractYearLastNumber;
            if (this.activeMonthStart === 11) this.activeYearEnd = this.activeYearStart + 1;
            let dateTmp = new Date();
            if (this.start || this.end) {
                this.dateRange = {
                    start: (this.start ? new Date(this.start) : new Date(dateTmp.getFullYear(), dateTmp.getMonth(), 1)),
                    end: (this.end ? new Date(this.end) : new Date(dateTmp.getFullYear(), dateTmp.getMonth(), this.getLastDayOfMonth(dateTmp.getFullYear(), dateTmp.getMonth()))),
                }
            } else if (this.isCreateDate) {
                this.dateRange = {
                    start: new Date(dateTmp.getFullYear(), dateTmp.getMonth(), 1),
                    end: new Date(dateTmp.getFullYear(), dateTmp.getMonth(), this.getLastDayOfMonth(dateTmp.getFullYear(), dateTmp.getMonth())),
                }
            }
        },
        watch: {
            startNextActiveMonth: function (value) {
                if (value === 0) this.activeYearEnd = this.activeYearStart + 1
            }
        },
        computed: {
            styleInputDate: function () {
                return 'width: ' + (this.width ? this.width : '200px');
            },
            onGetDateStringRender: function () {
                if (this.dateRange.start) {
                    return this.getDateString(this.dateRange.start) + ' - ' + this.getDateString(this.dateRange.end);
                }
                return this.placeholder;
            },
            monthsLocale: function () {
                return this.months || availableMonths[this.i18nLocale]
            },
            shortDaysLocale: function () {
                return this.shortDays || availableShortDays[this.i18nLocale]
            },
            captionsLocale: function () {
                return this.captions || defaultCaptions[this.i18nLocale]
            },
            s: function () {
                return Object.assign({}, defaultStyle, this.style)
            },
            startMonthDay: function () {
                return new Date(this.activeYearStart, this.activeMonthStart, 1).getDay()
            },
            startNextMonthDay: function () {
                return new Date(this.activeYearStart, this.startNextActiveMonth, 1).getDay()
            },
            endMonthDate: function () {
                return new Date(this.activeYearEnd, this.startNextActiveMonth, 0).getDate()
            },
            endNextMonthDate: function () {
                return new Date(this.activeYearEnd, this.activeMonthStart + 2, 0).getDate()
            },
            startNextActiveMonth: function () {
                return this.activeMonthStart >= 11 ? 0 : this.activeMonthStart + 1
            },
            finalPresetRanges: function () {
                const tmp = {}
                const presets = this.presetRanges || defaultPresets(this.i18nLocale)
                for (const i in presets) {
                    const item = presets[i]
                    let plainItem = item
                    if (typeof item === 'function') {
                        plainItem = item()
                    }
                    tmp[i] = plainItem
                }
                return tmp
            },
            isCompact: function () {
                return this.compact === 'true'
            },
            isRighttoLeft: function () {
                return this.righttoleft === 'true'
            }
        },
        methods: {
            getLastDayOfMonth: function (year, month) {
                let date = new Date(year, month + 1, 0);
                return date.getDate();
            },
            toggleCalendar: function () {
                if (this.isCompact) {
                    this.isOpen = !this.isOpen
                    return
                }
                this.isOpen = !this.isOpen
                this.showMonth = !this.showMonth
                return
            },
            getDateString: function (date, format = this.format) {
                if (!date) {
                    return null
                }
                const dateparse = new Date(Date.parse(date))
                return fecha.format(new Date(dateparse.getFullYear(), dateparse.getMonth(), dateparse.getDate()), format)
            },
            getDayIndexInMonth: function (r, i, startMonthDay) {
                const date = (this.numOfDays * (r - 1)) + i
                return date - startMonthDay
            },
            getDayCell(r, i, startMonthDay, endMonthDate) {
                const result = this.getDayIndexInMonth(r, i, startMonthDay)
                // bound by > 0 and < last day of month
                return result > 0 && result <= endMonthDate ? result : '&nbsp;'
            },
            getNewDateRange(result, activeMonth, activeYear) {
                const newData = {}
                let key = 'start'
                if (!this.isFirstChoice) {
                    key = 'end'
                } else {
                    newData['end'] = null
                }
                const resultDate = new Date(activeYear, activeMonth, result)
                if (!this.isFirstChoice && resultDate < this.dateRange.start) {
                    this.isFirstChoice = false
                    return {start: resultDate}
                }

                // toggle first choice
                this.isFirstChoice = !this.isFirstChoice
                newData[key] = resultDate
                return newData
            },
            selectFirstItem(r, i) {
                const result = this.getDayIndexInMonth(r, i, this.startMonthDay)
                this.dateRange = Object.assign({}, this.dateRange, this.getNewDateRange(result, this.activeMonthStart,
                    this.activeYearStart))
                if (this.dateRange.start && this.dateRange.end) {
                    this.presetActive = ''
                    if (this.isCompact) {
                        this.showMonth = false
                    }
                }
            },
            selectSecondItem(r, i) {
                const result = this.getDayIndexInMonth(r, i, this.startNextMonthDay)
                this.dateRange = Object.assign({}, this.dateRange, this.getNewDateRange(result, this.startNextActiveMonth,
                    this.activeYearEnd))
                if (this.dateRange.start && this.dateRange.end) {
                    this.presetActive = ''
                    if (this.isCompact) {
                        this.showMonth = false
                    }
                }
            },
            isDateSelected(r, i, key, startMonthDay, endMonthDate) {
                const result = this.getDayIndexInMonth(r, i, startMonthDay)
                if (result < 1 || result > endMonthDate) return false
                let currDate = null
                if (key === 'first') {
                    currDate = new Date(this.activeYearStart, this.activeMonthStart, result)
                } else {
                    currDate = new Date(this.activeYearEnd, this.startNextActiveMonth, result)
                }
                return (this.dateRange.start && this.dateRange.start.getTime() === currDate.getTime()) ||
                    (this.dateRange.end && this.dateRange.end.getTime() === currDate.getTime())
            },
            isDateInRange(r, i, key, startMonthDay, endMonthDate) {
                const result = this.getDayIndexInMonth(r, i, startMonthDay)
                if (result < 1 || result > endMonthDate) return false

                let currDate = null
                if (key === 'first') {
                    currDate = new Date(this.activeYearStart, this.activeMonthStart, result)
                } else {
                    currDate = new Date(this.activeYearEnd, this.startNextActiveMonth, result)
                }
                return (this.dateRange.start && this.dateRange.start.getTime() < currDate.getTime()) &&
                    (this.dateRange.end && this.dateRange.end.getTime() > currDate.getTime())
            },
            isDateDisabled(r, i, startMonthDay, endMonthDate, currentYear, currentMonth) {
                const result = this.getDayIndexInMonth(r, i, startMonthDay);
                // // bound by > 0 and < last day of month
                return ((currentYear < (this.startActiveYear - this.subtractYearLastNumber))
                    || !(result > 0 && result <= endMonthDate)
                    || (currentMonth > this.startActiveMonth && currentYear >= this.startActiveYear));
            },
            goPrevMonth() {
                const prevMonth = new Date(this.activeYearStart, this.activeMonthStart, 0)
                this.activeMonthStart = prevMonth.getMonth()
                this.activeYearStart = prevMonth.getFullYear()
                this.activeYearEnd = prevMonth.getFullYear()
            },
            goNextMonth() {
                const nextMonth = new Date(this.activeYearEnd, this.startNextActiveMonth, 1)
                this.activeMonthStart = nextMonth.getMonth()
                this.activeYearStart = nextMonth.getFullYear()
                this.activeYearEnd = nextMonth.getFullYear()
            },
            updatePreset(item) {
                this.presetActive = item.label
                this.dateRange = item.dateRange
                // update start active month
                this.activeMonthStart = this.dateRange.start.getMonth()
                this.activeYearStart = this.dateRange.start.getFullYear()
                this.activeYearEnd = this.dateRange.end.getFullYear()
            },
            setDateValue: function () {
                this.$emit('selected', this.dateRange)
                this.toggleCalendar();
            },
            setDateClear: function () {
                this.dateRange = {
                    start: null,
                    end: null,
                    clear: true,
                };
                this.$emit('selected', this.dateRange);
                this.toggleCalendar();
            },
            showCalendar: function () {
                this.showMonth = !this.showMonth;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .input-date {
        display: block;
        border: 1px solid #ccc;
        padding: 5px;
        font-size: 14px;
        width: 200px;
        cursor: pointer;
        &::after {
            content: "▼";
            float: right;
            font-size: smaller;
        }
    }

    .active-preset {
        border: 1px solid #0096d9;
        color: #0096d9;
        border-radius: 3px;
    }

    .months-text {
        text-align: center;
        font-weight: bold;
        .left {
            float: left;
            cursor: pointer;
            width: 16px;
            height: 16px;
            background-image: url("data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDMxLjQ5NCAzMS40OTQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMxLjQ5NCAzMS40OTQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTZweCIgaGVpZ2h0PSIxNnB4Ij4KPHBhdGggZD0iTTEwLjI3Myw1LjAwOWMwLjQ0NC0wLjQ0NCwxLjE0My0wLjQ0NCwxLjU4NywwYzAuNDI5LDAuNDI5LDAuNDI5LDEuMTQzLDAsMS41NzFsLTguMDQ3LDguMDQ3aDI2LjU1NCAgYzAuNjE5LDAsMS4xMjcsMC40OTIsMS4xMjcsMS4xMTFjMCwwLjYxOS0wLjUwOCwxLjEyNy0xLjEyNywxLjEyN0gzLjgxM2w4LjA0Nyw4LjAzMmMwLjQyOSwwLjQ0NCwwLjQyOSwxLjE1OSwwLDEuNTg3ICBjLTAuNDQ0LDAuNDQ0LTEuMTQzLDAuNDQ0LTEuNTg3LDBsLTkuOTUyLTkuOTUyYy0wLjQyOS0wLjQyOS0wLjQyOS0xLjE0MywwLTEuNTcxTDEwLjI3Myw1LjAwOXoiIGZpbGw9IiMwMDZERjAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==");
        }
        .right {
            float: right;
            cursor: pointer;
            width: 16px;
            height: 16px;
            background-image: url("data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDMxLjQ5IDMxLjQ5IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAzMS40OSAzMS40OTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiPgo8cGF0aCBkPSJNMjEuMjA1LDUuMDA3Yy0wLjQyOS0wLjQ0NC0xLjE0My0wLjQ0NC0xLjU4NywwYy0wLjQyOSwwLjQyOS0wLjQyOSwxLjE0MywwLDEuNTcxbDguMDQ3LDguMDQ3SDEuMTExICBDMC40OTIsMTQuNjI2LDAsMTUuMTE4LDAsMTUuNzM3YzAsMC42MTksMC40OTIsMS4xMjcsMS4xMTEsMS4xMjdoMjYuNTU0bC04LjA0Nyw4LjAzMmMtMC40MjksMC40NDQtMC40MjksMS4xNTksMCwxLjU4NyAgYzAuNDQ0LDAuNDQ0LDEuMTU5LDAuNDQ0LDEuNTg3LDBsOS45NTItOS45NTJjMC40NDQtMC40MjksMC40NDQtMS4xNDMsMC0xLjU3MUwyMS4yMDUsNS4wMDd6IiBmaWxsPSIjMDA2REYwIi8+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=");
        }
    }

    .calendar-root {
        position: relative;
    }

    .calendar-root,
    .calendar-title {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    .calendar-right-to-left {
        margin-left: -460px;
    }

    .calendar-right-to-left-mobile {
        margin-left: 0;
    }

    .calendar {
        display: block;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        width: 700px;
        font-size: 12px;
        min-height: 300px;
        box-shadow: -3px 4px 12px -1px #ccc;
        background: #fff;
        position: absolute;
        z-index: 9;
        ul {
            list-style-type: none;
        }
    }

    .calendar-head h2 {
        padding: 20px 0 0 20px;
        margin: 0;
    }

    .close {
        float: right;
        padding: 0 10px;
        margin-top: -35px;
        font-size: 32px;
        font-weight: normal;
        &:hover {
            cursor: pointer;
        }
    }

    .calendar-wrap {
        display: inline-block;
        float: left;
        width: 75%;
        padding: 10px;
    }

    .calendar-wrap-mobile {
        width: 100%;
    }

    .calendar-range {
        float: left;
        width: 24%;
        padding: 0 12px;
        border-left: 1px solid #ccc;
        margin: -2px;
    }

    .calendar-left-mobile {
        width: 100% !important;
    }

    .calendar_month_left,
    .calendar_month_right {
        float: left;
        width: 43%;
        padding: 10px;
        margin: 5px;
    }

    .calendar_weeks {
        margin: 0;
        padding: 10px 0;
        width: auto;
        li {
            display: inline-block;
            width: 13.6%;
            color: #999;
            text-align: center;
        }
    }

    .calendar_days {
        margin: 0;
        padding: 0;
        li {
            display: inline-block;
            width: 13.6%;
            color: #333;
            text-align: center;
            cursor: pointer;
            line-height: 2em;
            &:hover {
                background: #eee;
                color: #000;
            }
        }

    }

    .calendar_preset li {
        line-height: 2.6em;
        width: auto;
        display: block;
    }

    li.calendar_days--disabled {
        pointer-events: none;
        opacity: 0.4;
    }

    li.calendar_days_selected {
        background: #005a82;
        color: #fff;
    }

    li.calendar_days_in-range {
        background: #0096d9;
        color: #fff;
    }

    .calendar_preset {
        padding: 0;
    }

    .calendar_preset li.calendar_preset-ranges {
        padding: 0 10px 0 10px;
        margin-bottom: 1px;
        cursor: pointer;
        margin-top: 1px;
        &:hover {
            background: #eee;
        }
    }

    .calendar-mobile {
        width: 220px;
        z-index: 1;
        box-shadow: none;
    }

    .calendar-range-mobile {
        width: 100%;
        padding: 2px;
        border-left: none;
        margin: -20px 0;
    }

    .calendar-btn-apply {
        cursor: pointer;
        width: 100%;
        background: #f7931e;
        color: #fff;
        border: none;
        padding: 5px;
        font-size: 14px;
        margin-top: 5px;
    }

    .calendar-btn-clear {
        cursor: pointer;
        width: 100%;
        background: #d6d8e2;
        color: #303030;
        border: none;
        padding: 3px;
        font-size: 12px;
        margin-top: 5px;
        height: 26px;
        line-height: 18px;
    }

    .calendar-btn-show {
        cursor: pointer;
        width: 100%;
        background: #e8eaf5;
        color: #303030;
        border: none;
        padding: 3px;
        font-size: 12px;
        margin-top: 5px;
        height: 26px;
        line-height: 18px;
    }
</style>