<template>
  <v-container
    id="page-user"
    fluid
  >

    <base-wee-sketlon-loader
      :loading="loading"
      type="table-heading, table-thead, table-tbody, table-tfoot"
      :no="1"
    />

    <!-- Table  -->
    <wee-simple-table
      v-if="!loading"
      :headers="fillableHeaders"
      :title="$t('model.user.user')"
      :tr-list="filteredList"
      :pages="pages"
      :sort="sort"
      @update-search="searchTxt = $event"
      @on-item-click="onItemClick"
      @on-item-delete="onBeforeDeleteItem"
      @on-open-new-form="onOpenNewForm"
      @on-advance-search="advanceSearch"
      @on-reload-page="onReload"
    >
      <!-- <template v-slot:theader></template> 
      <template v-slot:tbody></template> 
      <template v-slot:tpaging></template>  -->
    </wee-simple-table>

    <user-form v-model="entity" :open="openNewForm" :processing="isProcessing" @close="openNewForm = false" @save="onSave"/>
    <wee-confirm ref="weeConfirmRef"></wee-confirm>
    <wee-toast ref="weeToastRef"></wee-toast>
  </v-container>
</template>

<script>
import { vLog } from "@/plugins/util";
//service
import UserService from "@/api/UserService";
import useCrudApi from "@/composition/UseCrudApi";
import { toRefs, onBeforeUnmount} from "@vue/composition-api";
export default {
  name: "page-user",
  components: {
    WeeConfirm: () => import("@/components/WeeConfirm"),
    WeeToast: () => import("@/components/WeeToast"),
    WeeSimpleTable: () => import("@/components/WeeSimpleTable"),
    UserForm: () => import("./UserForm"),
  },
  setup(props, { refs, root }) {
    const userService = new UserService();
    const tableHeaders = [
      {
        column: "username",
        label: "model.user.username",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "email",
        label: "model.user.email",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "image",
        label: "model.user.image",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "password",
        label: "model.user.password",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "salt",
        label: "model.user.salt",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "status",
        label: "model.user.status",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: true,
        //linkable: {external: true},
      },
      {
        label: "base.tool",
        fillable: true,
        baseTool: true
      }
    ];

    //entity
    const initialItem = {
      username: '',
      email: '',
      image: '',
      password: '',
      salt: '',
      status: false,
    };

    const {
      state,
      sort,
      pages,
      filteredList,
      entity,
      isProcessing,
      searchTxt,
      fillableHeaders,
      //method
      fetchData,
      openNewForm,
      onItemClick,
      onOpenNewForm,
      onBeforeDeleteItem,
      onSave,
      advanceSearch,
      onReload
    } = useCrudApi(refs, root, userService, initialItem, tableHeaders);

    //fell free to change sort colunm and mode
    //sort.column = "id";
    //sort.mode = "ASC";

    onBeforeUnmount(()=>{
      vLog('onBeforeUnmount')
    });

    return {
      ...toRefs(state),
      sort,
      pages,
      filteredList,
      entity,
      isProcessing,
      searchTxt,
      fillableHeaders,
      //method
      fetchData,
      openNewForm,
      onItemClick,
      onOpenNewForm,
      onBeforeDeleteItem,
      onSave,
      advanceSearch,
      onReload
    };
  }
};
</script>
