// mix.js('packages/assurrussa/grid-view-table/resources/js/app-full.js', 'public/js/amigrid-full.js');
import './../../../../../../node_modules/historyjs/scripts/bundled-uncompressed/html4+html5/native.history.js'; // npm i historyjs --save-dev
import './bootstrap';

import FormSerialize from 'form-serialize';
window.FormSerialize = FormSerialize;

import Datepicker from './Component/Datepicker';
import VSelectAjax from './Component/VselectAjax';
import DateRange from './Component/DateRange';
import SelectDateRange from './Component/SelectDateRange';

Vue.component('ami-select-ajax', VSelectAjax);
Vue.component('ami-datepicker', Datepicker);
Vue.component('ami-date-range', DateRange);
Vue.component('ami-select-date-range', SelectDateRange);

import './amigrid';