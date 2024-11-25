import FrontendGrid from './fieldtypes/FrontendGrid.vue';
//import VariableNumberIndex from './fieldtypes/VariableNumberIndex.vue';

Statamic.booting(() => {
    Statamic.$components.register('frontend_grid-fieldtype', FrontendGrid);
  //  Statamic.$components.register('variable_number-fieldtype-index', VariableNumberIndex);
});
