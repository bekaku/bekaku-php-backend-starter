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
        <v-toolbar-title>{{$t('model.edr_colleage.edr_colleage') +' ('+(!editMode ?  $t('base.addNew') : $t('base.edit'))+')'}} </v-toolbar-title>
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
                    :name="$t('model.edr_colleage.ipaddress')"
                    rules="max:50"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.ipaddress"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.ipaddress')"
                  :label="$t('model.edr_colleage.ipaddress')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.domain')"
                    rules="max:100"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.domain"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.domain')"
                  :label="$t('model.edr_colleage.domain')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.school_id')"
                    rules="max:10"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.school_id"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.school_id')"
                  :label="$t('model.edr_colleage.school_id')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.college_code')"
                    rules="max:25"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.college_code"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.college_code')"
                  :label="$t('model.edr_colleage.college_code')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.name_th')"
                    rules="max:255"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.name_th"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.name_th')"
                  :label="$t('model.edr_colleage.name_th')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.name_en')"
                    rules="max:255"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.name_en"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.name_en')"
                  :label="$t('model.edr_colleage.name_en')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.java_ipaddress')"
                    rules="max:180"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.java_ipaddress"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.java_ipaddress')"
                  :label="$t('model.edr_colleage.java_ipaddress')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.php_ipaddress')"
                    rules="max:180"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.php_ipaddress"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.php_ipaddress')"
                  :label="$t('model.edr_colleage.php_ipaddress')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.sockets_path')"
                    rules="max:180"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.sockets_path"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.sockets_path')"
                  :label="$t('model.edr_colleage.sockets_path')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                <v-switch
                  v-model="entity.use_std_api"
                  :label="entity.use_std_api ? $t('base.enable') : $t('base.disable')"
                ></v-switch>
              </v-col>

              <v-col cols="12">
                <p class='caption'>{{$t('model.edr_colleage.ssl_expire')}}</p>
                <v-date-picker v-model="entity.ssl_expire"></v-date-picker>

              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.storage')"
                    rules="max:11|numeric:"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.storage"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.storage')"
                  :label="$t('model.edr_colleage.storage')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.ram')"
                    rules="max:11|numeric:"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.ram"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.ram')"
                  :label="$t('model.edr_colleage.ram')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    :name="$t('model.edr_colleage.cpu')"
                    rules="max:11|numeric:"
                  >
                <v-text-field
                  prepend-icon="mdi-pencil"
                  v-model="entity.cpu"
                  :error-messages="errors"
                  :placeholder="$t('model.edr_colleage.cpu')"
                  :label="$t('model.edr_colleage.cpu')"
                ></v-text-field>
                  </validation-provider>
              </v-col>

              <v-col cols="12">
                <v-switch
                  v-model="entity.status"
                  :label="entity.status ? $t('base.enable') : $t('base.disable')"
                ></v-switch>
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
