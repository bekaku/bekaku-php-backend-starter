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
          <v-toolbar-title>{{$t('model.role.role')}}</v-toolbar-title>
          <v-spacer></v-spacer>
            <v-btn
              text
              @click="close"
              :disabled="processing"
            > {{$t('base.cancel') }}
            </v-btn>
            <v-btn
              text
              @click="onSave"
              :disabled="processing"
            ><v-icon>mdi-lead-pencil</v-icon> {{$t('base.save') }}
            </v-btn>
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
                    :name="$t('model.role.name')"
                    rules="required|max:100"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.name"
                  :error-messages="errors"
                  :placeholder="$t('model.role.name')"
                  :label="$t('model.role.name')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  clearable
                  prepend-icon="mdi-pencil"
                  v-model="entity.description"
                  :placeholder="$t('model.role.description')"
                  :label="$t('model.role.description')"
                ></v-textarea>
              </v-col>

              <v-col cols="12">
                <v-switch
                  v-model="entity.status"
                  :label="entity.status ? $t('base.enable') : $t('base.disable')"
                ></v-switch>
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
