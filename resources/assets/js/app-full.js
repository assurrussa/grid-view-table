// mix.js('packages/assurrussa/grid-view-table/resources/js/app-full.js', 'public/js/amigrid-full.js');
import './bootstrap';

import FormSerialize from 'form-serialize';
window.FormSerialize = FormSerialize;

import VSelectAjax from './Component/VselectAjax';

Vue.component('v-select-ajax', VSelectAjax);

import AmiGridJS from './amigrid';

window.AmiGridJS = AmiGridJS;