<template>
    <!--      Start  Form -->
    <v-dialog
     v-if="entity"
      v-model="open"
      fullscreen
      hide-overlay
      transition="dialog-bottom-transition"
      persistent
      scrollable
    >
      <v-card>
        <v-toolbar
          flat
        >
          <v-btn
            icon
            :loading="processing"
            :disabled="processing"
            @click="close"
          >
            <v-icon>mdi-keyboard-backspace</v-icon>
          </v-btn>
          <v-toolbar-title>{{$t('model.edr_std_mapper.edr_std_mapper')}}</v-toolbar-title>
          <v-spacer></v-spacer>
        </v-toolbar>
        <v-card-text>
              <validation-observer
                ref="form"
                v-slot="{ handleSubmit, reset }"
              >
                <form
                  @submit.prevent="handleSubmit(onSave)"
                  @reset.prevent="reset"
                >
          <v-container>
            <v-row>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_std_mapper.function_name')"
                    rules="required|max:255"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.function_name"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_std_mapper.function_name')"
                  :label="$t('model.edr_std_mapper.function_name')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_std_mapper.std')"
                    rules="max:255"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.std"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_std_mapper.std')"
                  :label="$t('model.edr_std_mapper.std')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_std_mapper.edr')"
                    rules="max:255"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.edr"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_std_mapper.edr')"
                  :label="$t('model.edr_std_mapper.edr')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

                <v-col cols="12" class="mt-6" align="center">
                  <v-btn
                    text
                    @click="close"
                    :disabled="processing"
                  >
                    {{ $t("base.cancel") }}
                  </v-btn>
                  <v-btn
                    type="submit"
                    text
                    color="primary"
                    :disabled="processing"
                  >
                    <v-icon>mdi-lead-pencil</v-icon> {{ $t("base.save") }}
                  </v-btn>
                </v-col>

                        </v-row>
                    </v-container>
                </form>
              </validation-observer>
        </v-card-text>
      </v-card>
    </v-dialog>
</template>
<script>
import { defineComponent, reactive } from "@vue/composition-api";
export default defineComponent({
  props: {
    value: null,
    open: {
      type: Boolean,
      default: false
    },
    processing: {
      type: Boolean,
      default: false
    }
  },
  setup(props, { emit }) {
    const entity = reactive(props.value);
    const close = () => {
      emit("close");
    };
    const onSave = () => {
      emit("save", entity);
    };
    return {
      entity,
      close,
      onSave
    };
  }
});
</script>
