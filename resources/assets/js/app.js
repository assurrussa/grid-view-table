// mix.js('packages/assurrussa/grid-view-table/resources/js/app.js', 'public/js/amigrid.js');
import FormSerialize from 'form-serialize';
window.FormSerialize = FormSerialize;

import VSelectAjax from './Component/VselectAjax';

Vue.component('v-select-ajax', VSelectAjax);

import AmiGridJS from './amigrid';

window.AmiGridJS = AmiGridJS;