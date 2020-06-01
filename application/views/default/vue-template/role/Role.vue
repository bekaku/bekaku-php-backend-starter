<template>
  <v-container
    id="page-role"
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
      :title="$t('model.role.role')"
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

    <role-form v-model="entity" :open="openNewForm" :processing="isProcessing" @close="openNewForm = false" @save="onSave"/>
    <wee-confirm ref="weeConfirmRef"></wee-confirm>
    <wee-toast ref="weeToastRef"></wee-toast>
  </v-container>
</template>

<script>
import { vLog } from "@/plugins/util";
//service
import RoleService from "@/api/RoleService";
import useCrudApi from "@/composition/UseCrudApi";
import { toRefs, onBeforeUnmount} from "@vue/composition-api";
export default {
  name: "page-role",
  components: {
    WeeConfirm: () => import("@/components/WeeConfirm"),
    WeeToast: () => import("@/components/WeeToast"),
    WeeSimpleTable: () => import("@/components/WeeSimpleTable"),
    RoleForm: () => import("./RoleForm"),
  },
  setup(props, { refs, root }) {
    const roleService = new RoleService();
    const tableHeaders = [
      {
        column: "name",
        label: "model.role.name",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "description",
        label: "model.role.description",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "status",
        label: "model.role.status",
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
      name: '',
      description: '',
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
    } = useCrudApi(refs, root, roleService, initialItem, tableHeaders);

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
