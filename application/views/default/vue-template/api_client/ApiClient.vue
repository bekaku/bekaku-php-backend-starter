<template>
  <v-container
    id="page-apiClient"
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
      :title="$t('model.api_client.api_client')"
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

    <api-client-form v-model="entity" :open="openNewForm" :processing="isProcessing" @close="openNewForm = false" @save="onSave"/>
    <wee-confirm ref="weeConfirmRef"></wee-confirm>
    <wee-toast ref="weeToastRef"></wee-toast>
  </v-container>
</template>

<script>
import { vLog } from "@/plugins/util";
//service
import ApiClientService from "@/api/ApiClientService";
import useCrudApi from "@/composition/UseCrudApi";
import { toRefs, onBeforeUnmount} from "@vue/composition-api";
export default {
  name: "page-apiClient",
  components: {
    WeeConfirm: () => import("@/components/WeeConfirm"),
    WeeToast: () => import("@/components/WeeToast"),
    WeeSimpleTable: () => import("@/components/WeeSimpleTable"),
    ApiClientForm: () => import("./ApiClientForm"),
  },
  setup(props, { refs, root }) {
    const apiClientService = new ApiClientService();
    const tableHeaders = [
      {
        column: "api_name",
        label: "model.api_client.api_name",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "api_token",
        label: "model.api_client.api_token",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: false,
        //linkable: {external: true},
      },
      {
        column: "by_pass",
        label: "model.api_client.by_pass",
        searchable: true,
        sortable: true,
        fillable: true,
        image: false,
        status: true,
        //linkable: {external: true},
      },
      {
        column: "status",
        label: "model.api_client.status",
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
      api_name: '',
      api_token: '',
      by_pass: false,
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
    } = useCrudApi(refs, root, apiClientService, initialItem, tableHeaders);

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
