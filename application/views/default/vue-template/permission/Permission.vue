<template>
  <v-container
    id="page-permission"
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
      :title="$t('model.permission.permission')"
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

    <permission-form v-model="entity" :open="openNewForm" :processing="isProcessing" @close="openNewForm = false" @save="onSave"/>
    <wee-confirm ref="weeConfirmRef"></wee-confirm>
    <wee-toast ref="weeToastRef"></wee-toast>
  </v-container>
</template>

<script>
import { vLog } from "@/plugins/util";
//service
import PermissionService from "@/api/PermissionService";
import useCrudApi from "@/composition/UseCrudApi";
import { toRefs, onBeforeUnmount} from "@vue/composition-api";
export default {
  name: "page-permission",
  components: {
    WeeConfirm: () => import("@/components/WeeConfirm"),
    WeeToast: () => import("@/components/WeeToast"),
    WeeSimpleTable: () => import("@/components/WeeSimpleTable"),
    PermissionForm: () => import("./PermissionForm"),
  },
  setup(props, { refs, root }) {
    const permissionService = new PermissionService();
    const tableHeaders = [
      {
        column: "id",
        label: "model.permission.id",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "name",
        label: "model.permission.name",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "description",
        label: "model.permission.description",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "crud_table",
        label: "model.permission.crud_table",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "status",
        label: "model.permission.status",
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
      id: '',
      name: '',
      description: '',
      crud_table: '',
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
    } = useCrudApi(refs, root, permissionService, initialItem, tableHeaders);

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
