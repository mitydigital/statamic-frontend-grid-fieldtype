<template>
    <div class="feg-space-y-4">
        <div v-for="(set, index) in value._sets"
             :key="'set_'+index">
            <div class="-feg-mx-4 sm:-feg-mx-6 ">
            <button class="feg-w-full feg-justify-between feg-font-medium feg-text-sm feg-flex feg-items-center feg-px-4 sm:feg-px-6 feg-py-3
                           feg-border-t feg-border-b border-gray-300
                           bg-gray-300 hover:bg-gray-200
                           dark:bg-dark-650 dark:border-dark-900 dark:hover:bg-dark-700"
                    @click.prevent="toggle(index)">
                <span>
                    {{ config.row_heading }} {{ index + 1}}
                </span>
                <span>
                    <svg-icon name="micro/chevron-left" class="w-3 h-3 feg-transition-all -rotate-90"
                        :class="{ 'rotate-90' : hide[index] }"/>
                </span>
            </button>
            </div>

            <div v-if="!hide[index]" class="-feg-mx-4 sm:-feg-mx-6">
                <div class="publish-fields @container">
                    <publish-field
                        v-for="field in config.fields"
                        :key="field.handle"
                        :config="field"
                        :value="value[index+'_'+field.handle]"
                        :read-only="true"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    mixins: [Fieldtype],

    inject: ['storeName'],


    computed: {
        state() {
            return this.$store.state.publish[this.storeName];
        },

        values() {
            return this.state.values;
        },

        message() {
            if (typeof this.value === 'object' && this.value.message) {
                return this.value.message;
            }
            return false;
        },

        fieldConfig() {
            let config = [];

            for (let i = 0; i < this.value._sets; i++) {
                let thisConfig = [];
                this.config.fields.forEach((field) => {
                    thisConfig.push(Object.assign({}, field, {
                        handle: i+'_'+field.handle
                    }));
                });
                config.push(thisConfig);
            }

            return config;
        }
    },

    data: function () {
        return {
            hide: {},
            widths: {
                100: 'sm:feg-col-span-12',
                75: 'sm:feg-col-span-9',
                66: 'sm:feg-col-span-8',
                50: 'sm:feg-col-span-6',
                33: 'sm:feg-col-span-4',
                25: 'sm:feg-col-span-3',
            }
        }
    },

    methods: {
        toggle(index) {
            if (!this.hide.hasOwnProperty(index)) {
                Vue.set(this.hide, index, true);
            }
            else {
                Vue.set(this.hide, index, !this.hide[index]);
            }
        }
    }
}
</script>
