// mix.js('packages/assurrussa/grid-view-table/resources/js/app.js', 'public/js/amigrid.js');
import FormSerialize from 'form-serialize';
window.FormSerialize = FormSerialize;

import VSelectAjax from './Component/VselectAjax';
import DateRange from './Component/DateRange';
import SelectDateRange from './Component/SelectDateRange';

Vue.component('ami-select-ajax', VSelectAjax);
Vue.component('ami-date-range', DateRange);
Vue.component('ami-select-date-range', SelectDateRange);

import AmiGridJS from './amigrid';

window.AmiGridJS = AmiGridJS;