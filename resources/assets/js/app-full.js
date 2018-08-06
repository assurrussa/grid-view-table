// mix.js('packages/assurrussa/grid-view-table/resources/js/app-full.js', 'public/js/amigrid-full.js');
import './../../../../../../node_modules/historyjs/scripts/bundled-uncompressed/html4+html5/native.history.js'; // npm i historyjs --save-dev
import './bootstrap';

import FormSerialize from 'form-serialize';

window.FormSerialize = FormSerialize;

import VSelectAjax from './Component/VselectAjax';

Vue.component('v-select-ajax', VSelectAjax);

import './amigrid';